<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\Models\User;
use App\Models\PatentTask;
use App\Enums\UserRoleEnum;
use App\Enums\TaskStatusEnum;
use App\Enums\UserStatusEnum;
use Illuminate\Console\Command;
use App\Notifications\PatentTaskDueNotification;

class CheckPatentTasksDueCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tasks:check-patent-tasks-due-command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check for tasks due in 2 days and send notifications';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $twoDaysFromNow = Carbon::now()->addDays(2)->startOfDay();

        $tasks = PatentTask::with('patent', 'patent.proponents')
            ->where('due_at', $twoDaysFromNow)
            ->where('status', '!=', TaskStatusEnum::COMPLETED)
            ->get();

        $managers = User::where('role', UserRoleEnum::MANAGEMENT)
            ->where('status', UserStatusEnum::ACTIVE)
            ->get();

        foreach ($tasks as $task) {
            // Notify task owner
            $proponents = $task->patent->proponents;
            $proponents->each(function ($proponent) use ($task) {
                $proponent->notify(new PatentTaskDueNotification($task));
            });

            // Notify management

            $managers->each(function ($manager) use ($task) {
                $manager->notify(new PatentTaskDueNotification($task));
            });
        }
    }
}
