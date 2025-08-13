<?php

namespace App\Filament\Management\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Patent;
use Filament\Forms\Set;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use App\Enums\PatentStatusEnum;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Forms\FormsComponent;
use Filament\Forms\Components\Select;
use Filament\Support\Enums\FontWeight;
use Filament\Forms\Components\Fieldset;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\Group;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\Component;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ImageEntry;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Management\Resources\PatentResource\Pages;
use App\Filament\Resources\PatentResource as ResourcesPatentResource;
use App\Filament\Management\Resources\PatentResource\RelationManagers;

class PatentResource extends Resource
{
    protected static ?string $model = Patent::class;

    protected static ?int $navigationSort = 2;

    protected static ?string $navigationIcon = 'heroicon-o-bolt';

    protected static ?string $recordTitleAttribute = 'invention';



    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('invention')
                    ->maxLength(500)
                    ->required()
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn(Set $set, ?string $state) => $set('slug', Str::slug($state)))
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('slug')
                    ->readOnly()
                    ->unique(Patent::class, 'slug', ignoreRecord: true)
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('inventors')
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
                Forms\Components\TextInput::make('patent_number')
                    ->maxLength(255),
                Forms\Components\Select::make('status')
                    ->required()
                    ->options(PatentStatusEnum::selectable()),
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
            ->query(Patent::notDowngraded()->latest())
            ->columns([
                Tables\Columns\TextColumn::make('invention')
                    ->wrap()
                    ->searchable(),
                Tables\Columns\TextColumn::make('inventors')
                    ->wrap()
                    ->searchable(),
                Tables\Columns\TextColumn::make('patent_number')
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
                            PatentStatusEnum::APPROVED => 'success',
                            PatentStatusEnum::FORMALITY_EXAMINATION_REPORT => 'warning',
                            PatentStatusEnum::SUBSEQUENT_EXAMINATION_REPORT => 'warning',
                            PatentStatusEnum::NOTICE_OF_PUBLICATION => 'warning',
                            PatentStatusEnum::SUBSTANTIVE_EXAMINATION_REPORT => 'warning',
                            PatentStatusEnum::PAYMENT_OF_ISSUANCE_OF_CERTIFICATE => 'warning',
                            PatentStatusEnum::NOTICE_OF_ISSUANCE => 'warning',
                            PatentStatusEnum::CLAIMING_OF_CERTIFICATE => 'warning',
                            PatentStatusEnum::ISSUANCE_OF_CERTIFICATE => 'warning',
                            PatentStatusEnum::SUBMITTED_IPOPHL => 'info',
                            PatentStatusEnum::SUBMITTED_UITSO => 'gray',
                            PatentStatusEnum::ABANDONED => 'danger',
                            PatentStatusEnum::WITHDRAWN => 'danger',
                        };
                    }),


            ])
            ->filters([
                SelectFilter::make('status')
                    ->options(PatentStatusEnum::class)
            ])
            ->actions([
                ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make()
                        ->visible(fn(Patent $record): bool => $record->status !== PatentStatusEnum::ABANDONED && $record->status !== PatentStatusEnum::WITHDRAWN),
                    Tables\Actions\Action::make('Documents')
                        ->icon('heroicon-o-document')
                        ->url(fn(Patent $record): string =>  self::getUrl('documents', ['record' => $record]))
                        ->visible(fn(Patent $record): bool => $record->status !== PatentStatusEnum::ABANDONED && $record->status !== PatentStatusEnum::WITHDRAWN && $record->status !== PatentStatusEnum::APPROVED),
                    Tables\Actions\Action::make('Tasks')
                        ->icon('heroicon-o-numbered-list')
                        ->visible(fn(Patent $record): bool => $record->status !== PatentStatusEnum::WITHDRAWN && $record->status !== PatentStatusEnum::APPROVED)
                        ->url(fn(Patent $record): string =>  self::getUrl('tasks', ['record' => $record])),
                    Tables\Actions\Action::make('Refile')
                        ->icon('heroicon-o-viewfinder-circle')
                        ->requiresConfirmation()
                        ->modalHeading('Confirm Refilement')
                        ->modalDescription('This will refile the patent application. Proceed?')
                        ->modalSubmitActionLabel('Yes, Refile')
                        ->action(function (Patent $record) {
                            $record->update([
                                'withdrawn_at' => null,
                                'status' => PatentStatusEnum::SUBMITTED_IPOPHL,
                            ]);
                            Notification::make()->success()->title('Refile Process Completed')->send();
                        })
                        ->visible(fn(Patent $record): bool => $record->status == PatentStatusEnum::WITHDRAWN),
                ])
                    ->visible(fn(Patent $record): bool => $record->status !== PatentStatusEnum::ABANDONED),



            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->searchPlaceholder('Search (Inventions, Inventors, Patent Number)');
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
                                    TextEntry::make('invention'),
                                    TextEntry::make('inventors'),
                                    Grid::make()
                                        ->schema([
                                            TextEntry::make('patent_number'),
                                            TextEntry::make('status')
                                                ->badge()
                                                ->color(static function ($record) {
                                                    return match ($record->status) {
                                                        PatentStatusEnum::APPROVED => 'success',
                                                        PatentStatusEnum::FORMALITY_EXAMINATION_REPORT => 'warning',
                                                        PatentStatusEnum::SUBSEQUENT_EXAMINATION_REPORT => 'warning',
                                                        PatentStatusEnum::NOTICE_OF_PUBLICATION => 'warning',
                                                        PatentStatusEnum::SUBSTANTIVE_EXAMINATION_REPORT => 'warning',
                                                        PatentStatusEnum::PAYMENT_OF_ISSUANCE_OF_CERTIFICATE => 'warning',
                                                        PatentStatusEnum::NOTICE_OF_ISSUANCE => 'warning',
                                                        PatentStatusEnum::CLAIMING_OF_CERTIFICATE => 'warning',
                                                        PatentStatusEnum::ISSUANCE_OF_CERTIFICATE => 'warning',
                                                        PatentStatusEnum::SUBMITTED_IPOPHL => 'info',
                                                        PatentStatusEnum::SUBMITTED_UITSO => 'gray',
                                                        PatentStatusEnum::ABANDONED => 'danger',
                                                        PatentStatusEnum::WITHDRAWN => 'danger',
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
            PatentResource\Widgets\PatentStatsOverview::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPatents::route('/'),
            'create' => Pages\CreatePatent::route('/create'),
            'view' => Pages\ViewPatent::route('/{record}'),
            'edit' => Pages\EditPatent::route('/{record}/edit'),
            'documents' => Pages\PatentDocuments::route('/{record}/document'),
            'tasks' => Pages\ManagePatentTasks::route('/{record}/tasks')
        ];
    }
}
