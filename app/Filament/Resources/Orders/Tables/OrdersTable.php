<?php

namespace App\Filament\Resources\Orders\Tables;

use App\Models\Order;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Validation\ValidationException;

class OrdersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('order_number')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('customer_name')
                    ->searchable(),
                TextColumn::make('items_count')
                    ->counts('items')
                    ->label('Items'),
                TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => Order::STATUSES[$state] ?? $state)
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'processing' => 'info',
                        'shipped' => 'primary',
                        'delivered' => 'success',
                        'cancelled' => 'danger',
                        default => 'gray',
                    })
                    ->sortable(),
                TextColumn::make('payment_status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'paid' => 'success',
                        'refunded' => 'gray',
                        default => 'warning', // unpaid / pending
                    }),
                TextColumn::make('total')
                    ->money('INR')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options(Order::STATUSES),
                SelectFilter::make('payment_status')
                    ->options([
                        'unpaid' => 'Unpaid',
                        'paid' => 'Paid',
                        'refunded' => 'Refunded',
                    ]),
            ])
            ->recordActions([
                ActionGroup::make([
                    Action::make('changeStatus')
                        ->label('Update order status')
                        ->icon('heroicon-o-arrow-path')
                        ->visible(fn (Order $record) => $record->availableStatusTransitions() !== [])
                        ->schema(fn (Order $record) => [
                            Select::make('status')
                                ->label('New status')
                                ->options($record->availableStatusTransitions())
                                ->required(),
                        ])
                        ->action(function (Order $record, array $data) {
                            try {
                                $record->transitionTo($data['status']);

                                Notification::make()
                                    ->title('Order status updated')
                                    ->success()
                                    ->send();
                            } catch (ValidationException $e) {
                                Notification::make()
                                    ->title('Could not update status')
                                    ->body($e->getMessage())
                                    ->danger()
                                    ->send();
                            }
                        }),

                    Action::make('changePaymentStatus')
                        ->label('Update payment status')
                        ->icon('heroicon-o-currency-rupee')
                        ->visible(fn (Order $record) => $record->status !== 'cancelled')
                        ->schema([
                            Select::make('payment_status')
                                ->label('New payment status')
                                ->options([
                                    'unpaid' => 'Unpaid',
                                    'paid' => 'Paid',
                                    'refunded' => 'Refunded',
                                ])
                                ->required(),
                        ])
                        ->fillForm(fn (Order $record) => [
                            'payment_status' => $record->payment_status,
                        ])
                        ->action(function (Order $record, array $data) {
                            $record->update([
                                'payment_status' => $data['payment_status'],
                            ]);

                            Notification::make()
                                ->title('Payment status updated')
                                ->success()
                                ->send();
                        }),

                    EditAction::make(),
                ])
                    ->label('Actions')
                    ->icon('heroicon-m-ellipsis-vertical')
                    ->color('gray'),
            ]);
    }
}