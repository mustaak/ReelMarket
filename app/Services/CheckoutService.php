<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class CheckoutService
{
    public function __construct(private CartService $cartService) {}

    public function couponDiscount(?string $couponCode, float $subtotal): float
    {
        $couponCode = strtoupper(trim($couponCode ?? ''));

        if ($couponCode === '') {
            return 0;
        }

        if ($couponCode !== 'WELCOME10') {
            throw ValidationException::withMessages(['couponCode' => 'Invalid coupon code.']);
        }

        return round($subtotal * 0.10, 2);
    }

    /**
     * @param array{
     *     customerName: string,
     *     customerEmail: string,
     *     customerPhone: string,
     *     addressLine1: string,
     *     addressLine2: ?string,
     *     city: string,
     *     state: string,
     *     postalCode: string,
     *     country: string
     * } $shippingAddress
     */
    public function placeOrder(User $user, array $shippingAddress, ?string $couponCode = null): Order
    {
        $quantities = $this->cartService->quantities();

        if ($quantities === []) {
            throw ValidationException::withMessages(['cart' => 'Your cart is empty.']);
        }

        $order = DB::transaction(function () use ($user, $shippingAddress, $quantities, $couponCode): Order {
            $productIds = [];
            $variantIds = [];

            foreach (array_keys($quantities) as $lineKey) {
                $variantId = CartService::variantIdFromLineKey($lineKey);

                if ($variantId) {
                    $variantIds[] = $variantId;
                } elseif (is_int($lineKey)) {
                    $productIds[] = $lineKey;
                }
            }

            $variants = ProductVariant::query()
                ->whereKey($variantIds)
                ->orderBy('id')
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            if ($variants->count() !== count($variantIds)) {
                throw ValidationException::withMessages(['cart' => 'A product in your cart is no longer available.']);
            }

            $productIds = array_values(array_unique(array_merge(
                $productIds,
                $variants->pluck('product_id')->map(fn ($id) => (int) $id)->all(),
            )));

            $products = Product::query()
                ->whereIn('id', $productIds)
                ->with('variants')
                ->orderBy('id')
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            if ($products->count() !== count($productIds)) {
                throw ValidationException::withMessages(['cart' => 'A product in your cart is no longer available.']);
            }

            $lines = [];
            $subtotal = 0.0;

            foreach ($quantities as $lineKey => $quantity) {
                $variantId = CartService::variantIdFromLineKey($lineKey);
                $variant = $variantId ? $variants->get($variantId) : null;
                $productId = $variant?->product_id ?? (is_int($lineKey) ? $lineKey : null);
                $product = $productId ? $products->get($productId) : null;

                if (! $product || ($variantId && ! $variant)) {
                    throw ValidationException::withMessages(['cart' => 'A product in your cart is no longer available.']);
                }

                $availableQuantity = $this->cartService->limitFor($product, $variant);

                if ($availableQuantity < $quantity) {
                    throw ValidationException::withMessages([
                        'cart' => ($variant?->label ?? $product->name).' no longer has enough stock. Update your cart and try again.',
                    ]);
                }

                $unitPrice = (float) ($variant?->price ?? $product->final_price);
                $lineTotal = round($unitPrice * $quantity, 2);
                $subtotal += $lineTotal;
                $lines[] = [
                    'product' => $product,
                    'variant' => $variant,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'line_total' => $lineTotal,
                ];
            }

            $subtotal = round($subtotal, 2);
            $normalizedCouponCode = strtoupper(trim($couponCode ?? ''));
            $discountAmount = $this->couponDiscount($normalizedCouponCode, $subtotal);
            $order = Order::query()->create([
                'user_id' => $user->id,
                'order_number' => 'ORD-'.now()->format('Ymd').'-'.Str::upper(Str::random(6)),
                'status' => 'pending',
                'payment_method' => 'cod',
                'payment_status' => 'pending',
                'customer_name' => $shippingAddress['customerName'],
                'customer_email' => $shippingAddress['customerEmail'],
                'customer_phone' => $shippingAddress['customerPhone'],
                'address_line1' => $shippingAddress['addressLine1'],
                'address_line2' => $shippingAddress['addressLine2'] ?? null,
                'city' => $shippingAddress['city'],
                'state' => $shippingAddress['state'],
                'postal_code' => $shippingAddress['postalCode'],
                'country' => $shippingAddress['country'],
                'subtotal' => $subtotal,
                'coupon_code' => $normalizedCouponCode !== '' ? $normalizedCouponCode : null,
                'discount_amount' => $discountAmount,
                'shipping_fee' => 0,
                'tax_amount' => 0,
                'total' => round($subtotal - $discountAmount, 2),
            ]);

            foreach ($lines as $line) {
                $product = $line['product'];
                $order->items()->create([
                    'product_id' => $product->id,
                    'variant_id' => $variant?->id,
                    'variant_label' => $variant?->label,
                    'variant_options' => $variant?->options,
                    'product_name' => $product->name,
                    'sku' => $variant?->sku ?? $product->sku,
                    'unit_price' => $line['unit_price'],
                    'quantity' => $line['quantity'],
                    'line_total' => $line['line_total'],
                ]);

                if ($line['variant']) {
                    $line['variant']->decrement('stock_quantity', $line['quantity']);
                } else {
                    $product->decrement('stock_quantity', $line['quantity']);
                }
            }

            return $order;
        }, 3);

        $this->cartService->clear();

        return $order;
    }
}
