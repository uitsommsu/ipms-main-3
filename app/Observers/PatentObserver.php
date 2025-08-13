<?php

namespace App\Observers;

use App\Models\User;
use App\Models\Patent;
use App\Enums\UserRoleEnum;
use Filament\Notifications\Notification;
use Filament\Notifications\Actions\Action;

class PatentObserver
{
    /**
     * Handle the Patent "created" event.
     */
    public function created(Patent $patent): void
    {
        $users = User::listOfAdminAndManagementUsers();

        Notification::make()
        ->title('New Patent submission: '. $patent->invention)
        ->success()
        ->actions([
            Action::make('view')
                ->url(route('filament.management.resources.patents.view', ['record' => $patent->slug]))
                ->button(),
            
        ])
        ->sendToDatabase($users);

        
    }

    /**
     * Handle the Patent "updated" event.
     */
    public function updated(Patent $patent): void
    {
        $users = User::listOfAdminAndManagementUsers();

        Notification::make()
        ->title('Patent updated: '. $patent->invention)
        ->success()
        ->actions([
            Action::make('view')
                ->url(route('filament.management.resources.patents.view', ['record' => $patent->slug]))
                ->button(),
            
        ])
        ->sendToDatabase($users);

    }

    /**
     * Handle the Patent "deleted" event.
     */
    public function deleted(Patent $patent): void
    {
        $users = User::listOfAdminAndManagementUsers();

        Notification::make()
        ->title('Patent deleted: '. $patent->invention)
        ->warning()
        ->sendToDatabase($users);
    }

    /**
     * Handle the Patent "restored" event.
     */
    public function restored(Patent $patent): void
    {
        //
    }

    /**
     * Handle the Patent "force deleted" event.
     */
    public function forceDeleted(Patent $patent): void
    {
        //
    }
}
