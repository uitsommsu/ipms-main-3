<?php

namespace App\Filament\Management\Resources\PatentResource\Pages;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Enums\TaskStatusEnum;
use App\Enums\PatentStatusEnum;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Database\Eloquent\Builder;
use Filament\Resources\Pages\ManageRelatedRecords;
use Illuminate\Database\Eloquent\Relations\Relation;
use App\Filament\Management\Resources\PatentResource;

class ManagePatentTasks extends ManageRelatedRecords
{
    protected static string $resource = PatentResource::class;

    protected static string $relationship = 'tasks';


    protected static ?string $navigationIcon = 'heroicon-o-numbered-list';

    protected function authorizeAccess(): void
    {
        parent::authorizeAccess();

        if ($this->record->status === PatentStatusEnum::ABANDONED || $this->record->status === PatentStatusEnum::WITHDRAWN || $this->record->status === PatentStatusEnum::APPROVED) {
            Notification::make()->warning()->title('Cannot assign a task')->seconds(.5)->send();
            Redirect::to(PatentResource::getUrl('index'));
        }
    }



    public static function getNavigationLabel(): string
    {
        return 'Manage Tasks';
    }

    public function getTitle(): string
    {

        return "Manage Tasks";
    }

    public function getRelationship(): Relation | Builder
    {
        return $this->getOwnerRecord()->{static::getRelationshipName()}()->latest();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->required()
                    ->maxLength(255)
                    ->columnSpanFull(),
                Forms\Components\RichEditor::make('description')
                    ->label('About the Task')
                    ->disableToolbarButtons([
                        'blockquote',
                        'strike',
                        'attachFiles',
                    ])
                    ->columnSpanFull(),
                Forms\Components\DatePicker::make('due_at')
                    ->label('Due Date')
                    ->required()
                    ->date()
                    ->minDate(function ($operation) {
                        // Apply minDate only on create
                        return $operation === 'create' ? now() : null;
                    }),

                Forms\Components\Select::make('status')
                    ->options(TaskStatusEnum::class)
                    ->default(TaskStatusEnum::IN_PROGRESS->value)
                    ->required()
                    ->visibleOn('edit'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->wrap(),
                Tables\Columns\TextColumn::make('description')
                    ->html()
                    ->wrap(),
                Tables\Columns\TextColumn::make('due_at')
                    ->label('Due Date')
                    ->date(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(static function ($record) {
                        return match ($record->status) {
                            TaskStatusEnum::IN_PROGRESS => 'gray',
                            TaskStatusEnum::COMPLETED => 'success',
                        };
                    }),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('New Task')
                    ->modalHeading('Create Task')
                    ->createAnother(false)
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['status'] = TaskStatusEnum::IN_PROGRESS->value;
                        return $data;
                    }),

            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->visible(fn($record) => $record->status !== TaskStatusEnum::COMPLETED),
                Tables\Actions\DeleteAction::make()
                    ->visible(fn($record) => $record->status !== TaskStatusEnum::COMPLETED),
            ])
            ->bulkActions([]);
    }
}
