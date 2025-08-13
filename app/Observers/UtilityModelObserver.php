<?php

namespace App\Observers;

use App\Models\User;
use App\Models\UtilityModel;
use Filament\Notifications\Notification;
use Filament\Notifications\Actions\Action;

class UtilityModelObserver
{
    /**
     * Handle the UtilityModel "created" event.
     */
    public function created(UtilityModel $utilityModel): void
    {
        $users = User::listOfAdminAndManagementUsers();

        Notification::make()
        ->title('New Utility Model submission: '. $utilityModel->title)
        ->success()
        ->actions([
            Action::make('view')
                ->url(route('filament.management.resources.utility-models.view', ['record' => $utilityModel->slug]))
                ->button(),
            
        ])
        ->sendToDatabase($users);
    }

    /**
     * Handle the UtilityModel "updated" event.
     */
    public function updated(UtilityModel $utilityModel): void
    {
        $users = User::listOfAdminAndManagementUsers();

        Notification::make()
        ->title('Utility Model updated: '. $utilityModel->title)
        ->success()
        ->actions([
            Action::make('view')
                ->url(route('filament.management.resources.utility-models.view', ['record' => $utilityModel->slug]))
                ->button(),
            
        ])
        ->sendToDatabase($users);
    }

    /**
     * Handle the UtilityModel "deleted" event.
     */
    public function deleted(UtilityModel $utilityModel): void
    {
        $users = User::listOfAdminAndManagementUsers();

        Notification::make()
        ->title('Utility Model deleted: '. $utilityModel->title)
        ->warning()
        ->sendToDatabase($users);
    }

    /**
     * Handle the UtilityModel "restored" event.
     */
    public function restored(UtilityModel $utilityModel): void
    {
        //
    }

    /**
     * Handle the UtilityModel "force deleted" event.
     */
    public function forceDeleted(UtilityModel $utilityModel): void
    {
        //
    }
}
