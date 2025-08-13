<?php

namespace App\Filament\Management\Widgets;

use App\Enums\PatentStatusEnum;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class PatentStats extends BaseWidget
{

    protected ?string $heading = 'Patents Analytics';

    protected ?string $description = 'An overview of Patents analytics.';

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
