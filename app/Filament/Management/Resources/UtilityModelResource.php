<?php

namespace App\Filament\Management\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Set;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use App\Models\UtilityModel;
use Dflydev\DotAccessData\Util;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use App\Enums\UtilityModelStatusEnum;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Fieldset;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\Group;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ImageEntry;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Management\Resources\UtilityModelResource\Pages;
use App\Filament\Management\Resources\UtilityModelResource\RelationManagers;

class UtilityModelResource extends Resource
{
    protected static ?string $model = UtilityModel::class;

    protected static ?int $navigationSort = 3;

    protected static ?string $navigationIcon = 'heroicon-o-circle-stack';

    protected static ?string $recordTitleAttribute = 'title';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->maxLength(500)
                    ->required()
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn(Set $set, ?string $state) => $set('slug', Str::slug($state)))
                    ->columnSpanFull(),

                Forms\Components\TextInput::make('slug')
                    ->readOnly()
                    ->unique(UtilityModel::class, 'slug', ignoreRecord: true)
                    ->columnSpanFull(),

                Forms\Components\TextInput::make('researchers')
                    ->maxLength(500)
                    ->required()
                    ->columnSpanFull(),

                Forms\Components\RichEditor::make('description')
                    ->disableToolbarButtons([
                        'blockquote',
                        'strike',
                        'attachFiles',
                        'link',
                    ])
                    ->required()
                    ->columnSpanFull(),

                Forms\Components\TextInput::make('um_number')
                    ->label('UM number')
                    ->maxLength(255),

                Forms\Components\Select::make('status')
                    ->required()
                    ->options(UtilityModelStatusEnum::selectable()),

                Fieldset::make('dates')
                    ->label('')
                    ->schema([
                        Forms\Components\DatePicker::make('filing_date'),
                        Forms\Components\DatePicker::make('publication_date'),
                        Forms\Components\DatePicker::make('expiry_date'),
                    ])
                    ->columns(3),

                Forms\Components\FileUpload::make('images')
                    ->image()
                    ->multiple()
                    ->imageEditor()
                    ->disk('public')
                    ->directory('photos')
                    ->visibility('public')
                    ->columnSpanFull(),

                Forms\Components\Section::make('Attach Inventors account')
                    ->schema([
                        Select::make('proponents')
                            ->label("User Account")
                            ->multiple()
                            ->relationship('proponents', 'name')
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->query(UtilityModel::notUpgradedToPatent()->latest())
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->wrap()
                    ->searchable(),
                Tables\Columns\TextColumn::make('researchers')
                    ->wrap()
                    ->searchable(),
                Tables\Columns\TextColumn::make('um_number')
                    ->label('UM number')
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('publication_date')
                    ->date()
                    ->toggleable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(static function ($record) {
                        return match ($record->status) {
                            UtilityModelStatusEnum::APPROVED => 'success',
                            UtilityModelStatusEnum::FORMALITY_EXAMINATION_REPORT => 'warning',
                            UtilityModelStatusEnum::SUBSEQUENT_EXAMINATION_REPORT => 'warning',
                            UtilityModelStatusEnum::NOTICE_OF_PUBLICATION => 'warning',
                            UtilityModelStatusEnum::SUBSTANTIVE_EXAMINATION_REPORT => 'warning',
                            UtilityModelStatusEnum::PAYMENT_OF_ISSUANCE_OF_CERTIFICATE => 'warning',
                            UtilityModelStatusEnum::NOTICE_OF_ISSUANCE => 'warning',
                            UtilityModelStatusEnum::CLAIMING_OF_CERTIFICATE => 'warning',
                            UtilityModelStatusEnum::ISSUANCE_OF_CERTIFICATE => 'warning',
                            UtilityModelStatusEnum::SUBMITTED_IPOPHL => 'info',
                            UtilityModelStatusEnum::SUBMITTED_UITSO => 'gray',
                            UtilityModelStatusEnum::WITHDRAWN => 'danger',
                            UtilityModelStatusEnum::ABANDONED => 'danger',
                        };
                    }),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options(UtilityModelStatusEnum::class)
            ])
            ->actions([
                ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make()
                        ->visible(fn(UtilityModel $record): bool => $record->status !== UtilityModelStatusEnum::ABANDONED && $record->status !== UtilityModelStatusEnum::WITHDRAWN),
                    Tables\Actions\Action::make('Documents')
                        ->icon('heroicon-o-document')
                        ->url(fn(UtilityModel $record): string =>  self::getUrl('documents', ['record' => $record]))
                        ->visible(fn(UtilityModel $record): bool => $record->status !== UtilityModelStatusEnum::ABANDONED && $record->status !== UtilityModelStatusEnum::WITHDRAWN && $record->status !== UtilityModelStatusEnum::APPROVED),
                    Tables\Actions\Action::make('Tasks')
                        ->icon('heroicon-o-numbered-list')
                        ->visible(fn(UtilityModel $record): bool => $record->status !== UtilityModelStatusEnum::WITHDRAWN && $record->status !== UtilityModelStatusEnum::APPROVED)
                        ->url(fn(UtilityModel $record): string =>  self::getUrl('tasks', ['record' => $record])),
                    Tables\Actions\Action::make('Refile')
                        ->icon('heroicon-o-viewfinder-circle')
                        ->requiresConfirmation()
                        ->modalHeading('Confirm Refilement')
                        ->modalDescription('This will refile the patent application. Proceed?')
                        ->modalSubmitActionLabel('Yes, Refile')
                        ->action(function (UtilityModel $record) {
                            $record->update([
                                'withdrawn_at' => null,
                                'status' => UtilityModelStatusEnum::SUBMITTED_IPOPHL,
                            ]);
                            Notification::make()->success()->title('Refile Process Completed')->send();
                        })
                        ->visible(fn(UtilityModel $record): bool => $record->status == UtilityModelStatusEnum::WITHDRAWN),
                ])->visible(fn(UtilityModel $record): bool => $record->status !== UtilityModelStatusEnum::ABANDONED),

            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->searchPlaceholder('Search (Title, Researchers, Utility Model Number)');
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('General Information')
                    ->schema([
                        Grid::make()
                            ->schema([
                                Group::make([
                                    TextEntry::make('title'),
                                    TextEntry::make('researchers'),
                                    Grid::make()
                                        ->schema([
                                            TextEntry::make('um_number')
                                                ->label('UM #'),
                                            TextEntry::make('status')
                                                ->badge()
                                                ->color(static function ($record) {
                                                    return match ($record->status) {
                                                        UtilityModelStatusEnum::APPROVED => 'success',
                                                        UtilityModelStatusEnum::FORMALITY_EXAMINATION_REPORT => 'warning',
                                                        UtilityModelStatusEnum::SUBSEQUENT_EXAMINATION_REPORT => 'warning',
                                                        UtilityModelStatusEnum::NOTICE_OF_PUBLICATION => 'warning',
                                                        UtilityModelStatusEnum::SUBSTANTIVE_EXAMINATION_REPORT => 'warning',
                                                        UtilityModelStatusEnum::PAYMENT_OF_ISSUANCE_OF_CERTIFICATE => 'warning',
                                                        UtilityModelStatusEnum::NOTICE_OF_ISSUANCE => 'warning',
                                                        UtilityModelStatusEnum::CLAIMING_OF_CERTIFICATE => 'warning',
                                                        UtilityModelStatusEnum::ISSUANCE_OF_CERTIFICATE => 'warning',
                                                        UtilityModelStatusEnum::SUBMITTED_IPOPHL => 'info',
                                                        UtilityModelStatusEnum::SUBMITTED_UITSO => 'gray',
                                                        UtilityModelStatusEnum::WITHDRAWN => 'danger',
                                                        UtilityModelStatusEnum::ABANDONED => 'danger',
                                                    };
                                                }),
                                        ])


                                ]),

                                Group::make([


                                    TextEntry::make('filing_date')
                                        ->badge()
                                        ->date()
                                        ->color('success'),
                                    TextEntry::make('publication_date')
                                        ->badge()
                                        ->date()
                                        ->color('success'),
                                    TextEntry::make('expiry_date')
                                        ->badge()
                                        ->date()
                                        ->color('success'),

                                ])
                            ])

                    ]),

                Section::make('Description')
                    ->schema([
                        TextEntry::make('description')
                            ->prose()
                            ->html()
                            ->hiddenLabel()
                    ]),

                Section::make('Photos')
                    ->schema([
                        ImageEntry::make('images')
                            ->hiddenLabel()
                            ->grow(true),
                    ])




            ]);
    }


    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getWidgets(): array
    {
        return [
            UtilityModelResource\Widgets\UtilityModelStatsOverview::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUtilityModels::route('/'),
            'create' => Pages\CreateUtilityModel::route('/create'),
            'view' => Pages\ViewUtilityModel::route('/{record}'),
            'edit' => Pages\EditUtilityModel::route('/{record}/edit'),
            'documents' => Pages\UtilityModelDocuments::route('/{record}/document'),
            'tasks' => Pages\ManageUtilityModelTasks::route('/{record}/tasks')
        ];
    }
}
