<?php

namespace App\Filament\Resources\Orders\Schemas;

use App\Models\Order;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class OrderForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                Section::make('Order details')
                    ->schema([
                        TextInput::make('order_number')
                            ->label('Order Number')
                            ->disabled()
                            ->dehydrated(false),

                        Select::make('status')
                            ->options(fn ($record) => $record
                                ? $record->availableStatusTransitions() + [
                                    $record->status => Order::STATUSES[$record->status],
                                ]
                                : Order::STATUSES)
                            ->required()
                            ->native(false),

                        TextInput::make('customer_name')
                            ->label('Customer Name')
                            ->disabled()
                            ->dehydrated(false),

                        TextInput::make('customer_email')
                            ->label('Customer Email')
                            ->disabled()
                            ->dehydrated(false),

                        TextInput::make('customer_phone')
                            ->label('Customer Phone')
                            ->disabled()
                            ->dehydrated(false),
                    ])
                    ->columns(2),

                Section::make('Payment')
                    ->schema([
                        TextInput::make('payment_method')
                            ->label('Payment Method')
                            ->disabled()
                            ->dehydrated(false),

                        TextInput::make('payment_status')
                            ->label('Payment Status')
                            ->disabled()
                            ->dehydrated(false),
                    ])
                    ->columns(2),

                Section::make('Shipping address')
                    ->schema([
                        TextInput::make('address_line1')
                            ->label('Address Line 1')
                            ->disabled()
                            ->dehydrated(false),

                        TextInput::make('address_line2')
                            ->label('Address Line 2')
                            ->disabled()
                            ->dehydrated(false),

                        TextInput::make('city')
                            ->disabled()
                            ->dehydrated(false),

                        TextInput::make('state')
                            ->disabled()
                            ->dehydrated(false),

                        TextInput::make('postal_code')
                            ->label('Postal Code')
                            ->disabled()
                            ->dehydrated(false),

                        TextInput::make('country')
                            ->disabled()
                            ->dehydrated(false),
                    ])
                    ->columns(2),

                Section::make('Order summary')
                    ->schema([
                        TextInput::make('subtotal')
                            ->prefix('₹')
                            ->disabled()
                            ->dehydrated(false),

                        TextInput::make('coupon_code')
                            ->label('Coupon')
                            ->disabled()
                            ->dehydrated(false),

                        TextInput::make('discount_amount')
                            ->label('Discount')
                            ->prefix('₹')
                            ->disabled()
                            ->dehydrated(false),

                        TextInput::make('shipping_fee')
                            ->label('Shipping')
                            ->prefix('₹')
                            ->disabled()
                            ->dehydrated(false),

                        TextInput::make('tax_amount')
                            ->label('Tax')
                            ->prefix('₹')
                            ->disabled()
                            ->dehydrated(false),

                        TextInput::make('total')
                            ->label('Grand Total')
                            ->prefix('₹')
                            ->disabled()
                            ->dehydrated(false),
                    ])
                    ->columns(2),

                Section::make('Order items')
                    ->schema([
                        Placeholder::make('items')
                            ->label('')
                            ->content(function ($record): string {
                                if (! $record) {
                                    return 'No items found.';
                                }

                                $record->loadMissing('items');

                                return $record->items
                                    ->map(function ($item): string {
                                        $variant = $item->variant_label
                                            ? " | {$item->variant_label}"
                                            : '';

                                        return sprintf(
                                            "%s%s\nSKU: %s | Qty: %d | Unit: ₹%s | Total: ₹%s",
                                            $item->product_name,
                                            $variant,
                                            $item->sku ?? '-',
                                            $item->quantity,
                                            number_format((float) $item->unit_price, 2),
                                            number_format((float) $item->line_total, 2),
                                        );
                                    })
                                    ->implode("\n\n");
                            }),
                    ]),
            ]);
    }
}