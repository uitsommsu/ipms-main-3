<?php

namespace App\Filament\Resources\AssistanceFormResource\Pages;

use App\Filament\Resources\AssistanceFormResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateAssistanceForm extends CreateRecord
{
    protected static string $resource = AssistanceFormResource::class;

    protected static bool $canCreateAnother = false;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = auth()->id();

        return $data;
    }
}
