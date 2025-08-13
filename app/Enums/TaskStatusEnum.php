<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum TaskStatusEnum: int implements HasLabel
{
    case IN_PROGRESS = 1;
    case COMPLETED = 2;

    public function getLabel(): ?string
    {
        return match ($this) {
            self::IN_PROGRESS => 'In Progress',
            self::COMPLETED => 'Completed',
        };
    }
}
