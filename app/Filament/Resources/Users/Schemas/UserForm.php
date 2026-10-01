<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')
                ->required()
                ->maxLength(255),

            TextInput::make('email')
                ->email()
                ->required()
                ->unique(ignoreRecord: true)
                ->maxLength(255),

            TextInput::make('password')
                ->password()
                ->required(fn (string $operation): bool => $operation === 'create')
                ->dehydrated(fn ($state) => filled($state))
                ->confirmed()
                ->maxLength(255),

            TextInput::make('password_confirmation')
                ->password()
                ->required(fn (string $operation): bool => $operation === 'create')
                ->dehydrated(false)
                ->maxLength(255),

            Toggle::make('status')
                ->label('Active')
                ->default(true)
                ->visible(fn (): bool => auth()->user()?->can('manage_user_status') ?? false),

            Select::make('roles')
                ->relationship(
                    name: 'roles',
                    titleAttribute: 'name',
                    modifyQueryUsing: fn (Builder $query): Builder => self::assignableRolesQuery($query),
                )
                ->multiple()
                ->preload()
                ->searchable(),
        ]);
    }

    public static function assignableRolesQuery(Builder $query): Builder
    {
        $user = auth()->user();

        if ($user?->isSuperAdmin()) {
            return $query;
        }

        if ($user?->hasRole('Manager')) {
            return $query->where('name', 'User');
        }

        return $query->where('name', '!=', 'Super Admin');
    }
}
