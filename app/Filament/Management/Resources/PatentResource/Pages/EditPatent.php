<?php

namespace App\Filament\Management\Resources\PatentResource\Pages;

use Filament\Actions;
use App\Enums\PatentStatusEnum;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Auth\Access\AuthorizationException;
use App\Filament\Management\Resources\PatentResource;

class EditPatent extends EditRecord
{
    protected static string $resource = PatentResource::class;

    protected function authorizeAccess(): void
    {
        parent::authorizeAccess();

        if ($this->record->status === PatentStatusEnum::ABANDONED || $this->record->status === PatentStatusEnum::WITHDRAWN) {
            Notification::make()->warning()->title('Editing is not allowed')->seconds(.5)->send();
            Redirect::to(PatentResource::getUrl('index'));
        }
    }


    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
