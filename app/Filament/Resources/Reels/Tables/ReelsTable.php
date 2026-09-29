<?php

namespace App\Filament\Resources\Reels\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ReelsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('thumbnail')
                    ->label('Thumbnail')
                    ->disk('public'),
                TextColumn::make('user.name')
                    ->label('User')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('product.name')
                    ->label('Product')
                    ->placeholder('—')
                    ->toggleable(),
                TextColumn::make('caption')
                    ->limit(40)
                    ->wrap(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'published' => 'success',
                        'flagged' => 'warning',
                        'removed' => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('views_count')
                    ->label('Views')
                    ->numeric()
                    ->toggleable()
                    ->sortable(),
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