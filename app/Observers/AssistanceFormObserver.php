<?php

namespace App\Observers;

use App\Models\User;
use App\Models\AssistanceForm;
use Filament\Notifications\Notification;
use Filament\Notifications\Actions\Action;

class AssistanceFormObserver
{
    /**
     * Handle the AssistanceForm "created" event.
     */
    public function created(AssistanceForm $assistanceForm): void
    {
        $users = User::listOfAdminAndManagementUsers();

        Notification::make()
            ->title('Assistance Form was submitted by ' . $assistanceForm->user->name)
            ->success()
            ->sendToDatabase($users);
    }

    /**
     * Handle the AssistanceForm "updated" event.
     */
    public function updated(AssistanceForm $assistanceForm): void
    {
        if ($assistanceForm->isDirty('response')) {
            Notification::make()
                ->title('Check the response to your inquiries on your Assistance Form List Page')
                ->success()
                ->sendToDatabase($assistanceForm->user);
        }
    }

    /**
     * Handle the AssistanceForm "deleted" event.
     */
    public function deleted(AssistanceForm $assistanceForm): void
    {
        //
    }

    /**
     * Handle the AssistanceForm "restored" event.
     */
    public function restored(AssistanceForm $assistanceForm): void
    {
        //
    }

    /**
     * Handle the AssistanceForm "force deleted" event.
     */
    public function forceDeleted(AssistanceForm $assistanceForm): void
    {
        //
    }
}
