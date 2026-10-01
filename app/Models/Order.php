<?php

namespace App\Models;

use Database\Factories\OrderFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Validation\ValidationException;

class Order extends Model
{
    public const STATUSES = [
        'pending' => 'Pending',
        'processing' => 'Processing',
        'shipped' => 'Shipped',
        'delivered' => 'Delivered',
        'cancelled' => 'Cancelled',
    ];

    /** @use HasFactory<OrderFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'order_number',
        'status',
        'payment_method',
        'payment_status',
        'customer_name',
        'customer_email',
        'customer_phone',
        'address_line1',
        'address_line2',
        'city',
        'state',
        'postal_code',
        'country',
        'subtotal',
        'coupon_code',
        'discount_amount',
        'shipping_fee',
        'tax_amount',
        'total',
    ];

    protected function casts(): array
    {
        return [
            'subtotal' => 'decimal:2',
            'discount_amount' => 'decimal:2',
            'shipping_fee' => 'decimal:2',
            'tax_amount' => 'decimal:2',
            'total' => 'decimal:2',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function availableStatusTransitions(): array
    {
        return match ($this->status) {
            'pending' => ['processing' => self::STATUSES['processing'], 'cancelled' => self::STATUSES['cancelled']],
            'processing' => ['shipped' => self::STATUSES['shipped'], 'cancelled' => self::STATUSES['cancelled']],
            'shipped' => ['delivered' => self::STATUSES['delivered']],
            default => [],
        };
    }

    public function transitionTo(string $status): void
    {
        if ($status === $this->status) {
            return;
        }

        if (! array_key_exists($status, $this->availableStatusTransitions())) {
            throw ValidationException::withMessages([
                'status' => 'This order status transition is not allowed.',
            ]);
        }

        $this->update(['status' => $status]);
    }
}
