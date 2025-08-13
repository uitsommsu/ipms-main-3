<?php

namespace App\Filament\Resources\UtilityModelResource\Pages;

use Filament\Tables\Table;
use App\Enums\TaskStatusEnum;
use App\Models\UtilityModelTask;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\Page;
use App\Enums\UtilityModelStatusEnum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Contracts\HasTable;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Redirect;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Contracts\HasInfolists;
use App\Filament\Resources\UtilityModelResource;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Infolists\Concerns\InteractsWithInfolists;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;

class UtilityModelTrackProgress extends Page implements HasInfolists, HasTable
{
    use InteractsWithRecord;
    use InteractsWithInfolists;
    use InteractsWithTable;

    protected static string $resource = UtilityModelResource::class;

    protected static string $view = 'filament.resources.utility-model-resource.pages.utility-model-track-progress';

    public function mount(int | string $record): void
    {
        $this->record = $this->resolveRecord($record);

        if ($this->record->status === UtilityModelStatusEnum::ABANDONED || $this->record->status === UtilityModelStatusEnum::WITHDRAWN) {
            Notification::make()->warning()->title('Viewing this page is not allowed')->seconds(.5)->send();
            Redirect::to(UtilityModelResource::getUrl('index'));
        }
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->record($this->record)
            ->schema([
                Section::make('General Information')
                    ->schema([
                        TextEntry::make('title'),
                        TextEntry::make('researchers'),
                        TextEntry::make('status')
                            ->badge(),
                    ]),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(UtilityModelTask::query()->where('utility_model_id', $this->record->id)->latest('due_at'))
            ->columns([
                TextColumn::make('title')
                    ->label('Task'),
                TextColumn::make('description')
                    ->wrap()
                    ->html(),
                TextColumn::make('due_at')
                    ->label('Due Date')
                    ->date('F j, Y'),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(function ($state) {
                        return match ($state) {
                            TaskStatusEnum::IN_PROGRESS => 'warning',
                            TaskStatusEnum::COMPLETED => 'success'
                        };
                    }),
            ]);
    }
}
