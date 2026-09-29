<?php

namespace App\Filament\Widgets;

use App\Models\Product;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class RecentProducts extends BaseWidget
{
    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Product::query()
                    ->with(['category', 'brand', 'images'])
                    ->latest()
                    ->limit(5)
            )
            ->columns([
                ImageColumn::make('images')
                    ->disk('public')
                    ->label('Image')
                    ->circular()
                    ->size(40)
                    ->getStateUsing(fn ($record) => $record->images->first()?->image),

                TextColumn::make('name')
                    ->searchable()
                    ->limit(30),

                TextColumn::make('category.name')
                    ->label('Category')
                    ->placeholder('—'),

                TextColumn::make('price')
                    ->money('INR'),

                TextColumn::make('stock_quantity')
                    ->label('Stock')
                    ->badge()
                    ->color(fn (int $state) => match (true) {
                        $state <= 0 => 'danger',
                        $state < 10 => 'warning',
                        default => 'success',
                    }),

                TextColumn::make('created_at')
                    ->label('Added')
                    ->since(),
            ])
            ->paginated(false);
    }
}