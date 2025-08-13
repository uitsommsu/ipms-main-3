<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum UtilityModelStatusEnum: int implements HasLabel
{
    case SUBMITTED_UITSO = 1;
    case SUBMITTED_IPOPHL = 2;
    case FORMALITY_EXAMINATION_REPORT = 3;
    case SUBSEQUENT_EXAMINATION_REPORT = 4;
    case NOTICE_OF_PUBLICATION = 5;
    case SUBSTANTIVE_EXAMINATION_REPORT = 6;
    case PAYMENT_OF_ISSUANCE_OF_CERTIFICATE = 7;
    case NOTICE_OF_ISSUANCE = 8;
    case CLAIMING_OF_CERTIFICATE = 9;
    case ISSUANCE_OF_CERTIFICATE = 10;
    case APPROVED = 11;
    case ABANDONED = 12;
    case WITHDRAWN = 13;



    public function getLabel(): ?string
    {
        return match ($this) {
            self::SUBMITTED_UITSO => 'Submitted to UITSO',
            self::SUBMITTED_IPOPHL => 'Submitted to IPOPHL',
            self::FORMALITY_EXAMINATION_REPORT => 'Formality Examination Report',
            self::SUBSEQUENT_EXAMINATION_REPORT => 'Subsequent Examination Report',
            self::NOTICE_OF_PUBLICATION => 'Notice of Publication',
            self::SUBSTANTIVE_EXAMINATION_REPORT => 'Substantive Examination Report',
            self::PAYMENT_OF_ISSUANCE_OF_CERTIFICATE => 'Payment of Issuance of Certicate and Second Publication',
            self::NOTICE_OF_ISSUANCE => 'Notice of Issuance',
            self::CLAIMING_OF_CERTIFICATE => 'Claiming of Certificate',
            self::ISSUANCE_OF_CERTIFICATE => 'Issuance of Certicate to Inventor',
            self::APPROVED => 'Approved',
            self::ABANDONED => 'Abandoned',
            self::WITHDRAWN => 'Withdrawn',
        };
    }

    public static function selectable(): array
    {
        return collect(self::cases())
            ->reject(fn($case) => in_array($case, [self::ABANDONED,  self::WITHDRAWN]))
            ->mapWithKeys(fn($case) => [$case->value => $case->getLabel()])
            ->toArray();
    }
}
