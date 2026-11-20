<?php

use App\Models\Product;
use App\Models\ProductVariant;
use App\Services\CartService;
use App\Services\ShopAssistant;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Component;

new class extends Component
{
    public bool $isOpen = false;

    public string $input = '';

    public array $messages = [];

    public array $selectedVariants = [];

    public function mount(): void
    {
        $this->messages = session()->get('shop_assistant.messages', []);
    }

    public function open(): void
    {
        $this->isOpen = true;
    }

    public function close(): void
    {
        $this->isOpen = false;
    }

    public function send(ShopAssistant $assistant): void
    {
        $message = trim($this->input);

        if ($message === '') {
            return;
        }

        $maxLength = (int) config('assistant.max_message_length', 1000);

        if (mb_strlen($message) > $maxLength) {
            $this->messages[] = [
                'role' => 'assistant',
                'content' => "Message zyada lamba hai. Maximum {$maxLength} characters bhejein.",
                'products' => [],
            ];

            $this->saveMessages();

            return;
        }

        $identity = auth()->check()
            ? 'user:' . auth()->id()
            : 'ip:' . (request()->ip() ?: 'unknown');

        $key = 'shop-assistant:' . hash('sha256', $identity);
        $limit = auth()->check() ? 10 : 5;

        if (RateLimiter::tooManyAttempts($key, $limit)) {
            $this->messages[] = [
                'role' => 'assistant',
                'content' => 'Aapne kaafi requests bhej di hain. Thodi der baad dobara try karein.',
                'products' => [],
            ];

            $this->input = '';
            $this->saveMessages();

            return;
        }

        RateLimiter::hit($key, 60);

        $this->input = '';

        $this->messages[] = [
            'role' => 'user',
            'content' => $message,
            'products' => [],
        ];

        if (! auth()->check() && $this->isOrderOrAccountRequest($message)) {
            $reply = 'Please log in to securely check your order or account details.';

            $this->messages[] = [
                'role' => 'assistant',
                'content' => $reply,
                'login_url' => route('login'),
                'products' => [],
            ];

            $this->saveMessages();

            return;
        }

        $history = session()->get('shop_assistant.history', []);

        try {
            $result = $assistant->reply($message, $history, auth()->user());

            $reply = is_string($result['message'] ?? null)
                ? $result['message']
                : 'Maaf kijiye, abhi response nahi mil paaya. Dobara try karein.';

            $products = is_array($result['products'] ?? null)
                ? $result['products']
                : [];

            $this->messages[] = [
                'role' => 'assistant',
                'content' => $reply,
                'products' => $products,
            ];

            foreach ($products as $product) {
                if (empty($product['id'])) {
                    continue;
                }

                $productId = (int) $product['id'];
                $variantIds = [];

                foreach (($product['variants'] ?? []) as $variant) {
                    if (
                        ! empty($variant['id'])
                        && (int) ($variant['stock_quantity'] ?? 0) > 0
                    ) {
                        $variantIds[] = (int) $variant['id'];
                    }
                }

                $allowed = session()->get('shop_assistant.allowed_products', []);
                $allowed[$productId] = $variantIds;

                session()->put('shop_assistant.allowed_products', $allowed);
            }

            $history[] = ['role' => 'user', 'content' => $message];
            $history[] = ['role' => 'assistant', 'content' => $reply];

            session()->put(
                'shop_assistant.history',
                array_slice($history, -24)
            );
        } catch (\Throwable $exception) {
            report($exception);

            $this->messages[] = [
                'role' => 'assistant',
                'content' => 'AI assistant abhi available nahi hai. Please thodi der baad try karein.',
                'products' => [],
            ];
        }

        $this->saveMessages();
    }

    public function addToCart(CartService $cart, int $productId): void
    {
        $allowedProducts = session()->get('shop_assistant.allowed_products', []);

        if (! array_key_exists($productId, $allowedProducts)) {
            $this->notifyUser('Please product ko dobara search karein.', 'error');

            return;
        }

        $product = Product::active()
            ->with(['variants'])
            ->find($productId);

        if (! $product) {
            $this->notifyUser('Ye product abhi available nahi hai.', 'error');

            return;
        }

        $variant = null;
        $selectedVariantId = (int) ($this->selectedVariants[$productId] ?? 0);

        if ($product->variants->isNotEmpty()) {
            if ($selectedVariantId < 1) {
                $this->notifyUser('Please pehle product variant select karein.', 'error');

                return;
            }

            $allowedVariantIds = array_map(
                'intval',
                $allowedProducts[$productId] ?? []
            );

            if (! in_array($selectedVariantId, $allowedVariantIds, true)) {
                $this->notifyUser('Selected variant available nahi hai. Product dobara search karein.', 'error');

                return;
            }

            $variant = $product->variants->firstWhere('id', $selectedVariantId);

            if (! $variant || (int) $variant->stock_quantity < 1) {
                $this->notifyUser('Ye variant out of stock hai.', 'error');

                return;
            }
        }

        if ($cart->limitFor($product, $variant) < 1) {
            $this->notifyUser('Ye product abhi cart mein add nahi ho sakta.', 'error');

            return;
        }

        try {
            $notice = $cart->add($product, 1, $variant);

            $this->dispatch('cart-updated');
            $this->dispatch('open-cart', notice: $notice);
            $this->dispatch('notify', type: 'success', message: $notice);
        } catch (\Throwable $exception) {
            report($exception);

            $this->notifyUser('Product cart mein add nahi ho paaya. Dobara try karein.', 'error');
        }
    }

    public function clearConversation(): void
    {
        $this->messages = [];
        $this->selectedVariants = [];

        session()->forget([
            'shop_assistant.messages',
            'shop_assistant.history',
            'shop_assistant.allowed_products',
        ]);
    }

    private function isOrderOrAccountRequest(string $message): bool
    {
        $message = mb_strtolower($message);

        $phrases = [
            'my order', 'my orders', 'order status', 'track order',
            'track my order', 'where is my order', 'cancel order',
            'cancel my order', 'refund', 'return my order',
            'payment status', 'my account', 'account details',
            'my profile', 'my address', 'mera order', 'meri order',
            'mere order', 'mere orders', 'mera parcel', 'meri parcel',
            'order kahan', 'order kaha', 'order kab milega',
            'order cancel', 'paise wapas', 'paisa wapas',
            'mera account', 'meri details', 'ਮੇਰਾ ਆਰਡਰ',
            'ਮੇਰੇ ਆਰਡਰ', 'ਆਰਡਰ ਕਿੱਥੇ', 'ਰਿਫੰਡ',
            'मेरा ऑर्डर', 'मेरे ऑर्डर', 'ऑर्डर कहाँ',
            'ऑर्डर कहां', 'ऑर्डर स्टेटस', 'रिफंड',
            'पैसे वापस', 'मेरा अकाउंट',
        ];

        foreach ($phrases as $phrase) {
            if (str_contains($message, $phrase)) {
                return true;
            }
        }

        return false;
    }

    private function saveMessages(): void
    {
        $this->messages = array_slice($this->messages, -30);

        session()->put('shop_assistant.messages', $this->messages);
    }

    private function notifyUser(string $message, string $type = 'error'): void
    {
        $this->dispatch('notify', type: $type, message: $message);
    }
};
?>

