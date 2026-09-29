<?php

namespace App\Filament\Resources\Comments\Tables;

use Filament\Actions\DeleteAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use App\Models\Comment;


class CommentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
        ->recordUrl(null)
            ->columns([
                TextColumn::make('id')->sortable(),
                TextColumn::make('commentable_type')
                    ->label('On')
                    ->formatStateUsing(fn (string $state): string => class_basename($state))
                    ->badge(),
                TextColumn::make('commentable_id')
                ->label('Content')
                ->state(function (Comment $record): string {
                    $content = $record->commentable;

                    if (! $content) {
                        return 'Deleted content';
                    }

                    $text = $content instanceof \App\Models\Post
                        ? $content->content
                        : $content->caption;

                    return \Illuminate\Support\Str::limit($text ?? '—', 40);
                })
                ->wrap(),
                TextColumn::make('user.name')
                    ->label('Commented by')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('content')
                    ->label('Comment')
                    ->limit(50)
                    ->searchable()
                    ->wrap(),
                TextColumn::make('created_at')
                    ->label('Commented on')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('commentable_type')
                    ->label('Content type')
                    ->options([
                        'App\\Models\\Post' => 'Post',
                        'App\\Models\\Reel' => 'Reel',
                    ]),
            ])
            ->recordActions([
                DeleteAction::make(),
            ])
            ->defaultSort('created_at', 'desc');
    }
}