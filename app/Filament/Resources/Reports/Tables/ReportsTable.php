<?php

namespace App\Filament\Resources\Reports\Tables;

use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ReportsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('reportable_type')
                    ->label('Content type')
                    ->formatStateUsing(fn (string $state): string => class_basename($state)),
                TextColumn::make('reportedBy.name')
                    ->label('Reported by')
                    ->searchable(),
                TextColumn::make('reason')
                    ->badge(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'reviewed' => 'gray',
                        'actioned' => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('created_at')
                    ->label('Reported on')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'reviewed' => 'Reviewed',
                        'actioned' => 'Actioned',
                    ]),
                SelectFilter::make('reason')
                    ->options([
                        'spam' => 'Spam',
                        'abuse' => 'Abusive content',
                        'nudity' => 'Nudity / sexual content',
                        'harassment' => 'Harassment',
                        'other' => 'Other',
                    ]),
            ])
            ->recordActions([
                Action::make('removeContent')
                    ->label('Remove content')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->visible(fn ($record) => $record->status !== 'actioned')
                    ->action(function ($record) {
                        $record->reportable()->update(['status' => 'removed']);
                        $record->update(['status' => 'actioned']);

                        Notification::make()
                            ->title('Content removed and report actioned')
                            ->success()
                            ->send();
                    }),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->defaultSort('created_at', 'desc');
    }
}