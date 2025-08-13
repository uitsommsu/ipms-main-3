<?php

namespace App\Filament\Resources;

use Filament\Forms;
use App\Models\User;
use Filament\Tables;
use App\Models\Patent;
use Filament\Forms\Set;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use App\Enums\PatentStatusEnum;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Select;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\PatentResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\PatentResource\RelationManagers;

class PatentResource extends Resource
{
    protected static ?string $model = Patent::class;

    protected static ?string $navigationIcon = 'heroicon-o-bolt';

    protected static ?string $recordTitleAttribute = 'invention';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->whereHas('proponents', function ($q) {
            $q->where('user_id', auth()->id());
        })->latest();
    }


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

                Forms\Components\FileUpload::make('document')
                    ->label('Technology Disclosure Form (pdf format)')
                    ->disk('local')
                    ->directory('documents')
                    ->acceptedFileTypes(['application/pdf'])  // Limit to PDF files
                    ->maxSize(5120)  // Optional: Set max size (e.g., 1MB = 1024KB)
                    ->columnSpanFull()
                    ->visibleOn('create')
                    ->required(),

                Forms\Components\FileUpload::make('images')
                    ->image()
                    ->multiple()
                    ->imageEditor()
                    ->disk('public')
                    ->directory('photos')
                    ->visibility('public')
                    ->visibleOn('edit')
                    ->columnSpanFull(),

                Forms\Components\Section::make('Attach Inventors account')
                    ->schema([
                        Select::make('proponents')
                            ->label("Link this Patent to your co-inventors")
                            ->multiple()
                            ->relationship('proponents', 'name', function ($query) {
                                return $query->where('users.id', '<>', auth()->id()); // Modify query to exclude the id of the aunthenticated user
                            })

                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
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
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Date of Submission')
                    ->date()
                    ->toggleable()
                    ->badge(),
                Tables\Columns\TextColumn::make('publication_date')
                    ->date()
                    ->toggleable()
                    ->badge()
                    ->color('success'),
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
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make()
                        ->visible(fn(Patent $record): bool => $record->status === PatentStatusEnum::SUBMITTED_UITSO),
                    Tables\Actions\Action::make('Track Progress')
                        ->icon('heroicon-o-queue-list')
                        ->url(fn(Patent $record): string =>  self::getUrl('track-progress', ['record' => $record])),
                    Tables\Actions\Action::make('Upload Document')
                        ->icon('heroicon-o-arrow-up-tray')
                        ->visible(fn(Patent $record): bool => $record->status !== PatentStatusEnum::APPROVED)
                        ->url(fn(Patent $record): string =>  self::getUrl('document-submission', ['record' => $record])),
                ])
                    ->visible(fn(Patent $record): bool => $record->status !== PatentStatusEnum::ABANDONED && $record->status !== PatentStatusEnum::WITHDRAWN)
            ])
            ->bulkActions([]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPatents::route('/'),
            'create' => Pages\CreatePatent::route('/create'),
            'edit' => Pages\EditPatent::route('/{record}/edit'),
            'track-progress' => Pages\PatentTrackProgress::route('/{record}/track-progress'),
            'document-submission' => Pages\PatentDocumentSubmission::route('/{record}/document-submission'),
        ];
    }
}
