<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum UserRoleEnum : int implements HasLabel
{
       case ADMIN = 1 ;
       case MANAGEMENT = 2 ;
       case RESEARCHER = 3;
       

       public function getLabel(): ?string
       {
           return match ($this) {
               self::ADMIN =>'Admin',
               self::MANAGEMENT =>'Management',
               self::RESEARCHER =>'Researcher'
           };
       }

}