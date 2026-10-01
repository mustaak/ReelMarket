<?php

namespace App\Filament\Resources\Reports\Tables;

use App\Models\Post;
use App\Models\Reel;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ReportsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('thumbnail')
                    ->label('')
                    ->disk('public')
                    ->square()
                    ->state(function ($record): ?string {
                        $content = $record->reportable;

                        if (! $content) {
                            return null;
                        }

                        if ($content instanceof Post) {
                            return $content->images->first()?->image_path;
                        }

                        return $content->thumbnail;
                    }),

                TextColumn::make('reportedBy.name')
                    ->label('Reported by')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('reportable_type')
                    ->label('Content type')
                    ->formatStateUsing(fn (string $state): string => class_basename($state))
                    ->badge()
                    ->color(fn (string $state): string => $state === Post::class ? 'info' : 'warning'),

                TextColumn::make('reportable_id')
                    ->label('Reported content')
                    ->state(function ($record): string {
                        $content = $record->reportable;

                        if (! $content) {
                            return 'Deleted content';
                        }

                        $text = $content instanceof Post ? $content->content : $content->caption;

                        return \Illuminate\Support\Str::limit($text ?? '—', 45);
                    })
                    ->wrap(),

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
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
                    ->visible(fn ($record) => $record->status !== 'actioned' && $record->reportable !== null)
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
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}