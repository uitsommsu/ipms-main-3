<?php

namespace App\Filament\Management\Resources\AssistanceFormResource\Pages;

use Filament\Actions;
use Illuminate\Support\Str;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Management\Resources\AssistanceFormResource;

class EditAssistanceForm extends EditRecord
{
    protected static string $resource = AssistanceFormResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {

        if (trim($data['response'])) {
            $data['is_responded'] = true;
            $data['responded_at'] = now();
        }


        return $data;
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
