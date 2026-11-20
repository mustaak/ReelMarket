@php
    $viewer = auth()->user();

    $hasOpenThread = $activeConversation || $recipient;

    $threadUser = null;

    if ($activeConversation) {
        $threadUser = $activeConversation->users->first(fn($user) => !$user->is($viewer));
    } elseif ($recipient) {
        $threadUser = $recipient;
    }

    $threadTitle = $threadUser?->name ?? 'Messages';
    $threadAvatar = $threadUser?->profile?->avatar ?? ($threadUser?->profile?->avatar_url ?? null);
    $requestCount = $requests->count();
@endphp

<div wire:key="messages-page"
    wire:poll.5s="refreshMessages"
    x-data="{ tab: 'primary', emojiPickerOpen: false }"
    x-on:conversation-scroll-bottom.window="
        $nextTick(() => {
            const el = document.getElementById('messages-scroll');

            if (el) {
                el.scrollTop = el.scrollHeight;
            }
        })
    "
    class="editorial-messages w-full px-3 py-3 sm:px-5 sm:py-5 lg:px-7">
    {{-- ============================================================ --}}
    {{-- MESSAGES CONTAINER --}}
    {{-- ============================================================ --}}

    <div
        class="
            mx-auto flex h-[calc(100dvh-140px)] min-h-[500px] max-h-[900px] w-full max-w-[1440px] overflow-hidden
            rounded-[28px]
            border
            border-[#262626]
            bg-[#0d0d0d]
            shadow-[0_24px_80px_rgba(0,0,0,0.18)]
        ">


        {{-- ======================================================== --}}
        {{-- SIDEBAR --}}
        {{-- ======================================================== --}}

        <aside
            class="
                flex
                w-full
                shrink-0
                flex-col
                border-r
                border-[#262626]
                bg-[#101010]
                md:w-[340px]
                lg:w-[370px]
                {{ $hasOpenThread ? 'hidden md:flex' : 'flex' }}
            ">

            {{-- ---------------------------------------------------- --}}
            {{-- SIDEBAR HEADER --}}
            {{-- ---------------------------------------------------- --}}

            <div class="px-5 pb-4 pt-6">

                <div class="flex items-center justify-between">

                    <div class="min-w-0">

                        <p class="truncate text-[19px] font-semibold tracking-tight text-white">
                            {{ $viewer->username ?? $viewer->name }}
                        </p>

                        <p class="mt-0.5 text-xs text-[#737373]">
                            Messages
                        </p>

                    </div>
                </div>

            </div>


            {{-- ---------------------------------------------------- --}}
            {{-- SEARCH --}}
            {{-- ---------------------------------------------------- --}}

            <div class="px-4 pb-4">

                <div
                    class="
                        flex
                        h-10
                        items-center
                        rounded-xl
                        border
                        border-[#252525]
                        bg-[#191919]
                        px-3
                        transition
                        focus-within:border-[#3a3a3a]
                    ">

                    <svg class="mr-2.5 h-[17px] w-[17px] text-[#707070]" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="2">
                        <circle cx="11" cy="11" r="7" />

                        <path stroke-linecap="round" d="m20 20-4-4" />
                    </svg>

                    <input
                        type="text"
                        placeholder="Search messages or people..."
                        wire:model.live.debounce.300ms="search"
                        class="w-full bg-transparent text-[14px] text-white outline-none placeholder:text-[#666]"
                    >

                </div>

            </div>


            {{-- ---------------------------------------------------- --}}
            {{-- QUICK USERS --}}
            {{-- ---------------------------------------------------- --}}

            <div class="border-b border-[#262626] px-4 pb-4">

                <div class="flex gap-4 overflow-x-auto no-scrollbar py-2">

                    {{-- Recent users --}}

                    @foreach ($conversations->take(4) as $storyConversation)
                        @php
                            $storyUser = $storyConversation->users->first(fn($user) => !$user->is($viewer));

                            $storyAvatar = $storyUser?->profile?->avatar ?? ($storyUser?->profile?->avatar_url ?? null);
                        @endphp

                        <button type="button" wire:click="selectConversation({{ $storyConversation->id }})"
                            class="flex w-[60px] shrink-0 flex-col items-center cursor-pointer">

                            <div class="relative">

                                @if ($storyAvatar)
                                    <img src="{{ $storyAvatar }}" alt="{{ $storyUser?->name }}"
                                        class="h-14 w-14 rounded-full object-cover">
                                @else
                                    <div
                                        class="flex h-14 w-14 items-center justify-center rounded-full bg-[#422711] text-[17px] font-medium text-[#ff8a1c]">
                                        {{ strtoupper(substr($storyUser?->name ?? 'U', 0, 1)) }}
                                    </div>
                                @endif

                            </div>

                            <span class="mt-1.5 w-full truncate text-center text-[11px] text-[#b8b8b8]">
                                {{ $storyUser?->name }}
                            </span>

                        </button>
                    @endforeach

                </div>

            </div>


            {{-- ---------------------------------------------------- --}}
            {{-- TABS --}}
            {{-- ---------------------------------------------------- --}}

            <div class="grid grid-cols-3 border-b border-[#262626]">

                <button type="button" @click="tab = 'primary'" class="relative h-12 text-[13px] font-semibold"
                    :class="tab === 'primary' ? 'text-white' : 'text-[#777]'">
                    Primary

                    <span
                        x-bind:class="tab === 'primary' ? 'absolute bottom-0 left-4 right-4 h-0.5 bg-[#ff7a00]' : 'hidden'"></span>
                </button>


                <button type="button" @click="tab = 'requests'"
                    class="relative flex h-12 items-center justify-center gap-1.5 text-[13px] font-semibold"
                    :class="tab === 'requests' ? 'text-white' : 'text-[#777]'">
                    Requests

                    @if ($requestCount > 0)
                        <span
                            class="flex h-[18px] min-w-[18px] items-center justify-center rounded-full bg-[#ff7a00] px-1 text-[10px] font-bold text-black">
                            {{ $requestCount }}
                        </span>
                    @endif

                    <span
                        x-bind:class="tab === 'requests' ? 'absolute bottom-0 left-4 right-4 h-0.5 bg-[#ff7a00]' : 'hidden'"></span>

                </button>

            </div>


            {{-- ==================================================== --}}
            {{-- PRIMARY --}}
            {{-- ==================================================== --}}

            <div x-bind:class="tab === 'primary' ? 'flex' : 'hidden'" class="min-h-0 flex-1 flex-col overflow-y-auto">

                @forelse ($conversations as $conversation)

                    @php
                        $otherUser = $conversation->users->first(fn($user) => !$user->is($viewer));

                        $avatar = $otherUser?->profile?->avatar ?? ($otherUser?->profile?->avatar_url ?? null);

                        $latestMessage = $conversation->latestMessage;

                        $isActive = $activeConversationId === $conversation->id;
                    @endphp


                    <div wire:key="conversation-{{ $conversation->id }}"
                        class="
                            group
                            flex
                            w-full
                            items-center
                            gap-3
                            border-l-2
                            px-4
                            py-3.5
                            text-left
                            transition
                            {{ $isActive ? 'border-[#ff7a00] bg-[#1b1b1b]' : 'border-transparent hover:bg-[#171717]' }}
                        ">
                        <button type="button" wire:click="selectConversation({{ $conversation->id }})"
                            class="flex min-w-0 flex-1 items-center gap-3 text-left">

                        {{-- Avatar --}}

                        <div class="relative shrink-0">

                            @if ($avatar)
                                <img src="{{ $avatar }}" alt="{{ $otherUser?->name }}"
                                    class="h-12 w-12 rounded-full object-cover">
                            @else
                                <div
                                    class="flex h-12 w-12 items-center justify-center rounded-full bg-[#422711] text-[16px] font-medium text-[#ff8a1c]">
                                    {{ strtoupper(substr($otherUser?->name ?? 'U', 0, 1)) }}
                                </div>
                            @endif

                        </div>


                        {{-- Conversation --}}

                        <div class="min-w-0 flex-1">

                            <div class="flex items-center justify-between gap-2">

                                <p class="truncate text-[14px] font-semibold text-white">
                                    {{ $otherUser?->name ?? 'Unknown User' }}
                                </p>

                                @if ($latestMessage)
                                    <span class="shrink-0 text-[10px] text-[#666]">
                                        {{ $latestMessage->created_at?->diffForHumans(null, true) }}
                                    </span>
                                @endif

                            </div>

                            <div class="mt-1 flex items-center gap-2">

                                <p class="min-w-0 flex-1 truncate text-[12px] text-[#777]">

                                    @if ($latestMessage)
                                        @if ($latestMessage->sender_id === $viewer->id)
                                            You:
                                        @endif

                                        {{ $latestMessage->content !== '' ? $latestMessage->content : ($latestMessage->attachment_name ? 'Attachment: '.$latestMessage->attachment_name : ($latestMessage->gif_url ? 'GIF' : '')) }}
                                    @else
                                        Start a conversation
                                    @endif

                                </p>

                                @if ($conversation->unread_messages_count > 0)
                                    <span class="h-2 w-2 shrink-0 rounded-full bg-[#ff7a00]"></span>
                                @endif

                            </div>

                        </div>

                        </button>

                    </div>

                @empty

                    <div class="flex flex-1 items-center justify-center px-5">

                        <div class="text-center">

                            <div class="mx-auto flex h-11 w-11 items-center justify-center rounded-full bg-[#191919]">
                                <svg class="h-5 w-5 text-[#666]" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor" stroke-width="1.6">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M8 10h8M8 14h5m7-2a8 8 0 01-8 8 8.3 8.3 0 01-3.7-.9L4 20l.9-3.3A8 8 0 1120 12z" />
                                </svg>
                            </div>

                            <p class="mt-3 text-sm text-[#777]">
                                No conversations yet
                            </p>

                        </div>

                    </div>

                @endforelse

            </div>


            {{-- ==================================================== --}}
            {{-- REQUESTS --}}
            {{-- ==================================================== --}}

            <div x-bind:class="tab === 'requests' ? 'flex' : 'hidden'"
                class="min-h-0 flex-1 flex-col overflow-y-auto">

                @forelse ($requests as $request)
                    @php
                        $requestUser = $request->users->first(fn($user) => !$user->is($viewer));

                        $avatar = $requestUser?->profile?->avatar ?? ($requestUser?->profile?->avatar_url ?? null);

                        $latestMessage = $request->latestMessage;
                    @endphp


                    <div wire:key="request-{{ $request->id }}" class="border-b border-[#222] px-4 py-4">

                        <div class="flex gap-3">

                            <div class="relative shrink-0">

                                @if ($avatar)
                                    <img src="{{ $avatar }}" alt="{{ $requestUser?->name }}"
                                        class="h-12 w-12 rounded-full object-cover">
                                @else
                                    <div
                                        class="flex h-12 w-12 items-center justify-center rounded-full bg-[#422711] text-[16px] text-[#ff8a1c]">
                                        {{ strtoupper(substr($requestUser?->name ?? 'U', 0, 1)) }}
                                    </div>
                                @endif

                            </div>


                            <div class="min-w-0 flex-1">

                                <p class="truncate text-[14px] font-semibold text-white">
                                    {{ $requestUser?->name ?? 'Unknown User' }}
                                </p>

                                <p class="mt-0.5 text-[11px] text-[#707070]">
                                    Wants to message you
                                </p>

                                @if ($latestMessage)
                                    <p class="mt-2 line-clamp-2 text-[12px] leading-5 text-[#aaa]">
                                        {{ $latestMessage->content !== '' ? $latestMessage->content : ($latestMessage->attachment_name ? 'Attachment: '.$latestMessage->attachment_name : ($latestMessage->gif_url ? 'GIF' : '')) }}
                                    </p>
                                @endif


                                <div class="mt-3 flex gap-2">

                                    <button type="button" wire:click="acceptRequest({{ $request->id }})"
                                        wire:loading.attr="disabled"
                                        class="flex-1 rounded-lg bg-[#ff7a00] px-3 py-2 text-xs font-semibold text-black transition hover:bg-[#ff8b20] disabled:opacity-50">
                                        Accept
                                    </button>

                                    <button type="button" wire:click="declineRequest({{ $request->id }})"
                                        wire:loading.attr="disabled"
                                        class="flex-1 rounded-lg border border-[#303030] bg-[#181818] px-3 py-2 text-xs font-semibold text-[#ddd] transition hover:bg-[#222] disabled:opacity-50">
                                        Decline
                                    </button>

                                </div>

                            </div>

                        </div>

                    </div>

                @empty

                    <div class="flex flex-1 items-center justify-center px-5">

                        <div class="text-center">

                            <div class="mx-auto flex h-11 w-11 items-center justify-center rounded-full bg-[#191919]">
                                <svg class="h-5 w-5 text-[#666]" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor" stroke-width="1.6">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M8 10h8M8 14h5m7-2a8 8 0 01-8 8 8.3 8.3 0 01-3.7-.9L4 20l.9-3.3A8 8 0 1120 12z" />
                                </svg>
                            </div>

                            <p class="mt-3 text-sm text-[#777]">
                                No message requests
                            </p>

                        </div>

                    </div>
                @endforelse

            </div>

        </aside>


        {{-- ======================================================== --}}
        {{-- CHAT AREA --}}
        {{-- ======================================================== --}}

        <section
            class="
                relative
                min-w-0
                flex-1
                flex-col
                bg-[#050505]
                {{ $hasOpenThread ? 'flex' : 'hidden md:flex' }}
            ">

            @if ($hasOpenThread)

                {{-- ================================================= --}}
                {{-- CHAT HEADER --}}
                {{-- ================================================= --}}

                <header class="flex h-[72px] shrink-0 items-center border-b border-[#262626] px-5">

                    {{-- Mobile back --}}

                    <button type="button" wire:click="showInbox"
                        class="mr-3 flex h-9 w-9 items-center justify-center rounded-full text-[#777] hover:bg-[#1b1b1b] md:hidden">

                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                            stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 18l-6-6 6-6" />
                        </svg>

                    </button>


                    {{-- Avatar --}}

                    <div class="relative shrink-0">

                        @if ($threadAvatar)
                            <img src="{{ $threadAvatar }}" alt="{{ $threadTitle }}"
                                class="h-11 w-11 rounded-full object-cover">
                        @else
                            <div
                                class="flex h-11 w-11 items-center justify-center rounded-full bg-[#422711] text-[16px] font-medium text-[#ff8a1c]">
                                {{ strtoupper(substr($threadTitle, 0, 1)) }}
                            </div>
                        @endif

                    </div>


                    <div class="ml-3 min-w-0">

                        <h2 class="truncate text-[15px] font-semibold text-white">
                            {{ $threadTitle }}
                        </h2>

                        <p class="mt-0.5 text-[11px] text-[#777]">
                            Direct conversation
                        </p>

                    </div>

                </header>

                {{-- ================================================= --}}
                {{-- MESSAGES --}}
                {{-- ================================================= --}}

                <div id="messages-scroll" class="min-h-0 flex-1 overflow-y-auto px-5 py-6 sm:px-8">

                    @if ($activeConversation)

                        <div class="mb-8 flex items-center gap-4">

                            <div class="h-px flex-1 bg-[#191919]"></div>

                            <span class="text-[11px] font-medium text-[#555]">
                                Today
                            </span>

                            <div class="h-px flex-1 bg-[#191919]"></div>

                        </div>


                        @forelse ($messages as $message)
                            @php
                                $isMine = $message->sender_id === $viewer->id;
                            @endphp


                            <div wire:key="message-{{ $message->id }}"
                                class="mb-5 flex {{ $isMine ? 'justify-end' : 'justify-start' }}">

                                @if (!$isMine)
                                    <div class="mr-2.5 shrink-0 self-end">

                                        @if ($threadAvatar)
                                            <img src="{{ $threadAvatar }}" alt=""
                                                loading="lazy" decoding="async"
                                                class="h-8 w-8 rounded-full object-cover">
                                        @else
                                            <div
                                                class="flex h-8 w-8 items-center justify-center rounded-full bg-[#422711] text-[12px] text-[#ff8a1c]">
                                                {{ strtoupper(substr($threadTitle, 0, 1)) }}
                                            </div>
                                        @endif

                                    </div>
                                @endif


                                <div class="max-w-[72%]">

                                    <div
                                        class="
                                            px-4
                                            py-2.5
                                            text-[14px]
                                            leading-5
                                            shadow-sm
                                            {{ $isMine
                                                ? 'rounded-[20px] rounded-br-md bg-[#ff7a00] text-black'
                                                : 'rounded-[20px] rounded-bl-md bg-[#1b1b1d] text-[#ededed]' }}
                                        ">
                                        @if ($message->content !== '')
                                            <p class="whitespace-pre-wrap break-words">{{ $message->content }}</p>
                                        @endif

                                        @if ($message->gif_url)
                                            <img src="{{ $message->gif_url }}" alt="Animated GIF" loading="lazy"
                                                class="mt-2 max-h-72 max-w-full rounded-xl object-contain">
                                        @endif

                                        @if ($message->attachment_path)
                                            <a href="{{ route('messages.attachments.show', $message) }}"
                                                class="{{ $message->content !== '' ? 'mt-2 block' : 'block' }}"
                                                @if (!str_starts_with((string) $message->attachment_mime, 'image/')) download @endif>
                                                @if (str_starts_with((string) $message->attachment_mime, 'image/'))
                                                    <img src="{{ route('messages.attachments.show', $message) }}"
                                                        alt="{{ $message->attachment_name }}"
                                                        loading="lazy"
                                                        class="max-h-64 max-w-full rounded-xl object-contain">
                                                @else
                                                    <span class="flex items-center gap-2 text-sm underline">
                                                        <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24"
                                                            stroke="currentColor" stroke-width="1.7">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                d="M12 16V4m0 12 4-4m-4 4-4-4M4 20h16" />
                                                        </svg>
                                                        <span class="break-all">{{ $message->attachment_name }}</span>
                                                    </span>
                                                @endif
                                            </a>
                                        @endif
                                    </div>


                                    <div
                                        class="
                                            mt-1
                                            text-[10px]
                                            text-[#555]
                                            {{ $isMine ? 'text-right' : 'text-left' }}
                                        ">
                                        {{ $message->created_at?->format('g:i A') }}

                                        @if ($isMine)
                                            ·
                                            {{ $message->read_at ? 'Seen' : 'Sent' }}
                                        @endif
                                    </div>

                                </div>

                            </div>

                        @empty

                            <div class="flex h-full items-center justify-center">

                                <div class="text-center">

                                    <div
                                        class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-[#151515]">
                                        <svg class="h-5 w-5 text-[#666]" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor" stroke-width="1.6">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M8 10h8M8 14h5m7-2a8 8 0 01-8 8 8.3 8.3 0 01-3.7-.9L4 20l.9-3.3A8 8 0 1120 12z" />
                                        </svg>
                                    </div>

                                    <p class="mt-3 text-sm text-[#666]">
                                        No messages yet
                                    </p>

                                </div>

                            </div>
                        @endforelse
                    @else
                        {{-- New conversation --}}

                        <div class="flex h-full items-center justify-center">

                            <div class="text-center">

                                @if ($threadAvatar)
                                    <img src="{{ $threadAvatar }}" alt="{{ $threadTitle }}"
                                        class="mx-auto h-16 w-16 rounded-full object-cover">
                                @else
                                    <div
                                        class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-[#422711] text-xl text-[#ff8a1c]">
                                        {{ strtoupper(substr($threadTitle, 0, 1)) }}
                                    </div>
                                @endif

                                <h2 class="mt-4 text-base font-semibold text-white">
                                    {{ $threadTitle }}
                                </h2>

                                <p class="mt-1 text-xs text-[#666]">
                                    Send a message to start the conversation.
                                </p>

                            </div>

                        </div>

                    @endif

                </div>


                {{-- ================================================= --}}
                {{-- COMPOSER --}}
                {{-- ================================================= --}}

                <div class="shrink-0 border-t border-[#191919] bg-[#0d0d0d]/90 px-4 py-4 sm:px-6">
                    <form wire:submit="sendMessage" class="flex flex-col gap-2">
                        @if ($attachment)
                            <div class="flex items-center justify-between gap-3 rounded-xl border border-[#292929] bg-[#111111] px-3 py-2 text-xs text-[#b8b8b8]">
                                <span class="truncate">{{ $attachment->getClientOriginalName() }}</span>
                                <button type="button" wire:click="removeAttachment" class="shrink-0 font-medium text-rose-500">Remove</button>
                            </div>
                        @endif

                        @if ($gifUrl)
                            <div class="flex items-center justify-between gap-3 rounded-xl border border-[#292929] bg-[#111111] px-3 py-2 text-xs text-[#b8b8b8]">
                                <div class="flex min-w-0 items-center gap-3">
                                    <img src="{{ $gifUrl }}" alt="Selected GIF" class="h-12 w-16 rounded-lg object-cover">
                                    <span class="truncate">GIF attached</span>
                                </div>
                                <button type="button" wire:click="removeGif" class="shrink-0 font-medium text-rose-500">Remove</button>
                            </div>
                        @endif

                        <div class="flex items-end gap-2">
                            <label class="flex h-11 w-11 shrink-0 cursor-pointer items-center justify-center rounded-full border border-[#292929] text-[#707070] transition hover:bg-[#171717] hover:text-white"
                                aria-label="Attach a photo or file" title="Attach a photo or file">
                                <input type="file" wire:model="attachment"
                                    accept=".jpg,.jpeg,.png,.gif,.webp,.pdf,.doc,.docx,.xls,.xlsx,.csv,.txt"
                                    class="sr-only">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.7">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m21.4 11.6-8.5 8.5a6 6 0 01-8.5-8.5l9.2-9.2a4 4 0 015.7 5.7l-9.2 9.2a2 2 0 01-2.8-2.8l8.5-8.5" />
                                </svg>
                            </label>

                            <div class="relative" x-data="chatGifPicker(@js(config('services.giphy.api_key')))" x-on:click.outside="isOpen = false">
                                <button type="button" x-on:click="toggle()"
                                    class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full border border-[#292929] text-[11px] font-bold tracking-wide text-[#707070] transition hover:bg-[#171717] hover:text-white"
                                    aria-label="Search GIFs" title="Search GIFs">GIF</button>

                                <section x-show="isOpen" x-cloak
                                    class="absolute bottom-14 left-0 z-30 flex max-h-[min(440px,70vh)] w-[min(360px,calc(100vw-2rem))] flex-col overflow-hidden rounded-2xl border border-[#292929] bg-[#111111] shadow-2xl sm:left-auto sm:right-0"
                                    aria-label="GIF picker">
                                    <div class="border-b border-[#292929] p-3">
                                        <input type="search" x-model="query" x-on:input.debounce.350ms="loadGifs()"
                                            placeholder="Search GIFs"
                                            class="h-10 w-full rounded-xl border border-[#292929] bg-[#191919] px-3 text-sm text-white outline-none placeholder:text-[#777] focus:border-[#555]">
                                        <p class="mt-2 text-[10px] font-semibold uppercase tracking-[0.16em] text-[#777]">
                                            Trending · Powered by GIPHY
                                        </p>
                                    </div>

                                    <div class="min-h-28 flex-1 overflow-y-auto p-2">
                                        <div x-show="isLoading" class="flex h-28 items-center justify-center text-xs text-[#999]">
                                            Loading GIFs…
                                        </div>
                                        <p x-show="error" x-text="error" class="px-3 py-5 text-center text-xs leading-5 text-[#aaa]"></p>
                                        <div x-show="!isLoading && !error && gifs.length === 0" class="px-3 py-5 text-center text-xs text-[#aaa]">
                                            No GIFs found. Try another search.
                                        </div>
                                        <div x-show="!isLoading && gifs.length > 0" class="grid grid-cols-3 gap-2">
                                            <template x-for="gif in gifs" :key="gif.id">
                                                <button type="button" x-on:click="selectGif(gif)"
                                                    class="group overflow-hidden rounded-lg bg-[#191919] focus:outline-none focus:ring-2 focus:ring-[var(--accent-primary)]"
                                                    :aria-label="`Send ${gif.title}`">
                                                    <img :src="gif.preview" :alt="gif.title" loading="lazy"
                                                        class="h-24 w-full object-cover transition group-hover:scale-105">
                                                </button>
                                            </template>
                                        </div>
                                    </div>
                                    <a href="https://giphy.com" target="_blank" rel="noopener noreferrer"
                                        class="border-t border-[#292929] px-3 py-2 text-center text-[10px] text-[#888] hover:text-white">
                                        Powered by GIPHY
                                    </a>
                                </section>
                            </div>

                            <div class="relative">
                                <button type="button" x-on:click="emojiPickerOpen = !emojiPickerOpen"
                                    class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full border border-[#292929] text-xl text-[#707070] transition hover:bg-[#171717] hover:text-white"
                                    aria-label="Open emoji picker" title="Emoji">😊</button>
                                <div x-show="emojiPickerOpen" x-cloak x-on:click.outside="emojiPickerOpen = false"
                                    class="absolute bottom-14 left-0 z-30 overflow-hidden rounded-2xl border border-[#292929] shadow-2xl sm:left-auto sm:right-0">
                                    <div wire:ignore>
                                        <emoji-picker x-on:emoji-click="
                                            const input = $refs.messageInput;
                                            if (!input) return;

                                            const start = input.selectionStart;
                                            const end = input.selectionEnd;
                                            input.value = input.value.slice(0, start) + $event.detail.unicode + input.value.slice(end);
                                            input.setSelectionRange(start + $event.detail.unicode.length, start + $event.detail.unicode.length);
                                            input.dispatchEvent(new Event('input', { bubbles: true }));
                                            input.focus();
                                        "></emoji-picker>
                                    </div>
                                </div>
                            </div>

                        <div
                            class="
                                flex
                                min-h-[44px]
                                flex-1
                                items-center
                                rounded-[22px]
                                border
                                border-[#292929]
                                bg-[#111111]
                                px-4
                                transition
                                focus-within:border-[#404040]
                            ">

                            <textarea x-ref="messageInput" wire:model="messageContent" rows="1" maxlength="2000" placeholder="Write a message..."
                                class="max-h-32 min-h-[24px] flex-1 resize-none bg-transparent py-3 text-[14px] text-white outline-none placeholder:text-[#5f5f5f]"
                                x-on:keydown.enter="
                                    if (!$event.shiftKey) {
                                        $event.preventDefault();
                                        $el.closest('form').requestSubmit();
                                    }
                                "></textarea>

                        </div>


                        <button type="submit" wire:loading.attr="disabled" wire:target="sendMessage"
                            class="
                                flex
                                h-11
                                w-12
                                shrink-0
                                items-center
                                justify-center
                                rounded-full
                                bg-[#ff7a00]
                                text-black
                                transition
                                hover:bg-[#ff8b20]
                                disabled:opacity-50
                            ">

                            <span wire:loading.remove wire:target="sendMessage">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M5 12h14M13 5l7 7-7 7" />
                                </svg>
                            </span>

                            <span wire:loading wire:target="sendMessage">
                                <svg class="h-5 w-5 animate-pulse" fill="currentColor"
                                    viewBox="0 0 24 24">
                                    <circle cx="5" cy="12" r="2" />
                                    <circle cx="12" cy="12" r="2" />
                                    <circle cx="19" cy="12" r="2" />
                                </svg>
                            </span>

                        </button>
                        </div>

                    </form>


                    @error('messageContent')
                        <p class="mt-2 px-2 text-xs text-red-500">
                            {{ $message }}
                        </p>
                    @enderror
                    @error('attachment')
                        <p class="mt-2 px-2 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                    @error('gifUrl')
                        <p class="mt-2 px-2 text-xs text-red-500">{{ $message }}</p>
                    @enderror

                    <div wire:loading wire:target="attachment" class="mt-2 px-2 text-xs text-[#777]">
                        Uploading attachment…
                    </div>

                </div>
            @else
                {{-- ================================================= --}}
                {{-- NO CHAT SELECTED --}}
                {{-- ================================================= --}}

                <div class="flex flex-1 items-center justify-center">

                    <div class="text-center">

                        <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-[#151515]">

                            <svg class="h-6 w-6 text-[#666]" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M8 10h8M8 14h5m7-2a8 8 0 01-8 8 8.3 8.3 0 01-3.7-.9L4 20l.9-3.3A8 8 0 1120 12z" />
                            </svg>

                        </div>

                        <h2 class="mt-4 text-base font-semibold text-white">
                            Your messages
                        </h2>

                        <p class="mt-1 text-xs text-[#666]">
                            Select a conversation to start chatting.
                        </p>

                    </div>

                </div>

            @endif

        </section>

    </div>
</div>
