<?php

namespace App\Traits\Enum;

trait StatusEnumTrait
{
    public function label(): string 
    {
        return static::getLabel($this);
    }

    public function class(): string 
    {
        return static::getClass($this);
    }

    public function color() : string
    {
        return static::getColor(($this));
    }

    public function icon() : string
    {
        return static::getIcon($this);
    }
    
    public function calColor() : string
    {
        return static::getCalendarColor(($this));
    }

    public static function getArray($type = '')
    {
        if($type == 'withAll') {
            $statuses[''] = 'All';
        } else {
            $statuses = [];
        }

        foreach(self::cases() as $status) {
            $statuses[$status->value] = $status->label();
        }
        return $statuses;
    }

    public static function getDropdownArray()
    {
        $statuses = [];

        foreach(self::cases() as $status) {
            $statuses[] = ['name' => $status->label(), 'value' => $status->value];
        }

        return $statuses;
    }
}