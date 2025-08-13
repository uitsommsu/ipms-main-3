<?php

namespace App\Observers;

use App\Models\PatentTask;
use App\Mail\PatentTaskMail;
use Illuminate\Support\Facades\Mail;
use Filament\Notifications\Notification;

class PatentTaskObserver
{
    /**
     * Handle the PatentTask "created" event.
     */
    public function created(PatentTask $patentTask): void
    {
        $proponents = $patentTask->patent->proponents;
        Notification::make()
        ->title('New Task for '. $patentTask->patent->invention)
        ->success()
        ->sendToDatabase($proponents);

        Mail::to($proponents)->send(new PatentTaskMail($patentTask));
        
    }

    /**
     * Handle the PatentTask "updated" event.
     */
    public function updated(PatentTask $patentTask): void
    {
        //
    }

    /**
     * Handle the PatentTask "deleted" event.
     */
    public function deleted(PatentTask $patentTask): void
    {
        //
    }

    /**
     * Handle the PatentTask "restored" event.
     */
    public function restored(PatentTask $patentTask): void
    {
        //
    }

    /**
     * Handle the PatentTask "force deleted" event.
     */
    public function forceDeleted(PatentTask $patentTask): void
    {
        //
    }
}
