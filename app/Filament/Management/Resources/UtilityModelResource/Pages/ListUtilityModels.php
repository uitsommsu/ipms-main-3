<?php

namespace App\Filament\Management\Resources\UtilityModelResource\Pages;

use App\Filament\Management\Resources\UtilityModelResource;
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

    protected function getHeaderWidgets(): array
    {
        return [
            UtilityModelResource\Widgets\UtilityModelStatsOverview::class,
        ];
    }
}
