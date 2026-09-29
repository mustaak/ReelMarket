<section class="bg-white py-8 antialiased dark:bg-gray-900 md:py-16">
  <div class="mx-auto max-w-screen-xl px-4 2xl:px-0">
    <h2 class="text-xl font-semibold text-gray-900 dark:text-white sm:text-2xl">
      Shopping Cart ({{ $cartItems->sum('qty') }} {{ Str::plural('item', $cartItems->sum('qty')) }})
    </h2>

    @if($cartItems->isNotEmpty())
      <div class="mt-6 sm:mt-8 md:gap-6 lg:flex lg:items-start xl:gap-8">
        
        <!-- Cart Items Column -->
        <div class="mx-auto w-full flex-none lg:max-w-2xl xl:max-w-4xl space-y-6">
          <div class="space-y-4">
            @foreach($cartItems as $item)
              <div wire:key="cart-item-{{ $item['id'] }}" class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-800 md:p-6">
                <div class="space-y-4 md:flex md:items-center md:justify-between md:gap-6 md:space-y-0">
                  
                  <!-- Product Image -->
                  <a href="#" class="shrink-0 md:order-1">
                    <img class="h-20 w-20 object-contain dark:hidden" src="{{ asset('storage/' . $item['image']) }}" alt="{{ $item['name'] }}" alt="{{ $item['name'] }}" />
                    <img class="hidden h-20 w-20 object-contain dark:block" src="{{ asset('storage/' . $item['image']) }}" alt="{{ $item['name'] }}" alt="{{ $item['name'] }}" />
                  </a>

                  <!-- Counter & Dynamic Total Price -->
                  <div class="flex items-center justify-between md:order-3 md:justify-end">
                    <div class="flex items-center">
                      <button wire:click="updateQuantity({{ $item['id'] }}, -1)" type="button" class="cursor-pointer inline-flex h-6 w-6 shrink-0 items-center justify-center rounded-md border border-gray-300 bg-gray-100 hover:bg-gray-200 focus:outline-none dark:border-gray-600 dark:bg-gray-700 dark:hover:bg-gray-600">
                        <svg class="h-2.5 w-2.5 text-gray-900 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 18 2">
                          <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 1h16" />
                        </svg>
                      </button>
                      
                      <span class="w-10 shrink-0 text-center text-sm font-medium text-gray-900 dark:text-white">
                        {{ $item['qty'] }}
                      </span>

                      <button wire:click="updateQuantity({{ $item['id'] }}, 1)" @if($item['qty'] >= $item['max']) disabled @endif type="button" class="cursor-pointer inline-flex h-6 w-6 shrink-0 items-center justify-center rounded-md border border-gray-300 bg-gray-100 hover:bg-gray-200 focus:outline-none disabled:opacity-40 disabled:cursor-not-allowed dark:border-gray-600 dark:bg-gray-700 dark:hover:bg-gray-600">
                        <svg class="h-2.5 w-2.5 text-gray-900 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 18 18">
                          <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 1v16M1 9h16" />
                        </svg>
                      </button>
                    </div>

                    <div class="text-end md:order-4 md:w-32">
                      <p class="text-base font-bold text-gray-900 dark:text-white">${{ number_format($item['total'] * $item['qty'], 2) }}</p>
                    </div>
                  </div>

                  <!-- Item Meta & Remove Actions -->
                  <div class="w-full min-w-0 flex-1 space-y-4 md:order-2 md:max-w-md">
                    <a href="#" class="text-base font-medium text-gray-900 hover:underline dark:text-white">
                      {{ $item['name'] }}
                    </a>

                    <div class="flex items-center gap-4">
                      {{-- <button type="button" class="cursor-pointer inline-flex items-center text-sm font-medium text-gray-500 hover:text-gray-900 hover:underline dark:text-gray-400 dark:hover:text-white">
                        <svg class="me-1.5 h-5 w-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                          <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12.01 6.001C6.5 1 1 8 5.782 13.001L12.011 20l6.23-7C23 8 17.5 1 12.01 6.002Z" />
                        </svg>
                        Add to Favorites
                      </button> --}}

                      <button wire:click="removeItem({{ $item['id'] }})" type="button" class="cursor-pointer inline-flex items-center text-sm font-medium text-red-600 hover:underline dark:text-red-500">
                        <svg class="me-1.5 h-5 w-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                          <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18 17.94 6M18 18 6.06 6" />
                        </svg>
                        Remove
                      </button>
                    </div>
                  </div>

                </div>
              </div>
            @endforeach
          </div>

          <!-- LOWEST PRICE DEALS SECTION (3-Grid Layout) -->
          @if(isset($lowestPriceProducts) && $lowestPriceProducts->isNotEmpty())
            <div class="pt-6 space-y-4  dark:border-gray-700">
              <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Lowest Price Deals</h3>
                <a href="#" class="text-sm font-medium text-primary-700 hover:underline dark:text-primary-500 cursor-pointer ">View All →</a>
              </div>

              <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($lowestPriceProducts as $product)
                  <div wire:key="lowest-{{ $product->id }}" class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-800 flex flex-col justify-between space-y-3">
                    <div class="space-y-2">
                      <div class="h-32 w-full rounded-md bg-gray-100 dark:bg-gray-700 overflow-hidden">
                       @if($product->images->first()?->image)
                            <img src="{{ asset('storage/' . $product->images->first()->image) }}" 
                                    alt="{{ $product->name }}" 
                                    class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                        @else
                            <div class="w-full h-full bg-slate-800/60 flex items-center justify-center text-slate-500 text-xs font-bold p-2 text-center">
                                {{ $product->name }}
                            </div>
                        @endif
                      </div>
                      <h4 class="text-sm font-semibold text-gray-900 dark:text-white line-clamp-1">{{ $product->name }}</h4>
                      <p class="text-sm font-bold text-emerald-600 dark:text-emerald-400">${{ number_format($product->price, 2) }}</p>
                    </div>

                    <button wire:click="updateQuantity({{ $product->id }}, 1)" type="button" class="w-full py-2 bg-primary-700 hover:bg-primary-800 text-white rounded-md text-xs font-semibold cursor-pointer transition theme-btn">
                      + Add to Cart
                    </button>
                  </div>
                @endforeach
              </div>
            </div>
          @endif

        </div>

        <!-- Order Summary & Voucher Column -->
        <div class="mx-auto mt-6 max-w-4xl flex-1 space-y-6 lg:mt-0 lg:w-full">
          <!-- Dynamic Order Summary -->
          <div class="space-y-4 rounded-lg border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-800 sm:p-6">
            <p class="text-xl font-semibold text-gray-900 dark:text-white">Order summary</p>

            <div class="space-y-4">
              <div class="space-y-2">
                <dl class="flex items-center justify-between gap-4">
                  <dt class="text-base font-normal text-gray-500 dark:text-gray-400">Original price</dt>
                  <dd class="text-base font-medium text-gray-900 dark:text-white">${{ number_format($subtotal, 2) }}</dd>
                </dl>

                @if($appliedDiscount > 0)
                  <dl class="flex items-center justify-between gap-4">
                    <dt class="text-base font-normal text-gray-500 dark:text-gray-400">Savings</dt>
                    <dd class="text-base font-medium text-green-600">-${{ number_format($appliedDiscount, 2) }}</dd>
                  </dl>
                @endif

                <dl class="flex items-center justify-between gap-4">
                  <dt class="text-base font-normal text-gray-500 dark:text-gray-400">Store Pickup</dt>
                  <dd class="text-base font-medium text-gray-900 dark:text-white">${{ number_format($pickupFee, 2) }}</dd>
                </dl>

                <dl class="flex items-center justify-between gap-4">
                  <dt class="text-base font-normal text-gray-500 dark:text-gray-400">Tax</dt>
                  <dd class="text-base font-medium text-gray-900 dark:text-white">${{ number_format($tax, 2) }}</dd>
                </dl>
              </div>

              <dl class="flex items-center justify-between gap-4 border-t border-gray-200 pt-2 dark:border-gray-700">
                <dt class="text-base font-bold text-gray-900 dark:text-white">Total</dt>
                <dd class="text-base font-bold text-gray-900 dark:text-white">${{ number_format($total, 2) }}</dd>
              </dl>
            </div>

            <a href="" class="cursor-pointer flex w-full items-center justify-center rounded-lg bg-primary-700 px-5 py-2.5 text-sm font-medium text-white hover:bg-primary-800 focus:outline-none focus:ring-4 focus:ring-primary-300 dark:bg-primary-600 dark:hover:bg-primary-700 dark:focus:ring-primary-800 theme-btn">Proceed to Checkout</a>

            <div class="flex items-center justify-center gap-2 ">
              <span class="text-sm font-normal text-gray-500 dark:text-gray-400"> or </span>
              <a href="{{ route('shop.index') }}" class="cursor-pointer inline-flex items-center gap-2 text-sm font-medium text-primary-700 underline hover:no-underline dark:text-primary-500">
                Continue Shopping
                <svg class="h-5 w-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                  <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 12H5m14 0-4 4m4-4-4-4" />
                </svg>
              </a>
            </div>
          </div>

          <!-- Dynamic Voucher Form -->
          <div class="space-y-4 rounded-lg border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-800 sm:p-6">
            <form wire:submit.prevent="applyVoucher" class="space-y-4">
              <div>
                <label for="voucher" class="mb-2 block text-sm font-medium text-gray-900 dark:text-white"> Do you have a voucher or gift card? </label>
                <input wire:model="voucherCode" type="text" id="voucher" class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-primary-500 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder:text-gray-400 dark:focus:border-primary-500 dark:focus:ring-primary-500" placeholder="e.g. SAVE100" required />
                
                @if($voucherError)
                  <p class="text-xs text-red-500 mt-1">{{ $voucherError }}</p>
                @endif
                @if($voucherSuccess)
                  <p class="text-xs text-green-500 mt-1">{{ $voucherSuccess }}</p>
                @endif
              </div>
              <button type="submit" class="cursor-pointer flex w-full items-center justify-center rounded-lg bg-primary-700 px-5 py-2.5 text-sm font-medium text-white hover:bg-primary-800 focus:outline-none focus:ring-4 focus:ring-primary-300 dark:bg-primary-600 dark:hover:bg-primary-700 dark:focus:ring-primary-800 theme-btn">Apply Code</button>
            </form>
          </div>
        </div>

      </div>
    @else
      <!-- Dynamic Empty Cart View -->
      <div class="mt-8 rounded-lg border border-gray-200 bg-white p-10 text-center shadow-sm dark:border-gray-700 dark:bg-gray-800 space-y-4">
        <p class="text-4xl">🛒</p>
        <h3 class="text-lg font-bold text-gray-900 dark:text-white">Your cart is currently empty</h3>
        <p class="text-sm text-gray-500 dark:text-gray-400">Discover incredible products and add them to your cart.</p>
        <a href="{{ route('shop.index') }}" class="inline-block rounded-lg bg-primary-700 px-5 py-2.5 text-sm font-medium text-white hover:bg-primary-800 transition cursor-pointer">
          Start Shopping
        </a>
      </div>
    @endif

  </div>
</section>