<?php

namespace App\Livewire;

use App\Models\User;
use App\Services\CartService;
use App\Services\CheckoutService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class CheckoutPage extends Component
{
    public string $customerName = '';

    public string $customerEmail = '';

    public string $customerPhone = '';

    public string $addressLine1 = '';

    public string $addressLine2 = '';

    public string $city = '';

    public string $state = '';

    public string $postalCode = '';

    public string $country = 'India';

    public string $couponCode = '';

    public float $discount = 0;

    public function mount(CartService $cartService)
    {
        if ($cartService->items()->isEmpty()) {
            return $this->redirectRoute('cart.index');
        }

        $user = Auth::user();

        if ($user instanceof User) {
            $this->customerName = $user->name;
            $this->customerEmail = $user->email;
        }
    }

    public function applyCoupon(CartService $cartService, CheckoutService $checkoutService): void
    {
        $code = strtoupper(trim($this->couponCode));

        $this->resetErrorBag('couponCode');
        $this->discount = 0;

        if ($code === '') {
            $this->addError('couponCode', 'Enter a coupon code.');

            return;
        }

        try {
            $this->discount = $checkoutService->couponDiscount($code, (float) $cartService->items()->sum('total'));
        } catch (ValidationException $exception) {
            $this->addError('couponCode', $exception->errors()['couponCode'][0] ?? 'Invalid coupon code.');

            return;
        }

        $this->couponCode = $code;
        session()->flash('coupon_success', '10% discount applied successfully.');
    }

    public function placeOrder(CheckoutService $checkoutService)
    {
        $user = Auth::user();
        abort_unless($user instanceof User, 403);

        $validated = $this->validate([
            'customerName' => ['required', 'string', 'max:120'],
            'customerEmail' => ['required', 'email', 'max:255'],
            'customerPhone' => ['required', 'string', 'max:32'],
            'addressLine1' => ['required', 'string', 'max:255'],
            'addressLine2' => ['nullable', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:100'],
            'state' => ['required', 'string', 'max:100'],
            'postalCode' => ['required', 'string', 'max:32'],
            'country' => ['required', 'string', 'max:100'],
        ]);

        $order = $checkoutService->placeOrder($user, $validated, $this->couponCode);

        return $this->redirectRoute('orders.show', ['order' => $order->id]);
    }

    public function render(CartService $cartService)
    {
        $cartItems = $cartService->items();

        return view('livewire.checkout-page', [
            'cartItems' => $cartItems,
            'subtotal' => $cartItems->sum('total'),
        ]);
    }
}
