<?php

namespace App\Filament\Resources\Posts\Tables;

use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class PostsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                TextColumn::make('user.name')
                    ->label('User')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('product.name')
                    ->label('Product')
                    ->searchable()
                    ->placeholder('—')
                    ->toggleable(),
                TextColumn::make('content')
                    ->label('Content')
                    ->limit(40)
                    ->searchable()
                    ->wrap()
                    ->toggleable(),
                TextColumn::make('visibility')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'public' => 'success',
                        'followers' => 'gray',
                        default => 'gray',
                    }),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'published' => 'success',
                        'flagged' => 'warning',
                        'removed' => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('likes_count')
                    ->label('Likes')
                    ->numeric()
                    ->toggleable()
                    ->sortable(),
                TextColumn::make('comments_count')
                    ->label('Comments')
                    ->numeric()
                    ->toggleable()
                    ->sortable(),
                TextColumn::make('shares_count')
                    ->label('Shares')
                    ->numeric()
                    ->toggleable()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Posted on')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'published' => 'Published',
                        'flagged' => 'Flagged',
                        'removed' => 'Removed',
                    ]),
                SelectFilter::make('visibility')
                    ->options([
                        'public' => 'Public',
                        'followers' => 'Followers only',
                    ]),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}