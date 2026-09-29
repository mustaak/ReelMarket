<?php

namespace App\Filament\Resources\Conversations\Tables;

use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ConversationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->recordUrl(null)
            ->columns([
                TextColumn::make('id')->sortable(),
                TextColumn::make('users.name')
                    ->label('Participants')
                    ->listWithLineBreaks()
                    ->limitList(2),
                TextColumn::make('messages_count')
                    ->counts('messages')
                    ->label('Total messages'),
                TextColumn::make('created_at')
                    ->label('Started on')
                    ->dateTime()
                    ->sortable(),
            ])
            ->recordActions([
                Action::make('viewMessages')
                    ->label('View messages')
                    ->icon('heroicon-o-chat-bubble-left-right')
                    ->color('info')
                    ->modalHeading('Conversation messages')
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Close')
                    ->modalContent(fn ($record) => view('filament.resources.conversations.messages-modal', [
                        'messages' => $record->messages()->with('sender')->oldest()->limit(50)->get(),
                    ])),
                DeleteAction::make()
                    ->label('Delete conversation'),
            ])
            ->defaultSort('created_at', 'desc');
    }
}