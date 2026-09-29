<?php

namespace App\Filament\Widgets;

use App\Models\Post;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class TopPostsWidget extends BaseWidget
{
    protected static ?string $heading = 'Top posts';

    protected int | string | array $columnSpan = [
        'md' => 2,
    ];

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Post::query()
                    ->where('status', 'published')
                    ->whereHas('user', fn ($q) => $q->whereDoesntHave('roles', fn ($q2) => $q2->where('name', 'Super Admin')))
                    ->orderByDesc('likes_count')
            )
            ->columns([
                TextColumn::make('user.name')
                    ->label('User'),
                TextColumn::make('content')
                    ->limit(35)
                    ->wrap(),
                TextColumn::make('likes_count')
                    ->label('Likes')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('comments_count')
                    ->label('Comments')
                    ->numeric()
                    ->sortable(),
            ])
            ->paginated([5]);
    }
}