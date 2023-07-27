<?php

namespace App\Enums\User;

use Exception;

enum UserStatusEnum: int
{
    case Active = 1;
    case Inactive = 0;

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

    public function buttonName() : string
    {
        return static::getButtonName($this);
    }

    public static function getLabel(self $value): string
    {
        return match ($value) {
            self::Active => 'Active',
            self::Inactive => 'Inactive',
            default => '-'
        };
    }

    public static function getClass(self $value) : string
    {
        return match ($value) {
            self::Active => 'success',
            self::Inactive => 'danger',
            default => 'danger'
        };
    }

    public static function getIcon(self $value) : string
    {
        return match ($value) {
            self::Active => 'fa-check-circle',
            self::Inactive => 'fa-times-circle',
            default => 'fa-times-circle'
        };
    }

    public static function getButtonName(self $value) : string
    {
        return match ($value) {
            self::Active => 'Activate',
            self::Inactive => 'Deactivate',
            default => 'Activate'
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
