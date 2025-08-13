<?php

namespace App\Observers;

use App\Mail\UtilityModelTaskMail;
use App\Models\UtilityModelTask;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Mail;

class UtilityModelTaskObserver
{
    /**
     * Handle the UtilityModelTask "created" event.
     */
    public function created(UtilityModelTask $utilityModelTask): void
    {
        $proponents = $utilityModelTask->utilityModel->proponents;
        Notification::make()
        ->title('New Task for '. $utilityModelTask->utilityModel->title)
        ->success()
        ->sendToDatabase($proponents);
        
        Mail::to($proponents)->send(new UtilityModelTaskMail($utilityModelTask));
    }

    /**
     * Handle the UtilityModelTask "updated" event.
     */
    public function updated(UtilityModelTask $utilityModelTask): void
    {
        //
    }

    /**
     * Handle the UtilityModelTask "deleted" event.
     */
    public function deleted(UtilityModelTask $utilityModelTask): void
    {
        //
    }

    /**
     * Handle the UtilityModelTask "restored" event.
     */
    public function restored(UtilityModelTask $utilityModelTask): void
    {
        //
    }

    /**
     * Handle the UtilityModelTask "force deleted" event.
     */
    public function forceDeleted(UtilityModelTask $utilityModelTask): void
    {
        //
    }
}
