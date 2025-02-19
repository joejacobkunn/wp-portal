<?php

namespace App\Enums\Scheduler;

use App\Traits\Enum\StatusEnumTrait;

enum ScheduleStatusEnum: string
{
    case scheduled =  'scheduled';
    case scheduled_linked =  'scheduled_linked';
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
            self::scheduled_linked => 'SRO Attached',
            self::confirmed => 'Parts are Ready',
            self::completed => 'Completed',
            self::cancelled => 'Cancelled',
            self::out_for_delivery => 'Tech in Progress',
            default => '-'
        };
    }

    public static function getColor(self $value): string
    {
        return match ($value) {
            self::scheduled => '#a5aaae',
            self::scheduled_linked => '#9E2EC9',
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
            self::scheduled_linked => 'linked',
            self::confirmed => 'primary',
            self::completed => 'success',
            self::cancelled => 'danger',
            self::out_for_delivery => 'info',
            default => '-'
        };
    }

}
