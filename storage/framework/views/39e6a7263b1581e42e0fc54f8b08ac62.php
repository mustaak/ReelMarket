<span>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($count > 0): ?>
        <span class="theme-btn absolute -right-2 -top-2 grid min-w-5 place-items-center rounded-full px-1 text-[11px] font-black leading-5">
            <?php echo e($count > 99 ? '99+' : $count); ?>

        </span>
        <span class="sr-only"><?php echo e($count); ?> <?php echo e(\Illuminate\Support\Str::plural('item', $count)); ?> in cart</span>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</span><?php /**PATH /var/www/html/ecommerce/resources/views/livewire/cart-count.blade.php ENDPATH**/ ?>