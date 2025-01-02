<?php

namespace App\Enums\Scheduler;

use App\Traits\Enum\StatusEnumTrait;

enum ScheduleEnum: string
{
    case at_home_maintenance =  'At Home Maintenance';
    case delivery = 'Delivery';
    case pickup = 'Pickup';
    case plow_installation = 'Plow Installation';

    public function label(): string
    {
        return static::getLabel($this);
    }

    public static function getLabel(self $value): string
    {
        return match ($value) {
            self::at_home_maintenance => 'At Home Maintenance',
            self::delivery => 'Delivery',
            self::pickup => 'Pickup',
            self::plow_installation => 'Plow Installation',
            default => '-'
        };
    }
}
