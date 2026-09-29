<?php

namespace App\Filament\Resources\Follows\Tables;

use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Table;

class FollowsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                // Follower Avatar (UserProfile model se profile_picture)
                ImageColumn::make('follower.profile.profile_picture')
                    ->label('Follower Avatar')
                    ->circular()
                    ->disk('public') // Aapka file storage disk (public / s3)
                    ->defaultImageUrl('https://ui-avatars.com/api/?name=Follower'),

                // Follower Name (User model se name)
                TextColumn::make('follower.name')
                    ->label('Follower')
                    ->searchable()
                    ->sortable(),

                // Following Avatar (UserProfile model se profile_picture)
                ImageColumn::make('following.profile.profile_picture')
                    ->label('Following Avatar')
                    ->circular()
                    ->disk('public')
                    ->defaultImageUrl('https://ui-avatars.com/api/?name=Following'),

                // Following Name (User model se name)
                TextColumn::make('following.name')
                    ->label('Following')
                    ->searchable()
                    ->sortable(),

                // Follow Status
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'accepted' => 'success',
                        'pending' => 'warning',
                        'blocked' => 'danger',
                        default => 'gray',
                    })
                    ->searchable(),

                TextColumn::make('created_at')
                    ->label('Followed Date')
                    ->dateTime()
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}