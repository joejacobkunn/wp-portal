<?php

namespace App\Enums\Order;

enum FortisStatus:int
{
    case SALE_APPROVED = 101;
    case SALE_CC_AUTHONLY = 102;
    case REFUND_CC_REFUNDED = 111;
    case CC_AVS_ONLY = 121;
    case ACH_PENDING_ORGANISATION = 131;
    case ACH_ORIGINATING = 132;
    case ACH_ORIGINATED = 133;
    case ACH_SETTLED = 134;
    case BATCH_SETTLED = 191;
    case CC_ACH_VOIDED = 201;
    case CC_ACH_DECLINED = 301;
    case CCH_ACH_CHARGED_BACK = 331;

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
            self::SALE_APPROVED => 'Sale cc Approved',
            self::SALE_CC_AUTHONLY => 'Sale cc AuthOnly',
            self::REFUND_CC_REFUNDED => 'Refund cc Refunded',
            self::CC_AVS_ONLY => 'Credit/Debit/Refund cc AvsOnly',
            self::ACH_PENDING_ORGANISATION => 'Credit/Debit/Refund ach Pending Origination',
            self::ACH_ORIGINATING => 'Credit/Debit/Refund ach Originating',
            self::ACH_ORIGINATED => 'Credit/Debit/Refund ach Originated',
            self::ACH_SETTLED => 'Credit/Debit/Refund ach Settled',
            self::BATCH_SETTLED => 'Settled',
            self::CC_ACH_VOIDED => 'All cc/ach Voided',
            self::CC_ACH_DECLINED => 'All cc/ach Declined',
            self::CCH_ACH_CHARGED_BACK => 'Credit/Debit/Refund ach Charged Back',

            default => '-'
        };
    }

    public static function getClass(self $value) : string
    {
        return match ($value) {
            self::SALE_APPROVED => 'success',
            self::SALE_CC_AUTHONLY => 'success',
            self::REFUND_CC_REFUNDED => 'success',
            self::CC_AVS_ONLY => 'success',
            self::ACH_PENDING_ORGANISATION => 'info',
            self::ACH_ORIGINATING => 'info',
            self::ACH_ORIGINATED => 'info',
            self::ACH_SETTLED => 'info',
            self::BATCH_SETTLED => 'info',
            self::CC_ACH_VOIDED => 'danger',
            self::CC_ACH_DECLINED => 'danger',
            self::CCH_ACH_CHARGED_BACK => 'danger',
            default => 'info'
        };
    }

    public static function getIcon(self $value) : string
    {
        return match ($value) {
            self::SALE_APPROVED => 'fa-check-circle',
            self::SALE_CC_AUTHONLY => 'fa-check-circle',
            self::REFUND_CC_REFUNDED => 'fa-check-circle',
            self::CC_AVS_ONLY => 'fa-check-circle',
            self::ACH_PENDING_ORGANISATION => 'fa-info-circle',
            self::ACH_ORIGINATING => 'fa-info-circle',
            self::ACH_ORIGINATED => 'fa-info-circle',
            self::ACH_SETTLED => 'fa-info-circle',
            self::BATCH_SETTLED => 'fa-info-circle',
            self::CC_ACH_VOIDED => 'fa-times',
            self::CC_ACH_DECLINED => 'fa-times',
            self::CCH_ACH_CHARGED_BACK => 'fa-times',
            default => 'fa-info-circle'
        };
    }
}
