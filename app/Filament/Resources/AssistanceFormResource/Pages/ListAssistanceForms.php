<?php

namespace App\Filament\Resources\AssistanceFormResource\Pages;

use App\Filament\Resources\AssistanceFormResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAssistanceForms extends ListRecords
{
    protected static string $resource = AssistanceFormResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
