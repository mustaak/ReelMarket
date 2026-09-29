<?php

namespace App\Filament\Resources\Permissions\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Spatie\Permission\Models\Permission;
use Filament\Tables\Filters\SelectFilter;


class PermissionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->sortable(),
                TextColumn::make('name')->searchable()->sortable(),
                TextColumn::make('module')
                    ->label('Module')
                    ->state(fn (Permission $record) => str($record->name)->afterLast('_')->ucfirst())
                    ->badge(),
                TextColumn::make('roles_count')->counts('roles')->label('Used in Roles'),
                TextColumn::make('created_at')->dateTime()->sortable(),
            ])
            ->filters([
                SelectFilter::make('module')
                    ->options(
                        Permission::all()
                            ->pluck('name')
                            ->map(fn ($name) => str($name)->afterLast('_')->toString())
                            ->unique()
                            ->mapWithKeys(fn ($m) => [$m => ucfirst($m)])
                    )
                    ->query(function ($query, array $data) {
                        if (blank($data['value'] ?? null)) {
                            return $query;
                        }

                        return $query->where('name', 'like', '%_'.$data['value']);
                    }),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
