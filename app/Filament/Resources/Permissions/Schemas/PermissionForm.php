<?php

namespace App\Filament\Resources\Permissions\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class PermissionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')
                ->label('Permission Name')
                ->required()
                ->unique(ignoreRecord: true)
                ->maxLength(255)
                ->rule('regex:/^[a-z]+_[a-z]+$/')
                ->helperText('Format: action_module — e.g. view_products, create_orders')
                ->placeholder('e.g. view_products'),
        ]);
    }
}
