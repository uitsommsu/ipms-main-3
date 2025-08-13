<?php

namespace App\Filament\Management\Resources\UtilityModelResource\Widgets;

use App\Enums\UtilityModelStatusEnum;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class UtilityModelStatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $totalOnProcessUtilityModel = \App\Models\UtilityModel::notUpgradedToPatent()
            ->where('status', '<>', UtilityModelStatusEnum::APPROVED)
            ->where('status', '<>', UtilityModelStatusEnum::WITHDRAWN)
            ->where('status', '<>', UtilityModelStatusEnum::ABANDONED)
            ->count();

        $totalApprovedUtilityModel = \App\Models\UtilityModel::where('status', UtilityModelStatusEnum::APPROVED)->count();

        $totalAbandonedUtilityModel = \App\Models\UtilityModel::where('status', UtilityModelStatusEnum::ABANDONED)->count();

        $totalWithdrawnUtilityModel = \App\Models\UtilityModel::where('status', UtilityModelStatusEnum::WITHDRAWN)->count();

        return [
            Stat::make('Total Approved Utility Models', $totalApprovedUtilityModel)
                ->label('Total Approved Utility Models'),

            Stat::make('Total On Process Utility Models', $totalOnProcessUtilityModel)
                ->label('Total On Process Utility Models'),

            Stat::make('Total Abandoned Utility Models', $totalAbandonedUtilityModel)
                ->label('Total Abandoned Utility Models'),

            Stat::make('Total Withdrawn Utility Models', $totalWithdrawnUtilityModel)
                ->label('Total Withdrawn Utility Models'),

        ];
    }
}
