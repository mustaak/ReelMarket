<?php

namespace App\Filament\Widgets;

use App\Models\User;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class RecentUsers extends BaseWidget
{
    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->heading('Recent Users')
            ->query(
                User::query()->latest()->limit(5)
            )
            ->columns([
                TextColumn::make('name')->searchable(),
                TextColumn::make('email'),
                IconColumn::make('status')->boolean()->label('Active'),
                TextColumn::make('roles.name')->badge()->label('Role'),
                TextColumn::make('created_at')->dateTime()->label('Joined'),
            ])
            ->paginated(false);
    }
}