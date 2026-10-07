<div wire:poll.5s="refreshMessages" x-data="{ tab: 'primary' }"
    x-on:conversation-scroll-bottom.window="
        $nextTick(() => {
            const el = document.getElementById('messages-scroll');

            if (el) {
                el.scrollTop = el.scrollHeight;
            }
        })
    "
    class="w-full bg-[#090909] px-3 py-3 sm:px-5 sm:py-5 lg:px-7">
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


    {{-- ============================================================ --}}
    {{-- MESSAGES CONTAINER --}}
    {{-- ============================================================ --}}

    <div
        class="
            mx-auto flex h-[calc(100vh-150px)] min-h-[610px] w-full max-w-[1320px] overflow-hidden
            rounded-2xl
            border
            border-[#262626]
            bg-[#0d0d0d]
            shadow-2xl
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

                    <input type="text" placeholder="Search messages"
                        class="w-full bg-transparent text-[14px] text-white outline-none placeholder:text-[#666]">

                </div>

            </div>


            {{-- ---------------------------------------------------- --}}
            {{-- QUICK USERS --}}
            {{-- ---------------------------------------------------- --}}

            <div class="border-b border-[#262626] px-4 pb-4">

                <div class="flex gap-4 overflow-x-auto no-scrollbar py-2">

                    {{-- Add note button --}}

                    {{-- Add note --}}

                    <button type="button" class="flex w-[60px] shrink-0 flex-col items-center">

                        <div
                            class="
                                flex
                                h-14
                                w-14
                                items-center
                                justify-center
                                rounded-full
                                border
                                border-dashed
                                border-[#555]
                                bg-[#151515]
                            ">
                            <svg class="h-5 w-5 text-[#777]" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                stroke-width="1.6">
                                <path stroke-linecap="round" d="M12 5v14M5 12h14" />
                            </svg>
                        </div>

                        <span class="mt-1.5 w-full truncate text-center text-[11px] text-[#858585]">
                            Add note
                        </span>

                    </button>


                    {{-- Recent users --}}

                    @foreach ($conversations->take(4) as $storyConversation)
                        @php
                            $storyUser = $storyConversation->users->first(fn($user) => !$user->is($viewer));

                            $storyAvatar = $storyUser?->profile?->avatar ?? ($storyUser?->profile?->avatar_url ?? null);
                        @endphp

                        <button type="button" wire:click="selectConversation({{ $storyConversation->id }})"
                            class="flex w-[60px] shrink-0 flex-col items-center">

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

                                <span
                                    class="absolute bottom-0 right-0 h-3.5 w-3.5 rounded-full border-2 border-[#101010] bg-emerald-500"></span>

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


                    <button type="button" wire:key="conversation-{{ $conversation->id }}"
                        wire:click="selectConversation({{ $conversation->id }})"
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

                            <span
                                class="absolute bottom-0 right-0 h-3 w-3 rounded-full border-2 border-[#101010] bg-emerald-500"></span>

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

                                        {{ $latestMessage->content }}
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
                                        {{ $latestMessage->content }}
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

                        <span
                            class="absolute bottom-0 right-0 h-3.5 w-3.5 rounded-full border-2 border-[#050505] bg-emerald-500"></span>

                    </div>


                    <div class="ml-3 min-w-0">

                        <h2 class="truncate text-[15px] font-semibold text-white">
                            {{ $threadTitle }}
                        </h2>

                        <p class="mt-0.5 text-[11px] text-emerald-500">
                            Active now
                        </p>

                    </div>


                    {{-- Header actions --}}

                    <div class="ml-auto flex items-center gap-1">

                        <button type="button"
                            class="flex h-9 w-9 items-center justify-center rounded-full text-[#707070] hover:bg-[#171717] hover:text-white">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                stroke-width="1.7">
                                <circle cx="12" cy="12" r="9" />
                                <path stroke-linecap="round" d="M12 8v4l2.5 1.5" />
                            </svg>
                        </button>

                        <button type="button"
                            class="flex h-9 w-9 items-center justify-center rounded-full text-[#707070] hover:bg-[#171717] hover:text-white">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                stroke-width="1.7">
                                <circle cx="12" cy="5" r="1" />
                                <circle cx="12" cy="12" r="1" />
                                <circle cx="12" cy="19" r="1" />
                            </svg>
                        </button>

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
                                        {{ $message->content }}
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

                <div class="shrink-0 border-t border-[#191919] px-4 py-4 sm:px-6">

                    <form wire:submit="sendMessage" class="flex items-end gap-2">

                        <div
                            class="
                                flex
                                min-h-[48px]
                                flex-1
                                items-center
                                rounded-2xl
                                border
                                border-[#292929]
                                bg-[#111111]
                                px-4
                                transition
                                focus-within:border-[#404040]
                            ">

                            <textarea wire:model="messageContent" rows="1" maxlength="2000" placeholder="Message..."
                                class="max-h-32 min-h-[24px] flex-1 resize-none bg-transparent py-3 text-[14px] text-white outline-none placeholder:text-[#5f5f5f]"
                                x-data
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
                                h-12
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
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                    stroke-width="1.9">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M22 2L11 13" />

                                    <path stroke-linecap="round" stroke-linejoin="round" d="M22 2l-7 20-4-9 20-7z" />
                                </svg>
                            </span>


                            <span wire:loading wire:target="sendMessage">
                                <svg class="h-5 w-5 animate-spin" fill="none" viewBox="0 0 24 24">
                                    <circle cx="12" cy="12" r="9" stroke="currentColor"
                                        stroke-width="2" class="opacity-25" />

                                    <path d="M21 12a9 9 0 00-9-9" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" class="opacity-75" />
                                </svg>
                            </span>

                        </button>

                    </form>


                    @error('messageContent')
                        <p class="mt-2 px-2 text-xs text-red-500">
                            {{ $message }}
                        </p>
                    @enderror

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
