<div>
    <?php
        $money = fn ($v) => '₹' . number_format($v, fmod((float) $v, 1) === 0.0 ? 0 : 2);
    ?>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($open): ?>
        <div class="fixed inset-0 z-[60]"
             role="dialog" aria-modal="true" aria-label="Shopping cart"
             wire:keydown.escape.window="close">

            <div class="absolute inset-0 bg-black/60" wire:click="close"></div>

            <aside class="theme-card absolute inset-y-0 right-0 flex w-full max-w-md flex-col border-l border-slate-800/80 text-slate-100 shadow-2xl">
                <header class="flex items-center justify-between border-b border-slate-800/80 px-4 py-3">
                    <h2 class="text-lg font-black tracking-wide">
                        Your cart
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($count > 0): ?>
                            <span class="text-slate-400">(<?php echo e($count); ?>)</span>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </h2>
                    <button type="button" wire:click="close" aria-label="Close cart"
                            class="rounded-lg p-2 text-slate-400 hover:bg-white/5 hover:text-white">
                        <?php if (isset($component)) { $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Svg::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('heroicon-o-x-mark'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\BladeUI\Icons\Components\Svg::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'size-6']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c)): ?>
<?php $attributes = $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c; ?>
<?php unset($__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal643fe1b47aec0b76658e1a0200b34b2c)): ?>
<?php $component = $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c; ?>
<?php unset($__componentOriginal643fe1b47aec0b76658e1a0200b34b2c); ?>
<?php endif; ?>
                    </button>
                </header>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($notice): ?>
                    <p class="theme-inner theme-text border-b border-slate-800/80 px-4 py-2 text-sm" role="status"><?php echo e($notice); ?></p>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($items->isEmpty()): ?>
                    <div class="flex flex-1 flex-col items-center justify-center gap-3 p-8 text-center">
                        <?php if (isset($component)) { $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Svg::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('heroicon-o-shopping-bag'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\BladeUI\Icons\Components\Svg::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'size-12 text-slate-500']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c)): ?>
<?php $attributes = $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c; ?>
<?php unset($__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal643fe1b47aec0b76658e1a0200b34b2c)): ?>
<?php $component = $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c; ?>
<?php unset($__componentOriginal643fe1b47aec0b76658e1a0200b34b2c); ?>
<?php endif; ?>
                        <p class="font-bold">Your cart is empty</p>
                        <a href="<?php echo e(route('shop.index')); ?>" wire:click="close"
                           class="theme-btn rounded-xl px-5 py-2.5 text-sm font-bold">
                            Continue shopping
                        </a>
                    </div>
                <?php else: ?>
                    <ul class="flex-1 divide-y divide-slate-800/80 overflow-y-auto px-4">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $line): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                            <li <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'line-'.e($line['cart_key']).''; ?>wire:key="line-<?php echo e($line['cart_key']); ?>" class="flex gap-3 py-4">
                                <a href="<?php echo e(route('product.detail', $line['slug'] ?? $line['id'])); ?>"
                                   class="theme-inner size-20 shrink-0 overflow-hidden rounded-lg">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($line['image']): ?>
                                        <img src="<?php echo e(asset('storage/' . $line['image'])); ?>" alt=""
                                             class="size-full object-cover">
                                    <?php else: ?>
                                        <span class="theme-text grid size-full place-items-center text-2xl font-black">
                                            <?php echo e(mb_substr($line['name'], 0, 1)); ?>

                                        </span>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </a>

                                <div class="flex min-w-0 flex-1 flex-col">
                                    <a href="<?php echo e(route('product.detail', $line['slug'] ?? $line['id'])); ?>" class="truncate text-sm font-bold text-white">
                                        <?php echo e($line['name']); ?>

                                    </a>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($line['variant_label']): ?>
                                        <span class="mt-1 text-xs text-slate-400"><?php echo e($line['variant_label']); ?></span>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    <span class="text-xs text-slate-400"><?php echo e($money($line['unit'])); ?></span>

                                    <div class="mt-auto flex items-center justify-between pt-2">
                                        <div class="theme-inner flex items-center rounded-lg border border-slate-700">
                                            <button type="button" wire:click="decrement(<?php echo e($line['id']); ?>, <?php echo e($line['variant_id'] ?? 'null'); ?>)"
                                                    aria-label="Decrease quantity of <?php echo e($line['name']); ?>"
                                                    class="px-2.5 py-1 text-slate-300 hover:text-white">−</button>
                                            <span class="w-7 text-center text-sm font-bold"><?php echo e($line['qty']); ?></span>
                                            <button type="button" wire:click="increment(<?php echo e($line['id']); ?>, <?php echo e($line['variant_id'] ?? 'null'); ?>)"
                                                    <?php if($line['qty'] >= $line['max']): echo 'disabled'; endif; ?>
                                                    aria-label="Increase quantity of <?php echo e($line['name']); ?>"
                                                    class="px-2.5 py-1 text-slate-300 hover:text-white disabled:opacity-40">+</button>
                                        </div>
                                        <b class="theme-text text-sm"><?php echo e($money($line['total'])); ?></b>
                                    </div>
                                </div>

                                <button type="button" wire:click="remove(<?php echo e($line['id']); ?>, <?php echo e($line['variant_id'] ?? 'null'); ?>)"
                                        aria-label="Remove <?php echo e($line['name']); ?> from cart"
                                        class="self-start rounded-lg p-1.5 text-slate-500 hover:bg-white/5 hover:text-white">
                                    <?php if (isset($component)) { $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Svg::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('heroicon-o-trash'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\BladeUI\Icons\Components\Svg::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'size-5']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c)): ?>
<?php $attributes = $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c; ?>
<?php unset($__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal643fe1b47aec0b76658e1a0200b34b2c)): ?>
<?php $component = $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c; ?>
<?php unset($__componentOriginal643fe1b47aec0b76658e1a0200b34b2c); ?>
<?php endif; ?>
                                </button>
                            </li>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    </ul>

                    <footer class="border-t border-slate-800/80 p-4">
                        <div class="flex items-baseline justify-between">
                            <span class="text-slate-400">Subtotal</span>
                            <b class="theme-text text-lg"><?php echo e($money($subtotal)); ?></b>
                        </div>
                        <p class="mt-1 text-xs text-slate-500">Shipping and taxes are calculated at checkout.</p>
                        <button type="button"
                                onclick="window.location.href='<?php echo e(route('checkout.index')); ?>'"
                                class="theme-btn mt-3 w-full rounded-xl px-6 py-3 text-sm font-bold">
                            Checkout
                        </button>
                    </footer>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </aside>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div><?php /**PATH /var/www/html/ecommerce/resources/views/livewire/cart-drawer.blade.php ENDPATH**/ ?>