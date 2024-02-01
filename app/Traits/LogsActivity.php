<?php

namespace App\Traits;

use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity as SpatieLogsActivity;


trait LogsActivity
{
    use SpatieLogsActivity;

    public static $DEFAULT_RESOLVERS = [
        'boolean' => 'resolveBooleanChange'
    ];

    public $activityCache = [];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnlyDirty()
            ->logOnly($this->fillable);
    }
    
    public function resolveLogField($field, $value)
    {
        if (!empty(static::LOG_FIELD_MAPS[$field]['resolve'])) {
            $resolverFn = static::LOG_FIELD_MAPS[$field]['resolve'];     
            
            return $this->{$resolverFn}($value);
        }
    }

    public function resolveBooleanChange($value)
    {
        return $value ? "Yes" : "No";
    }

    /**
     * Load data from cache/last query result
     */
    public function fromActivityCache($value)
    {
        $function = debug_backtrace()[1]['function'];
        
        if (empty($this->activityCache[$function][$value])) {
            $this->activityCache[$function][$value] = $this->{$function}($value, false);
        }

        return $this->activityCache[$function][$value];
    }

    /**
     * Record custom activity info
     */
    public function recordCustomEvent($desc, $properties = [], $metaData = [])
    {
        $activity = activity()
            ->performedOn($this);

        if (!empty($metaData['created_at'])) {
            $activity->createdAt(new \DateTime($metaData['created_at']));
        }

        if (!empty($metaData['caused_by'])) {
            $activity->by($metaData['caused_by']);
        }

        if (!empty($metaData['is_imported'])) {
            $activity->tap(function(\Spatie\Activitylog\Contracts\Activity $activity) {
                $activity->is_imported = 1;
            });
        }
        
        $activity->event('custom')
            ->withProperties($properties)
            ->log($desc);
    }
}
