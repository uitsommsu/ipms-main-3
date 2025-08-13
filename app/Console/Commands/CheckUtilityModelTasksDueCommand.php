<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\Models\User;
use App\Enums\UserRoleEnum;
use App\Enums\TaskStatusEnum;
use App\Enums\UserStatusEnum;
use Illuminate\Console\Command;
use App\Models\UtilityModelTask;
use App\Notifications\UtilityModelTaskDueNotification;

class CheckUtilityModelTasksDueCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tasks:check-utility-model-tasks-due-command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check for Utility Model tasks due in 2 days and send notifications';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $twoDaysFromNow = Carbon::now()->addDays(2)->startOfDay();

        $tasks = UtilityModelTask::with('utilityModel', 'utilityModel.proponents')
            ->where('due_at', $twoDaysFromNow)
            ->where('status', '!=', TaskStatusEnum::COMPLETED)
            ->get();

        $managers = User::where('role', UserRoleEnum::MANAGEMENT)
            ->where('status', UserStatusEnum::ACTIVE)
            ->get();

        foreach ($tasks as $task) {
            // Notify task owner
            $proponents = $task->utilityModel->proponents;
            $proponents->each(function ($proponent) use ($task) {
                $proponent->notify(new UtilityModelTaskDueNotification($task));
            });

            // Notify management

            $managers->each(function ($manager) use ($task) {
                $manager->notify(new UtilityModelTaskDueNotification($task));
            });
        }
    }
}
