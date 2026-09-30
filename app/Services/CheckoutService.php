<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class CheckoutService
{
    public function __construct(private CartService $cartService) {}

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
    public function placeOrder(User $user, array $shippingAddress): Order
    {
        $quantities = $this->cartService->quantities();

        if ($quantities === []) {
            throw ValidationException::withMessages(['cart' => 'Your cart is empty.']);
        }

        $order = DB::transaction(function () use ($user, $shippingAddress, $quantities): Order {
            $products = Product::query()
                ->whereIn('id', array_keys($quantities))
                ->orderBy('id')
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            if ($products->count() !== count($quantities)) {
                throw ValidationException::withMessages(['cart' => 'A product in your cart is no longer available.']);
            }

            $lines = [];
            $subtotal = 0.0;

            foreach ($quantities as $productId => $quantity) {
                $product = $products->get($productId);
                $availableQuantity = $this->cartService->limitFor($product);

                if ($availableQuantity < $quantity) {
                    throw ValidationException::withMessages([
                        'cart' => "{$product->name} no longer has enough stock. Update your cart and try again.",
                    ]);
                }

                $unitPrice = (float) $product->final_price;
                $lineTotal = round($unitPrice * $quantity, 2);
                $subtotal += $lineTotal;
                $lines[] = [
                    'product' => $product,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'line_total' => $lineTotal,
                ];
            }

            $subtotal = round($subtotal, 2);
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
                'shipping_fee' => 0,
                'tax_amount' => 0,
                'total' => $subtotal,
            ]);

            foreach ($lines as $line) {
                $product = $line['product'];
                $order->items()->create([
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'sku' => $product->sku,
                    'unit_price' => $line['unit_price'],
                    'quantity' => $line['quantity'],
                    'line_total' => $line['line_total'],
                ]);

                $product->decrement('stock_quantity', $line['quantity']);
            }

            return $order;
        }, 3);

        $this->cartService->clear();

        return $order;
    }
}
