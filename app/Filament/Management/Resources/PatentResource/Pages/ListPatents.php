<?php

namespace App\Filament\Management\Resources\PatentResource\Pages;

use App\Filament\Management\Resources\PatentResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPatents extends ListRecords
{
    protected static string $resource = PatentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            PatentResource\Widgets\PatentStatsOverview::class,
        ];
    }
}
