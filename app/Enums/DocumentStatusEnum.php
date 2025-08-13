<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum DocumentStatusEnum : int implements HasLabel
{
    
    case UNDER_REVIEW = 1;
    case ON_REVISION = 2;
    case APPROVED = 3;

    public function getLabel(): ?string
    {
        return match ($this) {
            self::UNDER_REVIEW =>'Under Review',
            self::ON_REVISION =>'On Revision',
            self::APPROVED =>'Approved',
        };
    }
}
