<?php

namespace App\Filament\Management\Resources\PatentResource\Pages;

use Filament\Actions;
use App\Models\Patent;
use App\Enums\PatentStatusEnum;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use App\Filament\Management\Resources\PatentResource;
use App\Services\DowngradePatentToUtilityModelService;

class ViewPatent extends ViewRecord
{
    protected static string $resource = PatentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()
                ->visible(fn(Patent $record): bool => $record->status !== PatentStatusEnum::WITHDRAWN && $record->status !== PatentStatusEnum::ABANDONED),
            Actions\Action::make('downgrade')
                ->label('Downgrade to Utility Model')
                ->icon('heroicon-o-arrow-down-circle')
                ->color('warning')
                ->requiresConfirmation()
                ->modalHeading('Confirm Downgrade')
                ->modalDescription('This will transfer the patent and all associated tasks/documents to a Utility Model. Proceed?')
                ->modalSubmitActionLabel('Yes, Downgrade')
                ->visible(fn(Patent $record): bool => $record->original_utility_model_id === null && $record->status !== PatentStatusEnum::APPROVED && $record->status !== PatentStatusEnum::WITHDRAWN && $record->status !== PatentStatusEnum::ABANDONED)
                ->action(function (Patent $record) {
                    DowngradePatentToUtilityModelService::handle($record); // Create a service class for cleanliness
                    Notification::make()->success()->title('Downgrade Complete')->send();
                    return redirect(PatentResource::getUrl('index'));
                }),
            Actions\Action::make('withdrawn')
                ->label('Withdraw')
                ->icon('heroicon-o-viewfinder-circle')
                ->color('danger')
                ->requiresConfirmation()
                ->modalHeading('Confirm Withdrawal')
                ->modalDescription('This will withdraw the patent application. Proceed?')
                ->modalSubmitActionLabel('Yes, Witdraw')
                ->visible(fn(Patent $record): bool => $record->status !== PatentStatusEnum::APPROVED && $record->status !== PatentStatusEnum::WITHDRAWN && $record->status !== PatentStatusEnum::ABANDONED)
                ->action(function (Patent $record) {
                    $record->update([
                        'withdrawn_at' => now(),
                        'status' => PatentStatusEnum::WITHDRAWN,
                    ]);
                    Notification::make()->success()->title('Withdrawal Completed')->send();
                    return redirect(PatentResource::getUrl('index'));
                }),
            Actions\Action::make('abandon')
                ->label('Abandon')
                ->icon('heroicon-o-viewfinder-circle')
                ->color('danger')
                ->requiresConfirmation()
                ->modalHeading('Confirm Abandonment')
                ->modalDescription('This will abandon the patent application. Proceed?')
                ->modalSubmitActionLabel('Yes, Abandon')
                ->visible(fn(Patent $record): bool => $record->status !== PatentStatusEnum::APPROVED && $record->status !== PatentStatusEnum::WITHDRAWN && $record->status !== PatentStatusEnum::ABANDONED)
                ->action(function (Patent $record) {
                    $record->update([
                        'abandoned_at' => now(),
                        'status' => PatentStatusEnum::ABANDONED,
                    ]);
                    Notification::make()->success()->title('Abandon Completed')->send();
                    return redirect(PatentResource::getUrl('index'));
                })


        ];
    }
}