<div>
    {{-- Floating assistant button --}}
    @if (! $isOpen)
        <button
            type="button"
            wire:click="open"
            aria-label="Open AI shopping assistant"
            class="fixed bottom-24 right-4 z-[55] flex h-14 w-14 items-center justify-center rounded-full bg-indigo-600 text-white shadow-xl transition hover:bg-indigo-700 focus:outline-none focus:ring-4 focus:ring-indigo-300 md:bottom-6"
        >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                <path stroke-linecap="round" stroke-linejoin="round" d="M8 10h8M8 14h5m-8 6-1.5-4A8 8 0 1 1 20 12a8 8 0 0 1-8 8H5Z" />
            </svg>
        </button>
    @endif

    {{-- Assistant panel --}}
    @if ($isOpen)
        <section
            class="fixed inset-x-3 bottom-20 z-[55] flex max-h-[75dvh] flex-col overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-2xl dark:border-gray-700 dark:bg-gray-900 sm:inset-x-auto sm:bottom-6 sm:right-4 sm:w-[410px] md:max-h-[min(680px,85dvh)]"
            aria-label="AI shopping assistant"
        >
            {{-- Header --}}
            <header class="flex items-center justify-between gap-3 bg-indigo-600 px-4 py-3 text-white">
                <div class="flex min-w-0 items-center gap-3">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-white/15">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v3m0 12v3M3 12h3m12 0h3M5.64 5.64l2.12 2.12m8.48 8.48 2.12 2.12m0-12.72-2.12 2.12m-8.48 8.48-2.12 2.12M16 12a4 4 0 1 1-8 0 4 4 0 0 1 8 0Z" />
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <h2 class="truncate text-sm font-semibold">AI Shopping Assistant</h2>
                        <p class="text-xs text-indigo-100">Ask me question Hindi and English</p>
                    </div>
                </div>

                <div class="flex shrink-0 items-center gap-1">
                    <button
                        type="button"
                        wire:click="clearConversation"
                        title="Clear conversation"
                        aria-label="Clear conversation"
                        class="rounded-lg p-2 transition hover:bg-white/15"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 7h16M10 11v6m4-6v6M5 7l1 13h12l1-13M9 7V4h6v3" />
                        </svg>
                    </button>
                    <button
                        type="button"
                        wire:click="close"
                        title="Close assistant"
                        aria-label="Close assistant"
                        class="rounded-lg p-2 transition hover:bg-white/15"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m6 6 12 12M18 6 6 18" />
                        </svg>
                    </button>
                </div>
            </header>

            {{-- Conversation --}}
            <div
                class="min-h-0 flex-1 space-y-4 overflow-y-auto bg-gray-50 p-3 dark:bg-gray-950 sm:p-4"
                id="shop-assistant-messages"
                aria-live="polite"
            >
                @if (empty($messages))
                    <div class="flex min-h-44 flex-col items-center justify-center px-3 py-6 text-center">
                        <div class="mb-3 flex h-14 w-14 items-center justify-center rounded-2xl bg-indigo-100 text-indigo-700 dark:bg-indigo-950 dark:text-indigo-300">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 7h18l-2 13H5L3 7Zm4 0a5 5 0 0 1 10 0M9 12h.01M15 12h.01" />
                            </svg>
                        </div>
                        <h3 class="text-base font-semibold text-gray-900 dark:text-white">Kya dhoondh rahe hain?</h3>
                        <p class="mt-1 max-w-xs text-sm text-gray-600 dark:text-gray-400">
                            Budget, colour, size ya product ka naam batayein. Main available products dhoondhne mein help karunga.
                        </p>
                        <div class="mt-4 flex flex-wrap justify-center gap-2">
                            <button type="button" wire:click="$set('input', '2000 ke andar blue jacket dikhao')" class="rounded-full border border-gray-200 bg-white px-3 py-2 text-xs text-gray-700 hover:border-indigo-400 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200">
                                Blue jacket under ₹2000
                            </button>
                            <button type="button" wire:click="$set('input', 'Available T-shirts dikhao')" class="rounded-full border border-gray-200 bg-white px-3 py-2 text-xs text-gray-700 hover:border-indigo-400 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200">
                                Available T-shirts
                            </button>
                        </div>
                    </div>
                @else
                    @foreach ($messages as $index => $message)
                        <div wire:key="shop-assistant-message-{{ $index }}" class="flex {{ ($message['role'] ?? 'assistant') === 'user' ? 'justify-end' : 'justify-start' }}">
                            <div class="max-w-[94%] min-w-0 {{ ($message['role'] ?? 'assistant') === 'user' ? 'rounded-2xl rounded-br-sm bg-indigo-600 text-white' : 'rounded-2xl rounded-bl-sm border border-gray-200 bg-white text-gray-800 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100' }} px-3 py-2.5 text-sm shadow-sm">
                                <p class="whitespace-pre-line break-words">{{ $message['content'] ?? '' }}</p>

                                @if (($message['role'] ?? '') === 'assistant' && ! empty($message['login_url']))
                                    <a
                                        href="{{ $message['login_url'] }}"
                                        class="mt-2 inline-block font-semibold text-indigo-600 underline hover:text-indigo-800 dark:text-indigo-400"
                                    >Log in to your account</a>
                                @endif

                                @if (($message['role'] ?? '') === 'assistant' && ! empty($message['products']))
                                    <div class="mt-3 space-y-3">
                                        @foreach ($message['products'] as $product)
                                            @if (! empty($product['id']))
                                                <article wire:key="shop-assistant-product-{{ $index }}-{{ $product['id'] }}" class="overflow-hidden rounded-xl border border-gray-200 bg-white text-gray-900 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100">
                                                    @php
                                                        $productImage = data_get($product, 'images.0');
                                                        $productPrice = $product['final_price'] ?? $product['sale_price'] ?? $product['price'] ?? null;
                                                        $productVariants = $product['variants'] ?? [];
                                                    @endphp

                                                    <div class="flex gap-3 p-3">
                                                        @if ($productImage)
                                                            <img
                                                                src="{{ \Illuminate\Support\Str::startsWith($productImage, ['http://', 'https://']) ? $productImage : asset('storage/' . ltrim($productImage, '/')) }}"
                                                                alt="{{ $product['name'] ?? 'Product' }}"
                                                                loading="lazy"
                                                                class="h-20 w-20 shrink-0 rounded-lg bg-gray-100 object-cover dark:bg-gray-700"
                                                            >
                                                        @endif

                                                        <div class="min-w-0 flex-1">
                                                            <h3 class="line-clamp-2 text-sm font-semibold">
                                                                {{ $product['name'] ?? 'Product' }}
                                                            </h3>

                                                            @if (! empty($product['brand']))
                                                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                                                    {{ is_array($product['brand']) ? ($product['brand']['name'] ?? '') : $product['brand'] }}
                                                                </p>
                                                            @endif

                                                            @if (is_numeric($productPrice))
                                                                <p class="mt-2 font-semibold text-indigo-700 dark:text-indigo-300">
                                                                    ₹{{ number_format((float) $productPrice, 2) }}
                                                                </p>
                                                            @endif

                                                            @if (! empty($product['url']))
                                                                <a href="{{ $product['url'] }}" class="mt-1 inline-block text-xs font-medium text-indigo-600 hover:underline dark:text-indigo-300">
                                                                    View product
                                                                </a>
                                                            @elseif (! empty($product['slug']))
                                                                <a href="{{ route('product.detail', ['slug' => $product['slug']]) }}" class="mt-1 inline-block text-xs font-medium text-indigo-600 hover:underline dark:text-indigo-300">
                                                                    View product
                                                                </a>
                                                            @endif
                                                        </div>
                                                    </div>

                                                    @if (! empty($productVariants))
                                                        <div class="space-y-2 px-3 pb-3">
                                                            <label class="block text-xs font-medium text-gray-600 dark:text-gray-300">
                                                                Select variant
                                                            </label>
                                                            <select
                                                                wire:model="selectedVariants.{{ $product['id'] }}"
                                                                class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-800 focus:border-indigo-500 focus:outline-none dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100"
                                                            >
                                                                <option value="">Choose an option</option>
                                                                @foreach ($productVariants as $variant)
                                                                    @if (! empty($variant['id']) && (int) ($variant['stock_quantity'] ?? 0) > 0)
                                                                        <option value="{{ $variant['id'] }}">
                                                                            {{ $variant['label'] ?? ('Option ' . $variant['id']) }}
                                                                            @if (is_array($variant['options'] ?? null))
                                                                                — {{ implode(', ', array_map(fn ($key, $value) => $key . ': ' . $value, array_keys($variant['options']), array_values($variant['options']))) }}
                                                                            @endif
                                                                        </option>
                                                                    @endif
                                                                @endforeach
                                                            </select>

                                                            <button
                                                                type="button"
                                                                wire:click="addToCart({{ (int) $product['id'] }})"
                                                                wire:loading.attr="disabled"
                                                                wire:target="addToCart"
                                                                @disabled(empty($selectedVariants[$product['id']]))
                                                                class="w-full rounded-lg bg-indigo-600 px-3 py-2.5 text-sm font-semibold text-white transition hover:bg-indigo-700 disabled:cursor-not-allowed disabled:opacity-50"
                                                            >
                                                                Add to cart
                                                            </button>
                                                        </div>
                                                    @else
                                                        <div class="px-3 pb-3">
                                                            <button
                                                                type="button"
                                                                wire:click="addToCart({{ (int) $product['id'] }})"
                                                                wire:loading.attr="disabled"
                                                                wire:target="addToCart"
                                                                class="w-full rounded-lg bg-indigo-600 px-3 py-2.5 text-sm font-semibold text-white transition hover:bg-indigo-700 disabled:cursor-not-allowed disabled:opacity-50"
                                                            >
                                                                Add to cart
                                                            </button>
                                                        </div>
                                                    @endif
                                                </article>
                                            @endif
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                @endif

                <div wire:loading wire:target="send" class="flex justify-start">
                    <div class="rounded-2xl rounded-bl-sm border border-gray-200 bg-white px-4 py-3 text-sm text-gray-600 shadow-sm dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">
                        Products searching…
                    </div>
                </div>
            </div>

            {{-- Message input --}}
            <form wire:submit="send" class="border-t border-gray-200 bg-white p-3 dark:border-gray-700 dark:bg-gray-900 sm:p-4">
                <div class="flex items-end gap-2">
                    <textarea
                        wire:model="input"
                        rows="1"
                        maxlength="{{ (int) config('assistant.max_message_length', 1000) }}"
                        placeholder="Jaise: ₹2000 ke andar blue jacket..."
                        aria-label="Your message"
                        class="max-h-24 min-h-11 flex-1 resize-y rounded-xl border border-gray-300 bg-white px-3 py-3 text-sm text-gray-900 outline-none placeholder:text-gray-400 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 dark:border-gray-700 dark:bg-gray-800 dark:text-white dark:focus:ring-indigo-950"
                    ></textarea>

                    <button
                        type="submit"
                        wire:loading.attr="disabled"
                        wire:target="send"
                        aria-label="Send message"
                        class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-indigo-600 text-white transition hover:bg-indigo-700 disabled:cursor-not-allowed disabled:opacity-50"
                    >
                        <svg wire:loading.remove wire:target="send" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m22 2-7 20-4-9-9-4 20-7ZM22 2 11 13" />
                        </svg>
                        <svg wire:loading wire:target="send" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 animate-spin" viewBox="0 0 24 24" fill="none">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 0 1 8-8V0C5.37 0 0 5.37 0 12h4Z" />
                        </svg>
                    </button>
                </div>
                <p class="mt-2 text-center text-[11px] text-gray-400">
                    Product availability aur prices database se verify hote hain.
                </p>
            </form>
        </section>
    @endif
</div>
