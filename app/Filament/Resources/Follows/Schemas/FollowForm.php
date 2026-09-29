<?php

namespace App\Filament\Resources\Follows\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;

class FollowForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('follower_id')
                    ->label('Follower (Who is following)')
                    ->relationship('follower', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),

                Select::make('following_id')
                    ->label('Following (Who is being followed)')
                    ->relationship('following', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),

                Select::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'accepted' => 'Accepted',
                        'blocked' => 'Blocked',
                    ])
                    ->default('accepted')
                    ->required(),
            ]);
    }
}
