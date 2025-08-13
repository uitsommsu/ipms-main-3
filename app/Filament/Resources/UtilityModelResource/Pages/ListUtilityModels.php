<?php

namespace App\Filament\Resources\UtilityModelResource\Pages;

use App\Filament\Resources\UtilityModelResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListUtilityModels extends ListRecords
{
    protected static string $resource = UtilityModelResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
