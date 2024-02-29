<?php

namespace App\Enums\Order;

enum OrderStatus:string
{
    case PendingReview = 'Pending Review';
    case Ignore = 'Ignored';
    case Cancelled = 'Cancelled';
    case FollowUp = 'Follow Up';
    case Closed = 'Closed';

    public function label(): string
    {
        return static::getLabel($this);
    }

    public function class(): string
    {
        return static::getClass($this);
    }

    public function icon(): string
    {
        return static::getIcon($this);
    }

    public static function getLabel(self $value): string
    {
        return match ($value) {
            self::PendingReview => 'Pending Review',
            self::Ignore => 'Ignored',
            self::Cancelled => 'Cancelled',
            self::FollowUp => 'Follow Up',
            self::Closed => 'Closed',
            default => '-'
        };
    }

    public static function getClass(self $value) : string
    {
        return match ($value) {
            self::PendingReview => 'primary',
            self::Ignore => 'secondary',
            self::Cancelled => 'danger',
            self::FollowUp => 'info',
            self::Closed => 'success',
            default => 'info'
        };
    }

    public static function getIcon(self $value) : string
    {
        return match ($value) {
            self::PendingReview => 'info',
            self::Ignore => 'info',
            self::Cancelled => 'info',
            default => 'info'
        };
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
