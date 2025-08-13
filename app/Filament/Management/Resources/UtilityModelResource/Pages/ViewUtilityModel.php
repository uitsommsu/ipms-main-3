<?php

namespace App\Filament\Management\Resources\UtilityModelResource\Pages;

use App\Enums\UtilityModelStatusEnum;
use Filament\Actions;
use App\Models\UtilityModel;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use App\Services\UpgradeUtilityModelToPatentService;
use App\Filament\Management\Resources\UtilityModelResource;

class ViewUtilityModel extends ViewRecord
{
    protected static string $resource = UtilityModelResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\Action::make('upgrade')
                ->label('Upgrade to Patent')
                ->icon('heroicon-o-arrow-up-circle')
                ->color('warning')
                ->requiresConfirmation()
                ->modalHeading('Confirm Upgrade')
                ->modalDescription('This will transfer the Utility Model and all associated tasks/documents to a Patent. Proceed?')
                ->modalSubmitActionLabel('Yes, Upgrade')
                ->visible(fn(UtilityModel $record): bool => $record->original_patent_id === null && $record->status !== UtilityModelStatusEnum::APPROVED)
                ->action(function (UtilityModel $record) {
                    UpgradeUtilityModelToPatentService::handle($record); // Create a service class for cleanliness
                    Notification::make()->success()->title('Downgrade Complete')->send();
                    return redirect(UtilityModelResource::getUrl('index'));
                }),

            Actions\Action::make('withdrawn')
                ->label('Withdraw')
                ->icon('heroicon-o-viewfinder-circle')
                ->color('danger')
                ->requiresConfirmation()
                ->modalHeading('Confirm Withdrawal')
                ->modalDescription('This will withdraw the utility model application. Proceed?')
                ->modalSubmitActionLabel('Yes, Witdraw')
                ->visible(fn(UtilityModel $record): bool => $record->status !== UtilityModelStatusEnum::APPROVED && $record->status !== UtilityModelStatusEnum::WITHDRAWN && $record->status !== UtilityModelStatusEnum::ABANDONED)
                ->action(function (UtilityModel $record) {
                    $record->update([
                        'withdrawn_at' => now(),
                        'status' => UtilityModelStatusEnum::WITHDRAWN,
                    ]);
                    Notification::make()->success()->title('Withdrawal Completed')->send();
                    return redirect(UtilityModelResource::getUrl('index'));
                }),

            Actions\Action::make('abandon')
                ->label('Abandon')
                ->icon('heroicon-o-viewfinder-circle')
                ->color('danger')
                ->requiresConfirmation()
                ->modalHeading('Confirm Abandonment')
                ->modalDescription('This will abandon the utility model application. Proceed?')
                ->modalSubmitActionLabel('Yes, Abandon')
                ->visible(fn(UtilityModel $record): bool => $record->status !== UtilityModelStatusEnum::APPROVED && $record->status !== UtilityModelStatusEnum::WITHDRAWN && $record->status !== UtilityModelStatusEnum::ABANDONED)
                ->action(function (UtilityModel $record) {
                    $record->update([
                        'abandoned_at' => now(),
                        'status' => UtilityModelStatusEnum::ABANDONED,
                    ]);
                    Notification::make()->success()->title('Abandon Completed')->send();
                    return redirect(UtilityModelResource::getUrl('index'));
                })
        ];
    }
}
