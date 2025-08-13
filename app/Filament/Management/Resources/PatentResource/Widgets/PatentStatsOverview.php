<?php

namespace App\Filament\Management\Resources\PatentResource\Widgets;

use App\Enums\PatentStatusEnum;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class PatentStatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $totalOnProcessPatents = \App\Models\Patent::notDowngraded()
            ->where('status', '<>', PatentStatusEnum::WITHDRAWN)
            ->where('status', '<>', PatentStatusEnum::ABANDONED)
            ->where('status', '<>', PatentStatusEnum::APPROVED)
            ->count();

        $totalApprovedPatents = \App\Models\Patent::where('status', PatentStatusEnum::APPROVED)->count();

        $totalAbandonedPatents = \App\Models\Patent::where('status', PatentStatusEnum::ABANDONED)->count();

        $totalWithdrawnPatents = \App\Models\Patent::where('status', PatentStatusEnum::WITHDRAWN)->count();

        return [
            Stat::make('Total Approved Patents', $totalApprovedPatents)
                ->label('Total Approved Patents'),

            Stat::make('Total On Process Patents', $totalOnProcessPatents)
                ->label('Total On Process Patents'),

            Stat::make('Total Abandoned Patents', $totalAbandonedPatents)
                ->label('Total Abandoned Patents'),

            Stat::make('Total Withdrawn Patents', $totalWithdrawnPatents)
                ->label('Total Withdrawn Patents'),

        ];
    }
}
