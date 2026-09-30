@php
    $hasOpenThread = $activeConversation || $recipient;
    
    $threadParticipants = $activeConversation
        ? $activeConversation->users->reject(fn ($conversationUser) => $conversationUser->is(auth()->user()))
        : collect($recipient ? [$recipient] : []);

    
    $threadTitle = $threadParticipants->pluck('name')->implode(', ') ?: 'Conversation';
    $threadAvatar = $threadParticipants->first()?->profile?->profile_picture;
    $threadUser = $threadParticipants->first();
    $threadLastSeen = $threadUser?->last_seen_at;
    $threadIsOnline = (bool) $threadUser?->status && $threadLastSeen?->gte(now()->subMinutes(2));
@endphp

<div wire:poll.5s="refreshMessages" class="mx-auto w-full max-w-5xl space-y-5">
    <header class="flex items-end justify-between gap-4">
        <div>
            <p class="text-[10px] font-bold uppercase tracking-[0.2em] theme-text">YourBrand</p>
            <h1 class="mt-1 font-display text-2xl font-semibold text-white">Messages</h1>
        </div>
        <a href="{{ route('home') }}" class="text-xs font-semibold text-slate-400 transition hover:text-white">Discover people</a>
    </header>

    <div class="theme-card grid h-[calc(100dvh-15rem)] min-h-[20rem] max-h-[850px] overflow-hidden rounded-xl border border-slate-800/80 md:grid-cols-[18rem_minmax(0,1fr)]">
        <aside @class([
            'theme-inner flex min-h-0 flex-col overflow-hidden border-r border-slate-800/80',
            'hidden md:flex' => $hasOpenThread,
        ])>
            <div class="border-b border-slate-800/80 px-4 py-4">
                <h2 class="text-sm font-bold text-white">Inbox</h2>
            </div>

            <ul class="no-scrollbar min-h-0 flex-1 divide-y divide-slate-800/60 overflow-y-auto">
                @forelse($conversations as $conversation)
                    @php
                        $otherParticipants = $conversation->users->reject(fn ($conversationUser) => $conversationUser->is(auth()->user()));
                        $otherUser = $otherParticipants->first();
                        $conversationTitle = $otherParticipants->pluck('name')->implode(', ') ?: 'Conversation';
                        $conversationAvatar = $otherUser?->profile?->profile_picture;
                        $lastSeenAt = $otherUser?->last_seen_at;
                        $isOnline = (bool) $otherUser?->status && $lastSeenAt?->gte(now()->subMinutes(2));
                        $lastMessage = $conversation->latestMessage;
                    @endphp
                    <li wire:key="conversation-{{ $conversation->id }}">
                        <button type="button" wire:click="selectConversation({{ $conversation->id }})" @class([
                            'flex w-full items-center gap-3 px-4 py-3 text-left transition hover:bg-white/5',
                            'bg-white/5' => $activeConversationId === $conversation->id,
                        ])>
                            <span class="theme-soft-bg theme-text relative flex size-11 shrink-0 items-center justify-center overflow-hidden rounded-full text-sm font-bold">
                                @if($conversationAvatar)
                                    <img src="{{ asset('storage/' . $conversationAvatar) }}" alt="" class="size-full object-cover">
                                @else
                                    {{ strtoupper(substr($otherParticipants->first()?->name ?? 'C', 0, 1)) }}
                                @endif
                                <span class="absolute bottom-0 right-0 size-3 rounded-full border-2 border-(--bg-inner) {{ $isOnline ? 'bg-emerald-400' : 'bg-slate-500' }}"></span>
                            </span>
                            <span class="min-w-0 flex-1">
                                <span class="flex items-center justify-between gap-2">
                                    <span class="truncate text-sm font-semibold text-white">{{ $conversationTitle }}</span>
                                    @if($conversation->unread_messages_count > 0)
                                        <span class="theme-btn flex size-5 shrink-0 items-center justify-center rounded-full text-[10px] font-bold">{{ $conversation->unread_messages_count }}</span>
                                    @endif
                                </span>
                                <span class="mt-1 block truncate text-xs text-slate-400">{{ $lastMessage?->content ?? 'Start the conversation' }}</span>
                                <span class="mt-1 flex items-center gap-1.5 text-[10px] {{ $isOnline ? 'text-emerald-300' : 'text-slate-500' }}">
                                    <span class="size-1.5 rounded-full {{ $isOnline ? 'bg-emerald-400' : 'bg-slate-500' }}"></span>
                                    {{ $isOnline ? 'Active now' : ($lastSeenAt ? 'Inactive · active ' . $lastSeenAt->diffForHumans() : 'Inactive') }}
                                </span>
                            </span>
                        </button>
                    </li>
                @empty
                    <li class="flex h-full flex-col items-center justify-center px-6 py-12 text-center">
                        <x-heroicon-o-chat-bubble-left-right class="size-8 text-slate-500" />
                        <p class="mt-3 text-sm font-semibold text-white">Your inbox is empty</p>
                        <p class="mt-1 text-xs leading-5 text-slate-400">Open a creator profile and tap Message to start chatting.</p>
                        <a href="{{ route('home') }}" class="mt-4 text-xs font-bold theme-text hover:underline">Explore the feed</a>
                    </li>
                @endforelse
            </ul>
        </aside>

        <section @class([
            'flex min-h-0 min-w-0 flex-col overflow-hidden',
            'hidden md:flex' => ! $hasOpenThread,
        ])>
            @if($hasOpenThread)
                <header class="flex shrink-0 items-center gap-3 border-b border-slate-800/80 px-4 py-3">
                    <button type="button" wire:click="showInbox" aria-label="Back to inbox" class="flex size-9 shrink-0 items-center justify-center rounded-lg text-slate-400 hover:bg-white/5 hover:text-white md:hidden">
                        <x-heroicon-o-arrow-left class="size-5" />
                    </button>
                    <span class="theme-soft-bg theme-text relative flex size-10 shrink-0 items-center justify-center overflow-hidden rounded-full text-sm font-bold">
                        @if($threadAvatar)
                            <img src="{{ asset('storage/' . $threadAvatar) }}" alt="" class="size-full object-cover">
                        @else
                            {{ strtoupper(substr($threadParticipants->first()?->name ?? 'C', 0, 1)) }}
                        @endif
                        @if($activeConversation?->users->count() <= 2)
                            <span class="absolute bottom-0 right-0 size-3 rounded-full border-2 border-(--bg-card) {{ $threadIsOnline ? 'bg-emerald-400' : 'bg-slate-500' }}"></span>
                        @endif
                    </span>
                    <div class="min-w-0">
                        <h2 class="truncate text-sm font-bold text-white">{{ $threadTitle }}</h2>
                        <p class="text-[11px] {{ $activeConversation?->users->count() > 2 ? 'text-slate-400' : ($threadIsOnline ? 'text-emerald-300' : 'text-slate-500') }}">
                            {{ $activeConversation?->users->count() > 2 ? 'Group conversation' : ($threadIsOnline ? 'Active now' : ($threadLastSeen ? 'Inactive · active ' . $threadLastSeen->diffForHumans() : 'Inactive')) }}
                        </p>
                    </div>
                </header>

                <div x-data x-init="$nextTick(() => $el.scrollTop = $el.scrollHeight)" x-on:conversation-scroll-bottom.window="$nextTick(() => $el.scrollTo({ top: $el.scrollHeight, behavior: 'smooth' }))" class="no-scrollbar min-h-0 flex-1 space-y-4 overflow-y-auto px-4 py-5" aria-live="polite">
                    @forelse($messages as $message)
                        @php $isOwnMessage = $message->sender_id === auth()->id(); @endphp
                        <div wire:key="message-{{ $message->id }}" class="flex {{ $isOwnMessage ? 'justify-end' : 'justify-start' }}">
                            <div class="max-w-[85%] sm:max-w-[72%]">
                                @if(!$isOwnMessage && $activeConversation?->users->count() > 2)
                                    <p class="mb-1 px-1 text-[10px] font-semibold text-slate-500">{{ $message->sender->name }}</p>
                                @endif
                                <div @class([
                                    'rounded-2xl px-3.5 py-2.5 text-sm leading-5',
                                    'theme-btn rounded-br-sm' => $isOwnMessage,
                                    'theme-inner rounded-bl-sm text-slate-100' => ! $isOwnMessage,
                                ])>
                                    <p class="whitespace-pre-wrap break-words">{{ $message->content }}</p>
                                </div>
                                <time datetime="{{ $message->created_at->toIso8601String() }}" class="mt-1 block px-1 text-[10px] text-slate-500 {{ $isOwnMessage ? 'text-right' : '' }}">
                                    {{ $message->created_at->format('g:i A') }}
                                </time>
                            </div>
                        </div>
                    @empty
                        <div class="flex h-full min-h-48 flex-col items-center justify-center text-center">
                            <p class="text-sm font-semibold text-white">Say hello to {{ $threadParticipants->first()?->name }}</p>
                            <p class="mt-1 text-xs text-slate-400">Your messages are private to this conversation.</p>
                        </div>
                    @endforelse
                </div>

                <form wire:submit="sendMessage" class="shrink-0 border-t border-slate-800/80 p-3">
                    <div class="theme-inner flex items-end gap-2 rounded-xl border border-slate-700/80 p-2">
                        <textarea wire:model="messageContent" rows="1" maxlength="2000" placeholder="Write a message..." class="max-h-32 min-h-10 flex-1 resize-y bg-transparent px-2 py-2 text-sm text-white placeholder:text-slate-500 focus:outline-none"></textarea>
                        <button type="submit" aria-label="Send message" class="theme-btn flex size-10 shrink-0 items-center justify-center rounded-lg disabled:opacity-50" wire:loading.attr="disabled" wire:target="sendMessage">
                            <x-heroicon-o-paper-airplane class="size-5" />
                        </button>
                    </div>
                    @error('messageContent')
                        <p class="px-2 pt-2 text-xs text-rose-400">{{ $message }}</p>
                    @enderror
                </form>
            @else
                <div class="flex flex-1 flex-col items-center justify-center px-6 text-center">
                    <span class="flex size-16 items-center justify-center rounded-full border border-slate-700 text-slate-300">
                        <x-heroicon-o-paper-airplane class="size-7" />
                    </span>
                    <h2 class="mt-4 text-lg font-semibold text-white">Your messages</h2>
                    <p class="mt-2 max-w-xs text-sm text-slate-400">Choose a conversation or open a creator profile to start a private chat.</p>
                </div>
            @endif
        </section>
    </div>
</div>
