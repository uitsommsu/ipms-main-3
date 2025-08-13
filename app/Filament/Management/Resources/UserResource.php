<?php

namespace App\Filament\Management\Resources;

use Filament\Forms;
use App\Models\User;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Enums\UserRoleEnum;
use App\Enums\UserStatusEnum;
use Filament\Resources\Resource;
use App\Mail\UserVerificationMail;
use Filament\Tables\Actions\Action;
use Illuminate\Support\Facades\Mail;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Management\Resources\UserResource\Pages;
use App\Filament\Management\Resources\UserResource\RelationManagers;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?int $navigationSort = 4;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    public static function canCreate(): bool
    {
        return false; // Deny creation
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name'),
                TextColumn::make('email'),
                TextColumn::make('agency'),
                TextColumn::make('role')
                    ->badge()
                    ->color(fn (UserRoleEnum $state): string => match ($state) {
                        $state::ADMIN => 'success',
                        $state::MANAGEMENT => 'warning',
                        $state::RESEARCHER => 'gray'
                    }),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (UserStatusEnum $state): string => match ($state) {
                        $state::ACTIVE => 'success',
                        $state::UNVERIFIED => 'gray'
                    }),
            ])
            ->filters([
                SelectFilter::make('role')
                    ->options(UserRoleEnum::class),
                SelectFilter::make('status')
                    ->options(UserStatusEnum::class),
            ])
            ->actions([
                //Tables\Actions\EditAction::make(),
                Action::make('verify')
                    ->icon('heroicon-o-chevron-double-down')
                    ->action(function (User $record) {
                        $record->status = UserStatusEnum::ACTIVE;
                        $record->save();
                        Mail::to($record)->send(new UserVerificationMail($record));
                    })
                    ->hidden(fn (User $record): bool
                    => $record->status === UserStatusEnum::ACTIVE),

                Action::make('changeRole')
                    ->label('Change Role')
                    ->icon('heroicon-o-user-circle')
                    ->requiresConfirmation()
                    ->form([
                        Select::make('role')
                            ->options(UserRoleEnum::class)
                            ->default(function (User $record = null) {
                                // Return the current record's status or the first Enum value by default
                                return $record ? $record->role : UserRoleEnum::RESEARCHER;
                            })
                            ->required(),
                    ])
                    ->action(function (User $user, array $data) {
                        $user->role = $data['role'];
                        $user->save();
                    })
                    ->hidden(fn (User $record): bool
                    => $record->status === UserStatusEnum::UNVERIFIED),

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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
