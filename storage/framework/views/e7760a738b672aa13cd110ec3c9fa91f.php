<div x-data="{ open: false }">
    
    <!-- Header Icon Button -->
    <button @click="open = true; document.body.classList.add('overflow-hidden')" 
            type="button" 
            title="Change Theme & Background"
            class="p-2.5 rounded-xl theme-inner border border-slate-800/80 text-slate-300 hover:text-white hover:border-slate-700 transition flex items-center gap-2 group">
        <?php if (isset($component)) { $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Svg::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('heroicon-o-swatch'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\BladeUI\Icons\Components\Svg::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'size-5 theme-text group-hover:scale-110 transition-transform']); ?>
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
        <span class="text-xs font-bold hidden sm:inline-block">Theme</span>
    </button>

    <!-- Modal Popup Container -->
    <template x-teleport="body">
        <div x-show="open" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-[99999] flex items-center justify-center p-4 sm:p-6" 
             style="display: none;">
            
            <!-- Overlay Backdrop -->
            <div @click="open = false; document.body.classList.remove('overflow-hidden')" 
                 class="absolute inset-0 bg-black/80 backdrop-blur-md"></div>

            <!-- 🌟 Constrained Card View (max-w-md limit lagaya hai) -->
            <div x-transition:enter="transition ease-out duration-300 transform"
                 x-transition:enter-start="scale-95 opacity-0"
                 x-transition:enter-end="scale-100 opacity-100"
                 x-transition:leave="transition ease-in duration-200 transform"
                 x-transition:leave-start="scale-100 opacity-100"
                 x-transition:leave-end="scale-95 opacity-0"
                 class="relative z-10 w-full max-w-md theme-card border border-slate-800/80 rounded-3xl p-5 space-y-5 shadow-2xl">
                
                <!-- Modal Header -->
                <div class="flex items-center justify-between border-b border-slate-800/80 pb-3">
                    <div>
                        <h3 class="font-black text-white text-sm flex items-center gap-2 tracking-wide">
                            <?php if (isset($component)) { $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Svg::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('heroicon-o-swatch'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\BladeUI\Icons\Components\Svg::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'size-5 theme-text']); ?>
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
                            Theme Customizer
                        </h3>
                        <p class="text-[11px] text-slate-400 mt-0.5">Select accent color & background mode</p>
                    </div>
                    
                    <button @click="open = false; document.body.classList.remove('overflow-hidden')" 
                            type="button"
                            class="p-1.5 rounded-full bg-slate-800/80 text-slate-400 hover:text-white hover:bg-slate-700 transition">
                        <?php if (isset($component)) { $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Svg::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('heroicon-o-x-mark'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\BladeUI\Icons\Components\Svg::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'size-4']); ?>
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
                </div>

                <!-- 1. ACCENT COLORS GRID -->
                <div class="space-y-2.5">
                    <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Accent Color</span>
                    
                    <div class="grid grid-cols-4 gap-2">
                        <!-- Amber -->
                        <button wire:click="setTheme('amber')" 
                                class="p-2 rounded-xl border flex flex-col items-center gap-1 transition <?php echo e($activeTheme === 'amber' ? 'border-amber-500 bg-amber-500/10 theme-text' : 'border-slate-800 bg-slate-900/50 text-slate-400 hover:border-slate-700'); ?>">
                            <span class="size-4 rounded-full bg-amber-500 shadow-md shadow-amber-500/50"></span>
                            <span class="text-[10px] font-bold">Amber</span>
                        </button>

                        <!-- Rose -->
                        <button wire:click="setTheme('rose')" 
                                class="p-2 rounded-xl border flex flex-col items-center gap-1 transition <?php echo e($activeTheme === 'rose' ? 'border-rose-500 bg-rose-500/10 theme-text' : 'border-slate-800 bg-slate-900/50 text-slate-400 hover:border-slate-700'); ?>">
                            <span class="size-4 rounded-full bg-rose-500 shadow-md shadow-rose-500/50"></span>
                            <span class="text-[10px] font-bold">Rose</span>
                        </button>

                        <!-- Emerald -->
                        <button wire:click="setTheme('emerald')" 
                                class="p-2 rounded-xl border flex flex-col items-center gap-1 transition <?php echo e($activeTheme === 'emerald' ? 'border-emerald-500 bg-emerald-500/10 theme-text' : 'border-slate-800 bg-slate-900/50 text-slate-400 hover:border-slate-700'); ?>">
                            <span class="size-4 rounded-full bg-emerald-500 shadow-md shadow-emerald-500/50"></span>
                            <span class="text-[10px] font-bold">Emerald</span>
                        </button>

                        <!-- Indigo -->
                        <button wire:click="setTheme('indigo')" 
                                class="p-2 rounded-xl border flex flex-col items-center gap-1 transition <?php echo e($activeTheme === 'indigo' ? 'border-indigo-500 bg-indigo-500/10 theme-text' : 'border-slate-800 bg-slate-900/50 text-slate-400 hover:border-slate-700'); ?>">
                            <span class="size-4 rounded-full bg-indigo-500 shadow-md shadow-indigo-500/50"></span>
                            <span class="text-[10px] font-bold">Indigo</span>
                        </button>

                        <!-- Cyan -->
                        <button wire:click="setTheme('cyan')" 
                                class="p-2 rounded-xl border flex flex-col items-center gap-1 transition <?php echo e($activeTheme === 'cyan' ? 'border-cyan-500 bg-cyan-500/10 theme-text' : 'border-slate-800 bg-slate-900/50 text-slate-400 hover:border-slate-700'); ?>">
                            <span class="size-4 rounded-full bg-cyan-500 shadow-md shadow-cyan-500/50"></span>
                            <span class="text-[10px] font-bold">Cyan</span>
                        </button>

                        <!-- Purple -->
                        <button wire:click="setTheme('purple')" 
                                class="p-2 rounded-xl border flex flex-col items-center gap-1 transition <?php echo e($activeTheme === 'purple' ? 'border-purple-500 bg-purple-500/10 theme-text' : 'border-slate-800 bg-slate-900/50 text-slate-400 hover:border-slate-700'); ?>">
                            <span class="size-4 rounded-full bg-purple-500 shadow-md shadow-purple-500/50"></span>
                            <span class="text-[10px] font-bold">Purple</span>
                        </button>

                        <!-- Orange -->
                        <button wire:click="setTheme('orange')" 
                                class="p-2 rounded-xl border flex flex-col items-center gap-1 transition <?php echo e($activeTheme === 'orange' ? 'border-orange-500 bg-orange-500/10 theme-text' : 'border-slate-800 bg-slate-900/50 text-slate-400 hover:border-slate-700'); ?>">
                            <span class="size-4 rounded-full bg-orange-500 shadow-md shadow-orange-500/50"></span>
                            <span class="text-[10px] font-bold">Orange</span>
                        </button>
                    </div>
                </div>

                <!-- 2. BACKGROUND MODES GRID -->
                <div class="space-y-2.5">
                    <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Background Mode</span>
                    
                    <div class="grid grid-cols-2 gap-2">
                        <button wire:click="setBg('midnight')" 
                                class="p-2.5 rounded-xl border flex items-center justify-between text-xs font-bold transition <?php echo e($activeBg === 'midnight' ? 'border-amber-500/60 theme-inner theme-text' : 'border-slate-800 bg-[#0b0813] text-slate-400 hover:border-slate-700'); ?>">
                            <span>Midnight</span>
                            <span class="size-2.5 rounded-full bg-[#0b0813] border border-slate-700"></span>
                        </button>

                        <button wire:click="setBg('black')" 
                                class="p-2.5 rounded-xl border flex items-center justify-between text-xs font-bold transition <?php echo e($activeBg === 'black' ? 'border-amber-500/60 theme-inner theme-text' : 'border-slate-800 bg-[#050505] text-slate-400 hover:border-slate-700'); ?>">
                            <span>Obsidian</span>
                            <span class="size-2.5 rounded-full bg-[#050505] border border-slate-700"></span>
                        </button>

                        <button wire:click="setBg('navy')" 
                                class="p-2.5 rounded-xl border flex items-center justify-between text-xs font-bold transition <?php echo e($activeBg === 'navy' ? 'border-amber-500/60 theme-inner theme-text' : 'border-slate-800 bg-[#060a12] text-slate-400 hover:border-slate-700'); ?>">
                            <span>Navy</span>
                            <span class="size-2.5 rounded-full bg-[#060a12] border border-slate-700"></span>
                        </button>

                        <button wire:click="setBg('slate')" 
                                class="p-2.5 rounded-xl border flex items-center justify-between text-xs font-bold transition <?php echo e($activeBg === 'slate' ? 'border-amber-500/60 theme-inner theme-text' : 'border-slate-800 bg-[#0f172a] text-slate-400 hover:border-slate-700'); ?>">
                            <span>Dark Slate</span>
                            <span class="size-2.5 rounded-full bg-[#0f172a] border border-slate-700"></span>
                        </button>
                    </div>
                </div>

                <!-- Save & Close -->
                <div class="pt-1">
                    <button @click="open = false; document.body.classList.remove('overflow-hidden')" 
                            type="button" 
                            class="w-full py-2.5 rounded-xl theme-btn font-extrabold text-xs shadow hover:scale-[1.01] transition">
                        Done
                    </button>
                </div>

            </div>
        </div>
    </template>
</div><?php /**PATH /var/www/html/ecommerce/resources/views/livewire/theme-switcher.blade.php ENDPATH**/ ?>