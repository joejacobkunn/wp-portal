<?php

namespace App\Enums\Scheduler;

use App\Traits\Enum\StatusEnumTrait;

enum ScheduleEnum: string
{
    case at_home_maintenance =  'At Home Maintenance';
    case delivery = 'Delivery';
    case pickup = 'Pickup';
    case setup_install = 'Setup/Install';

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
            self::delivery => 'Delivery',
            self::pickup => 'Pickup',
            self::setup_install => 'Setup/Install',
            default => '-'
        };
    }

    public static function getIcon(self $value): string
    {
        return match ($value) {
            self::at_home_maintenance => '<i class="fas fa-house-damage"></i>',
            self::delivery => '<i class="fas fa-shipping-fast"></i>',
            self::pickup => '<i class="fas fa-truck-loading"></i>',
            self::setup_install => '<i class="fas fa-tools"></i>',
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
