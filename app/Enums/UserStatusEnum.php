<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum UserStatusEnum : int implements HasLabel
{
       case ACTIVE = 1 ;
       case UNVERIFIED = 2;
       

       public function getLabel(): ?string
       {
           return match ($this) {
               self::ACTIVE =>'Active',
               self::UNVERIFIED =>'Unverified'
           };
       }

}