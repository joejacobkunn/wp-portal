<?php

namespace App\Enums\Scheduler;

use App\Traits\Enum\StatusEnumTrait;

enum ScheduleStatusEnum: string
{
    case Scheduled =  'Scheduled';
    case Confirmed = 'Confirmed';
    case Completed = 'Completed';
    case Cancelled = 'Cancelled';

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
            self::Scheduled => 'Scheduled',
            self::Confirmed => 'Confirmed',
            self::Completed => 'Completed',
            self::Cancelled => 'Cancelled',
            default => '-'
        };
    }

    public static function getColor(self $value): string
    {
        return match ($value) {
            self::Scheduled => 'gray',
            self::Confirmed => 'blue',
            self::Completed => 'green',
            self::Cancelled => 'red',
            default => '-'
        };
    }

    public static function getColorClass(self $value): string
    {
        return match ($value) {
            self::Scheduled => 'secondary',
            self::Confirmed => 'primary',
            self::Completed => 'success',
            self::Cancelled => 'danger',
            default => '-'
        };
    }

}
