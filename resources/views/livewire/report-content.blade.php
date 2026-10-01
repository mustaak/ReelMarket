<div>
    @if($open)
        <div class="fixed inset-0 z-[80] flex items-end justify-center bg-black/65 p-0 sm:items-center sm:p-4" role="dialog" aria-modal="true" aria-labelledby="report-heading">
            <button type="button" wire:click="close" class="absolute inset-0 cursor-default" aria-label="Close report form"></button>
            <section class="theme-card relative z-10 w-full max-w-md rounded-t-2xl border border-slate-800 p-5 shadow-2xl sm:rounded-2xl">
                <header class="flex items-center justify-between gap-4">
                    <h2 id="report-heading" class="text-lg font-black text-white">Report content</h2>
                    <button type="button" wire:click="close" aria-label="Close" class="rounded-lg p-2 text-slate-400 hover:bg-white/5 hover:text-white">
                        <x-heroicon-o-x-mark class="size-5" />
                    </button>
                </header>

                <form wire:submit="submit" class="mt-5 space-y-4">
                    <div>
                        <label for="report-reason" class="mb-1.5 block text-xs font-bold text-slate-300">Reason</label>
                        <select id="report-reason" wire:model="reason" required class="w-full rounded-xl border border-slate-700 bg-slate-950 px-3 py-2.5 text-sm text-white focus:border-(--accent-primary) focus:outline-none">
                            <option value="">Select a reason</option>
                            @foreach($reasons as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('reason') <p class="mt-1 text-xs text-rose-400">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="report-description" class="mb-1.5 block text-xs font-bold text-slate-300">Details (optional)</label>
                        <textarea id="report-description" wire:model="description" rows="3" maxlength="1000" class="w-full resize-y rounded-xl border border-slate-700 bg-slate-950 px-3 py-2.5 text-sm text-white placeholder:text-slate-500 focus:border-(--accent-primary) focus:outline-none"></textarea>
                        @error('description') <p class="mt-1 text-xs text-rose-400">{{ $message }}</p> @enderror
                    </div>

                    <div class="flex justify-end gap-2 border-t border-slate-800 pt-4">
                        <button type="button" wire:click="close" class="rounded-lg border border-slate-700 px-4 py-2 text-xs font-bold text-slate-200">Cancel</button>
                        <button type="submit" wire:loading.attr="disabled" class="theme-btn rounded-lg px-4 py-2 text-xs font-black disabled:opacity-50">Submit report</button>
                    </div>
                </form>
            </section>
        </div>
    @endif
</div>
