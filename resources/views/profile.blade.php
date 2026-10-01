<x-layouts.app>
    <main class="mx-auto max-w-[960px] px-4 pb-14 pt-8 sm:px-8 sm:pt-12 theme-card rounded">
        @if(session('success'))
            <p role="status" class="mb-6 rounded-lg border border-emerald-700/50 bg-emerald-950/40 px-4 py-3 text-sm text-emerald-200">
                {{ session('success') }}
            </p>
        @endif

        <section aria-label="Profile" class="grid grid-cols-[80px_minmax(0,1fr)] gap-x-5 gap-y-6 border-b border-slate-800/80 pb-8 sm:grid-cols-[140px_minmax(0,1fr)] sm:gap-x-10 md:grid-cols-[220px_minmax(0,1fr)] md:gap-x-14 md:pb-10">
            <div class="flex justify-center">
                <div class="bg-[conic-gradient(from_20deg,#f97316,#db2777,#7c3aed,#f97316)] aspect-square size-20 rounded-full p-[3px] sm:size-32 md:size-36">
                    <div class="flex size-full items-center justify-center overflow-hidden rounded-full bg-[#17121d] text-2xl font-bold text-white sm:text-4xl">
                        @if($profile->profile_picture)
                            <img src="{{ asset('storage/' . $profile->profile_picture) }}" alt="{{ $user->name }}" class="size-full object-cover">
                        @else
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        @endif
                    </div>
                </div>
            </div>

            <div class="min-w-0">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <h1 class="max-w-full truncate font-display text-xl font-semibold text-white sm:text-2xl">{{ $user->name }}</h1>
                    <div class="flex shrink-0 items-center gap-2">
                        @if($isOwnProfile)
                            <details class="relative">
                                <summary
                                    class="flex size-10 cursor-pointer list-none items-center justify-center rounded-xl border border-slate-700 text-slate-300 transition hover:bg-white/5 hover:text-white">
                                    <x-heroicon-o-ellipsis-horizontal class="size-5" />
                                </summary>
                                <div class="absolute right-10 top-full z-40 mt-2 w-48 overflow-hidden rounded-xl border border-slate-800 bg-[#140e26] shadow-2xl items-center">
                                    {{-- Orders --}}
                                    <a
                                        href="{{ route('orders.index') }}"
                                        class="flex items-center gap-3 px-4 py-3 text-xs font-semibold text-slate-300 transition hover:bg-white/5 hover:text-white">
                                        <x-heroicon-o-clipboard-document-list class="size-4" />
                                        My Orders
                                    </a>
                                    {{-- Logout --}}
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button
                                            type="submit"
                                            class="flex w-full items-center gap-3 px-4 py-3 text-left text-xs font-semibold text-slate-300 transition hover:bg-white/5 hover:text-white">
                                            <x-heroicon-o-arrow-right-start-on-rectangle class="size-4" />
                                            Logout
                                        </button>
                                    </form>
                                </div>
                            </details>
                            <a href="{{ route('profile.edit') }}" aria-label="Edit profile" title="Edit profile" class="flex size-9 items-center justify-center rounded-lg border border-slate-700 text-slate-200 transition hover:bg-white/5 hover:text-white">
                                <x-heroicon-o-pencil-square class="size-5" />
                            </a>
                            <a href="{{ route('posts.create') }}" aria-label="Create post" title="Create post" class="flex size-9 items-center justify-center rounded-lg border border-slate-700 text-slate-200 transition hover:bg-white/5 hover:text-white">
                                <x-heroicon-o-plus class="size-5" />
                            </a>
                            <a href="{{ route('reels.create') }}" aria-label="Create reel" title="Create reel" class="flex size-9 items-center justify-center rounded-lg border border-slate-700 text-slate-200 transition hover:bg-white/5 hover:text-white">
                                <x-heroicon-o-video-camera class="size-5" />
                            </a>
                        @else
                            @auth
                                <form method="POST" action="{{ route('users.follow.toggle', $user) }}">
                                    @csrf
                                    @php
                                        $followLabel = match (true) {
                                            $followStatus === 'accepted' => 'Following',
                                            $followStatus === 'pending' => 'Requested',
                                            $reverseFollowAccepted => 'Follow Back',
                                            default => 'Follow',
                                        };
                                        $followButtonClass = match (true) {
                                            $followStatus === 'accepted' =>
                                                'border border-slate-700 bg-white/5 text-white hover:bg-white/10',
                                            $followStatus === 'pending' =>
                                                'border border-slate-700 bg-white/5 text-white hover:bg-white/10',
                                            default => 'theme-btn',
                                        };
                                    @endphp
                                    <button
                                        type="submit"
                                        class="rounded-lg px-4 py-2 text-xs font-bold transition {{ $followButtonClass }}"
                                    >
                                        {{ $followLabel }}
                                    </button>
                                </form>
                            @else
                                <a href="{{ route('login') }}" class="theme-btn rounded-lg px-4 py-2 text-xs font-bold">Follow</a>
                            @endauth
                            @auth
                                @if(!$isPrivateProfile || $followStatus === 'accepted')
                                    <a href="{{ route('messages.index', ['user' => $user->id]) }}" aria-label="Message {{ $user->name }}" title="Message" class="flex size-9 items-center justify-center rounded-lg border border-slate-700 text-slate-200 transition hover:bg-white/5 hover:text-white">
                                        <x-heroicon-o-chat-bubble-left-right class="size-5" />
                                    </a>
                                @endif
                            @endauth
                        @endif
                        <a href="{{ route('home') }}" aria-label="Home" title="Home" class="flex size-9 items-center justify-center rounded-lg border border-slate-700 text-slate-300 transition hover:bg-white/5 hover:text-white">
                            <x-heroicon-o-home class="size-5" />
                        </a>
                    </div>
                </div>

                <dl class="mt-5 grid grid-cols-3 gap-2 sm:gap-8">
                    <div class="flex flex-col-reverse gap-0.5">
                        <dt class="text-[10px] text-slate-400 sm:text-xs">Posts</dt>
                        <dd class="text-sm font-bold text-white sm:text-base">{{ number_format($postsCount) }}</dd>
                    </div>
                    <div class="flex flex-col-reverse gap-0.5">
                        <dt class="text-[10px] text-slate-400 sm:text-xs">Followers</dt>
                        <dd class="text-sm font-bold text-white sm:text-base">
                            <a href="{{ $isOwnProfile ? route('profile', ['tab' => 'followers']) : route('users.show', ['user' => $user, 'tab' => 'followers']) }}" class="hover:theme-text">{{ number_format($followersCount) }}</a>
                        </dd>
                    </div>
                    <div class="flex flex-col-reverse gap-0.5">
                        <dt class="text-[10px] text-slate-400 sm:text-xs">Following</dt>
                        <dd class="text-sm font-bold text-white sm:text-base">
                            <a href="{{ $isOwnProfile ? route('profile', ['tab' => 'following']) : route('users.show', ['user' => $user, 'tab' => 'following']) }}" class="hover:theme-text">{{ number_format($followingCount) }}</a>
                        </dd>
                    </div>
                </dl>

                <div class="mt-4">
                    <p class="text-sm font-semibold text-slate-200">{{ ucfirst($user->type ?? 'creator') }}</p>
                    <p class="mt-1 whitespace-pre-line text-sm leading-6 text-slate-300">
                        {{ $profile->bio ?? 'Style enthusiast and creator.' }}
                    </p>
                    @if($isPrivateProfile)
                        <p class="mt-2 inline-flex items-center gap-1.5 text-xs font-medium text-slate-400">
                            <x-heroicon-o-lock-closed class="size-3.5" />
                            Private account
                        </p>
                    @endif
                </div>
            </div>
        </section>

        @if($isOwnProfile)
            <form method="POST" action="{{ route('profile.privacy.update') }}" class="flex flex-wrap items-center justify-between gap-4 border-b border-slate-800/80 py-4">
                @csrf
                @method('PATCH')
                <label for="is_private" class="flex min-w-0 items-center gap-3">
                    <input id="is_private" name="is_private" type="checkbox" value="1" @checked(!$profile->is_public) class="size-4 shrink-0 accent-amber-500">
                    <span>
                        <span class="block text-sm font-semibold text-white">Private account</span>
                        <span class="mt-0.5 block text-xs text-slate-400">Only accepted followers can see your posts and reels.</span>
                    </span>
                </label>
                <button type="submit" class="rounded-lg border border-slate-700 px-3 py-2 text-xs font-bold text-slate-200 transition hover:bg-white/5 hover:text-white">
                    Save privacy
                </button>
            </form>
        @endif

        @if($isOwnProfile && $pendingFollowRequests->isNotEmpty())
            <section aria-labelledby="follow-requests-heading" class="border-b border-slate-800/80 py-6">
                <div class="flex items-center justify-between gap-3">
                    <h2 id="follow-requests-heading" class="text-sm font-semibold text-white">Follow requests</h2>
                    <span class="text-xs text-slate-500">{{ $pendingFollowRequests->count() }} pending</span>
                </div>
                <ul class="mt-4 divide-y divide-slate-800/70">
                    @foreach($pendingFollowRequests as $followRequest)
                        <li class="flex items-center justify-between gap-3 py-3">
                            <a href="{{ route('users.show', $followRequest->follower) }}" class="min-w-0 truncate text-sm font-medium text-slate-200 hover:text-white">
                                {{ $followRequest->follower->name }}
                            </a>
                            <div class="flex shrink-0 items-center gap-2">
                                <form method="POST" action="{{ route('follow-requests.accept', $followRequest) }}">
                                    @csrf
                                    <button type="submit" class="theme-btn rounded-lg px-3 py-1.5 text-xs font-bold">Accept</button>
                                </form>
                                <form method="POST" action="{{ route('follow-requests.reject', $followRequest) }}">
                                    @csrf
                                    <button type="submit" class="rounded-lg border border-slate-700 px-3 py-1.5 text-xs font-semibold text-slate-300 hover:text-white">Delete</button>
                                </form>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </section>
        @endif

        @if($isPrivateProfile && !$isOwnProfile && $followStatus !== 'accepted')
            <section class="mx-auto flex max-w-md flex-col items-center py-16 text-center">
                <span class="flex size-14 items-center justify-center rounded-full border border-slate-700 text-slate-300">
                    <x-heroicon-o-lock-closed class="size-7" />
                </span>
                <h2 class="mt-4 text-lg font-semibold text-white">This account is private</h2>
                <p class="mt-2 text-sm text-slate-400">Follow this creator to see their photos and product picks.</p>
            </section>
        @else
            @php
                $postsUrl = $isOwnProfile
                    ? route('profile', ['tab' => 'posts'])
                    : route('users.show', ['user' => $user, 'tab' => 'posts']);
                $reelsUrl = $isOwnProfile
                    ? route('profile', ['tab' => 'reels'])
                    : route('users.show', ['user' => $user, 'tab' => 'reels']);
            @endphp

            <section aria-label="Profile media" class="mt-7">
                <nav class="flex justify-center border-t border-slate-800/80" aria-label="Profile media tabs">
                    <a href="{{ $postsUrl }}" @class([
                        '-mt-px inline-flex items-center gap-2 border-t px-3 py-3 text-[11px] font-bold uppercase tracking-[0.16em] transition',
                        'border-white text-white' => $profileTab === 'posts',
                        'border-transparent text-slate-500 hover:text-slate-200' => $profileTab !== 'posts',
                    ]) @if($profileTab === 'posts') aria-current="page" @endif>
                        <x-heroicon-o-squares-2x2 class="size-4" />
                        Posts
                        <span class="text-[10px] text-slate-500">{{ number_format($postsCount) }}</span>
                    </a>
                    <a href="{{ $reelsUrl }}" @class([
                        '-mt-px inline-flex items-center gap-2 border-t px-3 py-3 text-[11px] font-bold uppercase tracking-[0.16em] transition',
                        'border-white text-white' => $profileTab === 'reels',
                        'border-transparent text-slate-500 hover:text-slate-200' => $profileTab !== 'reels',
                    ]) @if($profileTab === 'reels') aria-current="page" @endif>
                        <x-heroicon-o-play class="size-4" />
                        Reels
                        <span class="text-[10px] text-slate-500">{{ number_format($reelsCount) }}</span>
                    </a>
                    <a href="{{ $isOwnProfile ? route('profile', ['tab' => 'followers']) : route('users.show', ['user' => $user, 'tab' => 'followers']) }}" @class([
                        '-mt-px inline-flex items-center gap-2 border-t px-3 py-3 text-[11px] font-bold uppercase tracking-[0.16em] transition',
                        'border-white text-white' => $profileTab === 'followers',
                        'border-transparent text-slate-500 hover:text-slate-200' => $profileTab !== 'followers',
                    ]) @if($profileTab === 'followers') aria-current="page" @endif>
                        Followers
                    </a>
                    <a href="{{ $isOwnProfile ? route('profile', ['tab' => 'following']) : route('users.show', ['user' => $user, 'tab' => 'following']) }}" @class([
                        '-mt-px inline-flex items-center gap-2 border-t px-3 py-3 text-[11px] font-bold uppercase tracking-[0.16em] transition',
                        'border-white text-white' => $profileTab === 'following',
                        'border-transparent text-slate-500 hover:text-slate-200' => $profileTab !== 'following',
                    ]) @if($profileTab === 'following') aria-current="page" @endif>
                        Following
                    </a>
                </nav>

                @if($profileTab === 'posts')
                    <div class="grid grid-cols-3 gap-[2px] sm:gap-1 md:gap-7">
                        @forelse ($posts as $post)
                            @php
                                $image = $post->images->first()?->image_path ?? $post->image ?? null;
                            @endphp
                            <article class="group relative aspect-square overflow-hidden bg-[#17121d]">
                                @if($image)
                                    <img src="{{ asset('storage/' . $image) }}" alt="{{ $post->content ?: 'Photo by ' . $user->name }}" loading="lazy" class="size-full object-cover transition duration-300 group-hover:scale-[1.02]">
                                @else
                                    <div class="flex size-full items-center justify-center p-3 text-center text-xs leading-5 text-slate-300 sm:p-6 sm:text-sm">
                                        {{ $post->content ?: 'A look from ' . $user->name }}
                                    </div>
                                @endif

                                @if($post->product)
                                    <a href="{{ route('product.detail', $post->product->slug ?? $post->product->id) }}" aria-label="Shop {{ $post->product->name }}" class="absolute inset-0 z-10 flex items-end bg-gradient-to-t from-black/75 via-transparent to-transparent p-2 opacity-100 transition sm:opacity-0 sm:group-hover:opacity-100 sm:focus-visible:opacity-100 sm:p-4">
                                        <span class="inline-flex max-w-full items-center gap-1.5 rounded-md bg-black/60 px-2 py-1.5 text-[10px] font-semibold text-white backdrop-blur-sm sm:text-xs">
                                            <x-heroicon-o-shopping-bag class="size-3.5 shrink-0" />
                                            <span class="truncate">{{ $post->product->name }}</span>
                                        </span>
                                    </a>
                                @endif
                            </article>
                        @empty
                            <div class="col-span-3 py-16 text-center">
                                <x-heroicon-o-camera class="mx-auto size-9 text-slate-600" />
                                <p class="mt-3 text-sm font-semibold text-white">No posts yet</p>
                                @if($isOwnProfile)
                                    <a href="{{ route('posts.create') }}" class="mt-2 inline-flex text-sm font-semibold theme-text hover:underline">Share your first photo</a>
                                @endif
                            </div>
                        @endforelse
                    </div>

                    @if(method_exists($posts, 'hasPages') && $posts->hasPages())
                        <div class="mt-8">{{ $posts->links() }}</div>
                    @endif
                @elseif($profileTab === 'reels')
                    <div class="grid grid-cols-3 gap-[2px] sm:gap-1 md:gap-7">
                        @forelse($reels as $reel)
                            @php $reelPoster = $reel->thumbnail ? asset('storage/' . $reel->thumbnail) : null; @endphp
                            <a href="{{ route('reels.index', ['reel' => $reel->id]) }}#reel-{{ $reel->id }}" aria-label="Watch reel{{ $reel->caption ? ': ' . $reel->caption : '' }}" class="group relative aspect-square overflow-hidden bg-[#17121d]">
                                @if($reelPoster)
                                    <img src="{{ $reelPoster }}" alt="{{ $reel->caption ?: 'Reel by ' . $user->name }}" loading="lazy" class="size-full object-cover transition duration-300 group-hover:scale-[1.02]">
                                @else
                                    <div class="flex size-full items-center justify-center bg-[linear-gradient(145deg,#17212b,#252038_55%,#30202b)] p-3 text-center text-xs leading-5 text-slate-200 sm:p-6 sm:text-sm">
                                        {{ $reel->caption ?: 'Reel by ' . $user->name }}
                                    </div>
                                @endif
                                <span class="absolute right-2 top-2 flex size-7 items-center justify-center rounded-full bg-black/55 text-white backdrop-blur-sm">
                                    <x-heroicon-s-play class="size-4" />
                                </span>
                                @if($reel->product)
                                    <span class="absolute inset-x-0 bottom-0 flex items-center gap-1.5 bg-gradient-to-t from-black/75 to-transparent px-2 pb-2 pt-6 text-[10px] font-semibold text-white sm:px-3 sm:pb-3 sm:text-xs">
                                        <x-heroicon-o-shopping-bag class="size-3.5 shrink-0" />
                                        <span class="truncate">{{ $reel->product->name }}</span>
                                    </span>
                                @endif
                            </a>
                        @empty
                            <div class="col-span-3 py-16 text-center">
                                <x-heroicon-o-film class="mx-auto size-9 text-slate-600" />
                                <p class="mt-3 text-sm font-semibold text-white">No reels yet</p>
                            </div>
                        @endforelse
                    </div>

                    @if(method_exists($reels, 'hasPages') && $reels->hasPages())
                        <div class="mt-8">{{ $reels->links() }}</div>
                    @endif
                @else
                    <section aria-label="{{ $profileTab === 'followers' ? 'Followers' : 'Following' }}" class="mx-auto max-w-2xl divide-y divide-slate-800/70">
                        @forelse($connections as $connection)
                            <a href="{{ route('users.show', $connection) }}" wire:key="connection-{{ $connection->id }}" class="flex items-center gap-3 py-3">
                                <span class="theme-soft-bg flex size-11 shrink-0 items-center justify-center overflow-hidden rounded-full text-sm font-bold text-white">
                                    @if($connection->profile?->profile_picture)
                                        <img src="{{ asset('storage/' . $connection->profile->profile_picture) }}" alt="" class="size-full object-cover">
                                    @else
                                        {{ strtoupper(substr($connection->name, 0, 1)) }}
                                    @endif
                                </span>
                                <span class="min-w-0 flex-1 truncate text-sm font-semibold text-white">{{ $connection->name }}</span>
                                @if($profileTab === 'following' && $connection->pivot?->status === 'pending')
                                    <span class="shrink-0 text-[10px] font-bold text-amber-300">Requested</span>
                                @endif
                                <x-heroicon-o-chevron-right class="size-4 shrink-0 text-slate-500" />
                            </a>
                        @empty
                            <p class="py-12 text-center text-sm text-slate-400">{{ $profileTab === 'followers' ? 'No followers yet.' : 'Not following anyone yet.' }}</p>
                        @endforelse
                        @if(method_exists($connections, 'hasPages') && $connections->hasPages())
                            <div class="pt-5">{{ $connections->links() }}</div>
                        @endif
                    </section>
                @endif
            </section>
        @endif
    </main>
</x-layouts.app>