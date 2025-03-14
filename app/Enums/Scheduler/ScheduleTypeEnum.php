<?php

namespace App\Enums\Scheduler;

use App\Traits\Enum\StatusEnumTrait;

enum ScheduleTypeEnum: string
{
    case at_home_maintenance =  'at_home_maintenance';
    case pickup_delivery = 'pickup_delivery';

    public function label(): string
    {
        return static::getLabel($this);
    }

    public function icon(): string
    {
        return static::getIcon($this);
    }

    public static function getLabel(self $value): string
    {
        return match ($value) {
            self::at_home_maintenance => 'At Home Maintenance',
            self::pickup_delivery => 'Pickup / Delivery',
            default => '-'
        };
    }

    public static function getIcon(self $value): string
    {
        return match ($value) {
            self::at_home_maintenance => '<i class="fas fa-house-damage"></i>',
            self::pickup_delivery => '<i class="fas fa-truck-loading"></i>',
            default => '-'
        };

    }

    public static function getArray($type = '')
    {
        $statuses = [];

        foreach(self::cases() as $status) {
            $statuses[$status->value] = $status->label();
        }
        return $statuses;
    }
}
