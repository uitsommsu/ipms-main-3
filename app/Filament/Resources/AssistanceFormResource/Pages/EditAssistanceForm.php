<?php

namespace App\Filament\Resources\AssistanceFormResource\Pages;

use App\Filament\Resources\AssistanceFormResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAssistanceForm extends EditRecord
{
    protected static string $resource = AssistanceFormResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
