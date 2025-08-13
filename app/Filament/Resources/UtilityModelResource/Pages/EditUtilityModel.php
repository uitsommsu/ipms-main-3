<?php

namespace App\Filament\Resources\UtilityModelResource\Pages;

use Filament\Actions;
use App\Enums\UtilityModelStatusEnum;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Redirect;
use App\Filament\Resources\UtilityModelResource;

class EditUtilityModel extends EditRecord
{
    protected static string $resource = UtilityModelResource::class;

    protected function authorizeAccess(): void
    {
        parent::authorizeAccess();

        if ($this->record->status === UtilityModelStatusEnum::ABANDONED || $this->record->status === UtilityModelStatusEnum::WITHDRAWN) {
            Notification::make()->warning()->title('Editing is not allowed')->seconds(.5)->send();
            Redirect::to(UtilityModelResource::getUrl('index'));
        }
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
