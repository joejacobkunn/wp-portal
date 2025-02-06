<?php

namespace App\Console\Commands;

use App\Contracts\DistanceInterface;
use App\Models\Scheduler\Schedule;
use App\Models\Scheduler\TruckSchedule;
use Carbon\Carbon;
use Illuminate\Console\Command;

class RouteFinder extends Command
{
    public $scheduleId;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:route-finder {--schedule=}';

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
            $query = $query->where('schedule_date', Carbon::now()->format('Y-m-d'));
        }

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
        // Get first schedule for truck and date info
        $firstSchedule = $schedules->first();
        $this->info(sprintf('Processing schedules for Truck ID: %d, Date: %s',
            $firstSchedule->truck_id,
            $firstSchedule->schedule_date
        ));

        // Collect all confirmed orders from all schedules
        $allConfirmedOrders = collect();
        foreach ($schedules as $schedule) {
            $confirmedOrders = $schedule->orderSchedule()
                ->where('status', 'Confirmed')
                ->get();
            $allConfirmedOrders = $allConfirmedOrders->concat($confirmedOrders);
        }

        if ($allConfirmedOrders->isEmpty()) {
            $this->warn(sprintf('No confirmed schedules found for Truck ID: %d', $firstSchedule->truck_id));
            return;
        }

        // Get warehouse address and all service addresses
        $warehouse = $firstSchedule->truck->warehouse;
        $dataInput = [$warehouse->address];
        $destinations = $allConfirmedOrders->pluck('service_address')->toArray();
        $dataInput = array_merge($dataInput, $destinations);

        // Remove duplicates if any
        $dataInput = array_unique($dataInput);

        $response = $this->getDistance($dataInput);
        if(isset($response['error'])) {
            $this->info(sprintf($response['message']));
            return 0;
        }
        $this->updateSchedulePriorities($schedules, $allConfirmedOrders, $response, $dataInput);

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
        // Duration comes in seconds from Google API, convert to minutes
        return ceil($durationMatrix[$currentIndex][$nextIndex] / 60);
    }


    private function updateSchedulePriorities($schedules, $allConfirmedOrders, $routeData, $addresses)
    {
        $optimalRoute = $routeData['optimal_route'];
        $durations = $routeData['durations'];
        $firstSchedule = $schedules->first();
        $expectedTime = Carbon::parse($firstSchedule->schedule_date . ' ' . '08:45 AM');

        $orderMap = $allConfirmedOrders->keyBy('service_address');

        $priority = 1;

        foreach (array_slice($optimalRoute, 1) as $address) {
            if (isset($orderMap[$address])) {
                $order = $orderMap[$address];

                // Calculate expected arrival time
                $travelTime = $this->calculateTravelTime(
                    array_search($address, $optimalRoute) - 1,
                    array_search($address, $optimalRoute),
                    $durations
                );
                $expectedTime->addMinutes($travelTime);
                $expectedTime->addMinutes(20); // delay time added
                $order->update([
                    'travel_prio_number' => $priority,
                    'expected_arrival_time' => $expectedTime->format('H:i:s')
                ]);

                $this->info("Updated Schedule ID: {$order->id} with priority: {$priority} and expected time: {$expectedTime->format('H:i:s')}");
                $priority++;
            }
        }
    }
}
