<?php

namespace App\Console\Commands;

use App\Contracts\DistanceInterface;
use App\Models\Scheduler\Schedule;
use App\Models\Scheduler\TruckSchedule;
use App\Models\Scheduler\TruckScheduleReturn;
use Carbon\Carbon;
use Illuminate\Console\Command;

class RouteFinder extends Command
{
    public $scheduleId;
    public $schedule_date;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:route-finder {--schedule=} {--schedule_date=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'find the best route for schedules and re-arrange schedules';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->scheduleId = $this->option('schedule');
        $this->schedule_date = $this->option('schedule_date');

        if(!$this->schedule_date) {
            $this->schedule_date = Carbon::now()->format('Y-m-d');
        }

        $this->info('Starting process...');
        try {
            $result = $this->processData();
            $this->info('Process completed successfully!');

        } catch (\Exception $e) {

            $this->error('An error occurred: ' . $e->getMessage());
            return 1;
        }
        return 0;
    }

    private function processData()
    {
        $query = TruckSchedule::with('orderSchedule', 'truck.warehouse');

        if($this->scheduleId) {
            $query = $query->where('id', $this->scheduleId);
        } else {
            $query = $query->where('schedule_date', $this->schedule_date);
        }

        $query = $query->orderByRaw('STR_TO_DATE(start_time, "%h:%i %p") asc');


        // Group truck schedules by truck_id and schedule_date
        $groupedSchedules = $query->get()->groupBy(function($schedule) {
            return $schedule->truck_id . '_' . $schedule->schedule_date;
        });

        if ($groupedSchedules->isEmpty()) {
            $this->info('No schedules found.');
            return 0;
        }

        foreach ($groupedSchedules as $schedules) {
            $this->processCombinedSchedules($schedules);
        }

        return 0;
    }

    private function processCombinedSchedules($schedules)
    {
        $firstSchedule = $schedules->first();
        $this->info(sprintf('Processing schedules for Truck ID: %d, Date: %s',
            $firstSchedule->truck_id,
            $firstSchedule->schedule_date
        ));
        if(!$firstSchedule->truck) {
            $this->warn('truck info is missing');
            return;
        }
        $lastAddress = $firstSchedule->truck->warehouse->address;
        $currentTime = null;
        $lastExpectedTime =null;
        foreach ($schedules as $key => $schedule) {
            $confirmedOrders = $schedule->orderSchedule()
                ->where('status', 'confirmed')
                ->orderBy('id')
                ->get();

            if($currentTime == null) {
                $currentTime = Carbon::parse($schedule->schedule_date . ' ' . $schedule->start_time);
                $currentTime = $currentTime->subMinutes(15);
            }
            if ($confirmedOrders->isEmpty()) {
                $this->warn(sprintf('No confirmed orders found for Schedule ID: %d', $schedule->id));
                $currentTime = null;
                continue;
            }

            // Group orders by address while maintaining order
            $addressToOrders = [];
            foreach ($confirmedOrders as $order) {
                if (!isset($addressToOrders[$order->service_address])) {
                    $addressToOrders[$order->service_address] = [];
                }
                $addressToOrders[$order->service_address][] = $order;
            }

            // Prepare input data starting from last known address
            $dataInput = [$lastAddress];
            $destinations = array_keys($addressToOrders);
            $dataInput = array_merge($dataInput, $destinations);

            $response = $this->getDistance($dataInput);
            if(isset($response['error'])) {
                $this->info(sprintf($response['message']));
                continue;
            }

            // Update the schedule with optimized route
            $responseData = $this->updateSchedulePriorities(
                $response,
                $currentTime,
                $addressToOrders
            );
            $lastExpectedTime = $responseData['expectedTime'];
            $optimalRoute = $response['optimal_route'];
            $lastAddress = end($optimalRoute);
            //add break 1 hour
            if (count($optimalRoute) > 1 && $key < count($schedules) - 1) {
                $nextSchedule = $schedules[$key + 1];
                $nextScheduleHasConfirmedOrders = $nextSchedule->orderSchedule()
                    ->where('status', 'confirmed')
                    ->exists();

                if ($nextScheduleHasConfirmedOrders) {
                    $currentTime = $lastExpectedTime->addHour();
                }
            }
        }

        if(!$lastExpectedTime) {
            return;
        }
        $returnResponse = $this->getDistance([$lastAddress, $firstSchedule->truck->warehouse->address]);
        if (!isset($returnResponse['error'])) {
            $this->saveWarehouseReturn(
                $returnResponse,
                $firstSchedule->truck,
                $lastExpectedTime,
                $lastAddress,
                $responseData['scheduleId']
            );
        }

    }

