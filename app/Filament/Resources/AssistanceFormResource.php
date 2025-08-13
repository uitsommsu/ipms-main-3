<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\AssistanceForm;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\RichEditor;
use Illuminate\Database\Eloquent\Builder;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use App\Filament\Resources\AssistanceFormResource\Pages;


class AssistanceFormResource extends Resource
{
    protected static ?string $model = AssistanceForm::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $pluralModelLabel = 'Assistance Form';

    protected static ?string $navigationLabel = 'Assistance Form';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('user_id', auth()->id())->latest();
    }



    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                RichEditor::make('inquiry')
                    ->label('Describe the assistance you needed')
                    ->disableToolbarButtons([
                        'blockquote',
                        'strike',
                        'attachFiles',
                        'link',
                    ])
                    ->required(),
                Forms\Components\FileUpload::make('filename')
                    ->label('Technology Disclosure Form (pdf format)')
                    ->disk('local')
                    ->directory('documents')
                    ->acceptedFileTypes(['application/pdf'])  // Limit to PDF files
                    ->maxSize(5120)  // Optional: Set max size (e.g., 1MB = 1024KB)
                    ->columnSpanFull()
                    ->required(),
            ])->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('inquiry')
                    ->html()
                    ->wrap()
                    ->lineClamp(2),

                TextColumn::make('created_at')
                    ->label('Date of Inquiry')
                    ->dateTime('F j, Y'),
                IconColumn::make('is_responded')
                    ->boolean(),
                TextColumn::make('responded_at')
                    ->label('Date Responded')
                    ->dateTime('F j, Y'),

            ])
            ->filters([
                Filter::make('is_responded')
                    ->toggle()
                    ->query(fn(Builder $query): Builder => $query->where('is_responded', true))
            ])
            ->recordUrl(null)
            ->actions([
                ViewAction::make()
                    ->label('Response')
                    ->icon('heroicon-o-document-text')
                    ->modalHeading('UITSO Response')
                    ->infolist([
                        Section::make('Response')
                            ->description('Response for your inquiry')
                            ->schema([
                                TextEntry::make('response')
                                    ->label('')
                                    ->html(),
                                TextEntry::make('responded_at')
                                    ->label('Responded At')
                                    ->dateTime('F j, Y')
                            ])


                    ])
                    ->hidden(fn(AssistanceForm $record): bool
                    => $record->is_responded === false),
                Tables\Actions\EditAction::make()
                    ->hidden(fn(AssistanceForm $record): bool
                    => $record->is_responded === true),
                Tables\Actions\DeleteAction::make()
                    ->hidden(fn(AssistanceForm $record): bool
                    => $record->is_responded === true),
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
            'index' => Pages\ListAssistanceForms::route('/'),
            'create' => Pages\CreateAssistanceForm::route('/create'),
            'edit' => Pages\EditAssistanceForm::route('/{record}/edit'),
        ];
    }
}
