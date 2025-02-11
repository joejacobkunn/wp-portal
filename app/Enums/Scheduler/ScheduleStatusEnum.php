<?php

namespace App\Enums\Scheduler;

use App\Traits\Enum\StatusEnumTrait;

enum ScheduleStatusEnum: string
{
    case scheduled =  'scheduled';
    case confirmed = 'confirmed';
    case completed = 'completed';
    case cancelled = 'cancelled';
    case out_for_delivery = 'out_for_delivery';

    public function label(): string
    {
        return static::getLabel($this);
    }

    public function color(): string
    {
        return static::getColor($this);
    }
    public function colorClass(): string
    {
        return static::getColorClass($this);
    }

    public static function getLabel(self $value): string
    {
        return match ($value) {
            self::scheduled => 'Scheduled',
            self::confirmed => 'Confirmed',
            self::completed => 'Completed',
            self::cancelled => 'Cancelled',
            self::out_for_delivery => 'Out for Delivery',
            default => '-'
        };
    }

    public static function getColor(self $value): string
    {
        return match ($value) {
            self::scheduled => '#a5aaae',
            self::confirmed => '#435cbe',
            self::completed => '#43aa48',
            self::cancelled => '#bb2d3b',
            self::out_for_delivery => 'cyan',
            default => '-'
        };

    }

    public static function getColorClass(self $value): string
    {
        return match ($value) {
            self::scheduled => 'secondary',
            self::confirmed => 'primary',
            self::completed => 'success',
            self::cancelled => 'danger',
            self::out_for_delivery => 'info',
            default => '-'
        };
    }

}
