<?php

namespace App\Filament\Resources\PatentResource\Pages;

use App\Models\PatentTask;
use Filament\Tables\Table;
use App\Enums\TaskStatusEnum;
use App\Models\PatentDocument;
use App\Enums\PatentStatusEnum;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\Page;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Contracts\HasTable;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Redirect;
use App\Filament\Resources\PatentResource;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Contracts\HasInfolists;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Infolists\Concerns\InteractsWithInfolists;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;

class PatentTrackProgress extends Page implements HasInfolists, HasTable
{
    use InteractsWithRecord;
    use InteractsWithInfolists;
    use InteractsWithTable;

    protected static string $resource = PatentResource::class;

    protected static string $view = 'filament.resources.patent-resource.pages.patent-track-progress';

    public function mount(int | string $record): void
    {
        $this->record = $this->resolveRecord($record);

        if ($this->record->status === PatentStatusEnum::ABANDONED || $this->record->status === PatentStatusEnum::WITHDRAWN) {
            Notification::make()->warning()->title('You are not allowed to view this page')->seconds(.5)->send();
            Redirect::to(PatentResource::getUrl('index'));
        }
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->record($this->record)
            ->schema([

                Section::make('General Information')
                    ->schema([
                        TextEntry::make('invention'),
                        TextEntry::make('inventors'),
                        TextEntry::make('status')
                            ->badge(),
                    ]),



            ]);
    }



    public function table(Table $table): Table
    {
        return $table
            ->query(PatentTask::query()->where('patent_id', $this->record->id)->latest('due_at'))
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
