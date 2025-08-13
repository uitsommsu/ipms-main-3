<?php

namespace App\Filament\Management\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\AssistanceForm;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\RichEditor;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Management\Resources\AssistanceFormResource\Pages;
use App\Filament\Management\Resources\AssistanceFormResource\RelationManagers;

class AssistanceFormResource extends Resource
{
    protected static ?string $model = AssistanceForm::class;

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function canCreate(): bool
    {
        return false; // Deny creation
    }


    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->latest();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                RichEditor::make('inquiry')
                    ->label('Describe the assistance you needed')
                    ->disabled()
                    ->disableToolbarButtons([
                        'blockquote',
                        'strike',
                        'attachFiles',
                        'link',
                    ]),

                RichEditor::make('response')
                    ->disableToolbarButtons([
                        'blockquote',
                        'strike',
                        'attachFiles',
                        'link',
                    ])
                    ->required()
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
                TextColumn::make('user.name')
                    ->label('Submitted By'),

                IconColumn::make('is_responded')
                    ->boolean(),
                TextColumn::make('created_at')
                    ->dateTime('F j, Y')
            ])
            ->filters([
                Filter::make('is_responded')
                    ->toggle()
                    ->query(fn(Builder $query): Builder => $query->where('is_responded', true))
            ])
            ->actions([
                Action::make('Download')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->action(function ($record) {
                        return response()->download(storage_path('app/' . $record->filename));
                    }),
                Tables\Actions\EditAction::make()->label('Response'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
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
