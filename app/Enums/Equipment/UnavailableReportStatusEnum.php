<?php

namespace App\Enums\Equipment;

use App\Traits\Enum\StatusEnumTrait;

enum UnavailableReportStatusEnum: string
{
    use StatusEnumTrait;
    
    case PendingReview = 'Pending Review';
    case Completed = 'Completed';

    public static function getLabel(self $value): string 
    {
        return match ($value) {
            self::PendingReview => 'Pending Review',
            self::Completed => 'Completed',
            default => '-'
        };
    }

    public static function getClass(self $value) : string 
    {
        return match ($value) {
            self::PendingReview => 'warning',
            self::Completed => 'success',
            default => 'primary'
        };
    }

    public static function getColor(self $value) : string
    {
        return match ($value) {
            self::PendingReview => '#BDACD0',
            self::Completed => '#2361CE',
            default => '#1F2937'
        };
    }

    public static function getIcon(self $value) : string
    {
        return match ($value) {
            self::PendingReview => 'fas fa-exclamation-triangle',
            self::Completed => 'fas fa-check-circle',
            default => ''
        };
    }
}