    private function saveWarehouseReturn($returnResponse, $truck, $lastExpectedTime, $lastAddress, $scheduleId)
    {
        $distanceToWarehouse = $returnResponse['distances'][0][1];
        $durationToWarehouse = ceil($returnResponse['durations'][0][1] / 60);
        $distanceInMiles = round($distanceToWarehouse / 1609.344, 2);

        $expectedArrival = clone $lastExpectedTime;
        $expectedArrival->addMinutes($durationToWarehouse);

        TruckScheduleReturn::updateOrCreate(
            [
                'truck_id' => $truck->id,
                'schedule_date' => $this->schedule_date,
            ],
            [
                'whse' => $truck->warehouse_short,
                'expected_arrival_time' => $expectedArrival->format('H:i:s'),
                'last_scheduled_address' => $lastAddress,
                'distance' => $distanceInMiles,
                'schedule_id' => $scheduleId,
            ]
        );
    }
    private function getDistance($dataInput)
    {
        $google = app(DistanceInterface::class);

        $response = $google->findDistance(implode("|", $dataInput), implode("|", $dataInput));
        if (!isset($response['status']) || $response['status'] !== 'OK') {
            return [
                'error' => true,
                'message' => 'Google API Error: ' . ($response['error_message'] ?? 'Unknown error'),
                'response' => $response
            ];
        }

        if (isset($response['rows']) && count($response['rows']) > 0) {
            $distanceMatrix = [];
            $durationMatrix = [];

            foreach ($response['rows'] as $row) {
                $distances = array_map(fn($element) => $element['distance']['value'], $row['elements']);
                $durations = array_map(fn($element) => $element['duration']['value'], $row['elements']);
                $distanceMatrix[] = $distances;
                $durationMatrix[] = $durations;
            }

            $optimalRouteIndexes = $this->solveTSP($distanceMatrix);
            $optimalRoute = array_map(fn($index) => $dataInput[$index], $optimalRouteIndexes);

            return [
                'starting_point' => $dataInput[0],
                'optimal_route' => $optimalRoute,
                'distances' => $distanceMatrix,
                'durations' => $durationMatrix
            ];
        }
    }

    private function solveTSP($distanceMatrix)
    {
        $numLocations = count($distanceMatrix);
        $visited = array_fill(0, $numLocations, false);
        $route = [0]; // Always start from index 0 (first address)
        $visited[0] = true;

        for ($i = 1; $i < $numLocations; $i++) {
            $last = $route[count($route) - 1];
            $minDistance = PHP_INT_MAX;
            $nextLocation = -1;

            for ($j = 1; $j < $numLocations; $j++) { // Start from 1 to avoid reselecting 0
                if (!$visited[$j] && $distanceMatrix[$last][$j] < $minDistance) {
                    $minDistance = $distanceMatrix[$last][$j];
                    $nextLocation = $j;
                }
            }

            if ($nextLocation !== -1) {
                $visited[$nextLocation] = true;
                $route[] = $nextLocation;
            }
        }

        return $route;
    }

    private function calculateTravelTime($currentIndex, $nextIndex, $durationMatrix)
    {
        if ($currentIndex < 0 || $nextIndex < 0 ||
            $currentIndex >= count($durationMatrix) ||
            $nextIndex >= count($durationMatrix)) {
            $this->warn("Invalid indices for travel time calculation: {$currentIndex} to {$nextIndex}");
            return 0;
        }

        return ceil($durationMatrix[$currentIndex][$nextIndex] / 60);
    }


    private function updateSchedulePriorities($routeData, $startTime, $addressToOrders)
    {
        $optimalRoute = $routeData['optimal_route'];
        $durations = $routeData['durations'];
        $expectedTime = clone $startTime;
        $priority = 1;
        $previousAddress = $optimalRoute[0];
        foreach (array_slice($optimalRoute, 1) as $address) {
            if (isset($addressToOrders[$address])) {
                $isFirstOrderAtAddress = true;
                $lastScheduleID = null;
                foreach ($addressToOrders[$address] as $schedule) {
                    if ($isFirstOrderAtAddress) {
                        // Calculate travel time only for the first schedule at this address
                        $currentIndex = array_search($address, $optimalRoute);
                        $previousIndex = array_search($previousAddress, $optimalRoute);

                        $travelTime = $this->calculateTravelTime(
                            $previousIndex,
                            $currentIndex,
                            $durations
                        );
                        $expectedTime->addMinutes($travelTime);
                        $isFirstOrderAtAddress = false;
                    }

                    $schedule->update([
                        'travel_prio_number' => $priority,
                        'expected_arrival_time' => $expectedTime->format('H:i:s')
                    ]);

                    $this->info(sprintf(
                        "Updated Truck Schedule ID: %d with priority: %d and expected time: %s",
                        $schedule->id,
                        $priority,
                        $expectedTime->format('H:i:s')
                    ));

                    $expectedTime->addHour(); // Add service time for each order
                    $priority++;
                }
                $previousAddress = $address;
                $lastScheduleID = $schedule->id;
            }
        }
        return ['expectedTime' => $expectedTime, 'scheduleId' => $lastScheduleID];
    }
}
