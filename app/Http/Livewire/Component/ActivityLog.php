<?php

namespace App\Http\Livewire\Component;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Component;

class ActivityLog extends Component
{
    /*
    |--------------------------------------------------------------------------
    | Configurable Attributes 
    |--------------------------------------------------------------------------
    */

    /**
     * Title of card
     */
    public $title = "Activity";

    /**
     * Configured entity (Eloquent Model)
     */
    public $entity;

    /**
     * Record type to specify in activity description
     * 
     * Eg: If the activity info is about role creation/updation, the record type will be "role".
     */
    public $recordType = 'record';

    /**
     * Load by default
     */
    public $defaultLoad = true;

    /**
     * Default load limit
     */
    public $perPage = 5;

    public $deferLoad = false;

    /*
    |--------------------------------------------------------------------------
    | Non-Configurable Attributes 
    |--------------------------------------------------------------------------
    */

    /**
     * Formatted logs
     */
    public $logs = [];

    /**
     * Pagination for records
     */
    public $nextPage = 1;


    public function render()
    {
        return view('livewire.component.activity-log');
    }

    public function mount()
    {
        if (!$this->deferLoad) {
            $this->loadLogs();
        }
    }

    public function loadLogs()
    {
        $logs = $this->entity->activities()->latest()->paginate($this->perPage, ['*'], 'page', $this->nextPage);
        $this->logs = array_merge($this->logs, $this->getFormattedLogs($logs->items()));
        $this->defaultLoad = true;

        if ($logs->hasMorePages()) {
            $this->nextPage += 1;
        } else {
            $this->nextPage = null;
        }
    }

    public function getFormattedLogs($logs)
    {
        $formattedLogs = [];
        $causersList = [
            auth()->user()->id => "You"
        ];

        $yesterday = Carbon::now()->subDay(1)->timestamp;
        foreach ($logs as $log) {
            $formattedLog = [
                'event' => $log->event
            ];

            if (!isset($causersList[$log->causer_id]) && $log->causer_id) {
                $causersList[$log->causer_id] = $log->causer()->select('user_id', 'name_full')->first()->name;
            }

            if ($log->event == 'created') {
                $formattedLog['title'] = $causersList[$log->causer_id] ?? "" . " Created the " . $this->recordType;
                $formattedLog['icon'] = 'fas fa-plus-circle';
            } elseif ($log->event == 'updated') {
                $formattedLog['title'] = $causersList[$log->causer_id] ?? "" . " Updated the " . $this->recordType;
                $formattedLog['icon'] = 'fas fa-user-edit';
                $updatedFields = $log->changes();

                //check if old index exists ie updated or created
                if (array_key_exists('old', $updatedFields->toArray())) {
                    foreach ($updatedFields['old'] as $fieldName => $oldValue) {
                        if (isset($this->entity::LOG_FIELD_MAPS[$fieldName])) {
                            $formattedLog['changes'][$fieldName] = [
                                'label' => $this->entity::LOG_FIELD_MAPS[$fieldName]['field_label'] ?? $fieldName,
                                'old_value' => $oldValue,
                                'new_value' => $updatedFields['attributes'][$fieldName],
                            ];

                            if (!empty($this->entity::LOG_FIELD_MAPS[$fieldName]['resolve'])) {
                                $formattedLog['changes'][$fieldName]['old_value'] = $this->entity->resolveLogField($fieldName, $oldValue);
                                $formattedLog['changes'][$fieldName]['new_value'] = $this->entity->resolveLogField($fieldName, $updatedFields['attributes'][$fieldName]);
                            }
                        }
                    }
                }
            } elseif ($log->event == 'custom') {
                $formattedLog['title'] = ($log->causer_id ? $causersList[$log->causer_id] : "");
                $formattedLog['description'] = $log->description ?? '';
                $formattedLog['icon'] = $log->properties['icon'] ?? "fas fa-exclamation-circle";

                $updatedFields = $log->changes();
                //check if old index exists ie updated or created
                if (array_key_exists('old', $updatedFields->toArray())) {
                    foreach ($updatedFields['old'] as $fieldName => $oldValue) {
                        if (isset($this->entity::LOG_FIELD_MAPS[$fieldName])) {
                            $formattedLog['changes'][$fieldName] = [
                                'label' => $this->entity::LOG_FIELD_MAPS[$fieldName]['field_label'] ?? $fieldName,
                                'old_value' => $oldValue,
                                'new_value' => $updatedFields['attributes'][$fieldName],
                            ];

                            if (!empty($this->entity::LOG_FIELD_MAPS[$fieldName]['resolve'])) {
                                $formattedLog['changes'][$fieldName]['old_value'] = $this->entity->resolveLogField($fieldName, $oldValue);
                                $formattedLog['changes'][$fieldName]['new_value'] = $this->entity->resolveLogField($fieldName, $updatedFields['attributes'][$fieldName]);
                            }
                        }
                    }
                }
            }

            $formattedLog['timestamp_string'] = $log->created_at->timestamp > $yesterday ? $log->created_at->diffForHumans() : $log->created_at->format(config('app.default_datetime_format'));
            $formattedLog['timestamp'] = $log->created_at;
            $formattedLogs[] = $formattedLog;
        }

        return $formattedLogs;
    }
}
