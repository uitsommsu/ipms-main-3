<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum UtilityModelDocumentTypeEnum :int implements HasLabel
{

    case DISCLOSURE_FORM = 1;
    case CLAIMS = 2;
    case SPECIFICATION = 3;
    case ABSTRACT = 4;
    case FORMALITY_EXAMINATION_REPORT = 5;
    case SUBSEQUENT_FORMALITY_EXAMINATION_REPORT = 6;
    case OTHER_IP_DOCUMENT = 7;

    public function getLabel(): ?string
       {
           return match ($this) {
               self::DISCLOSURE_FORM =>'Technology Disclosure Form',
               self::CLAIMS =>'Claims',
               self::SPECIFICATION =>'Specification',
               self::ABSTRACT =>'Abstract',
               self::FORMALITY_EXAMINATION_REPORT => 'Formality Examination Report',
               self::SUBSEQUENT_FORMALITY_EXAMINATION_REPORT =>'Subsequent Formality Examination Report',
               self::OTHER_IP_DOCUMENT => 'Other IP Document',
           };
       }
}
