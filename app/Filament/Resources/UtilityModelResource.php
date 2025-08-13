<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Set;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use App\Models\UtilityModel;
use Filament\Resources\Resource;
use App\Enums\UtilityModelStatusEnum;
use Filament\Forms\Components\Select;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\UtilityModelResource\Pages;
use App\Filament\Resources\UtilityModelResource\RelationManagers;

class UtilityModelResource extends Resource
{
    protected static ?string $model = UtilityModel::class;

    protected static ?string $navigationIcon = 'heroicon-o-circle-stack';

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
                    ->columnSpanFull()
                    ->visibleOn('edit'),

                Forms\Components\Section::make('Attach Inventors account')
                    ->schema([
                        Select::make('proponents')
                            ->label("Link this Utility Model to your co-inventors")
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
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Date of Submission')
                    ->date()
                    ->toggleable()
                    ->badge(),
                Tables\Columns\TextColumn::make('publication_date')
                    ->date()
                    ->toggleable()
                    ->badge(),
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
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make()
                        ->visible(fn(UtilityModel $record): bool => $record->status === UtilityModelStatusEnum::SUBMITTED_UITSO),
                    Tables\Actions\Action::make('Track Progress')
                        ->icon('heroicon-o-queue-list')
                        ->url(fn(UtilityModel $record): string =>  self::getUrl('track-progress', ['record' => $record])),
                    Tables\Actions\Action::make('Upload Document')
                        ->icon('heroicon-o-arrow-up-tray')
                        ->visible(fn(UtilityModel $record): bool => $record->status !== UtilityModelStatusEnum::APPROVED)
                        ->url(fn(UtilityModel $record): string =>  self::getUrl('document-submission', ['record' => $record])),

                ])->visible(fn(UtilityModel $record): bool => $record->status !== UtilityModelStatusEnum::ABANDONED && $record->status !== UtilityModelStatusEnum::WITHDRAWN)
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
            'index' => Pages\ListUtilityModels::route('/'),
            'create' => Pages\CreateUtilityModel::route('/create'),
            'edit' => Pages\EditUtilityModel::route('/{record}/edit'),
            'track-progress' => Pages\UtilityModelTrackProgress::route('/{record}/track-progress'),
            'document-submission' => Pages\UtilityModelDocumentSubmission::route('/{record}/document-submission'),
        ];
    }
}
