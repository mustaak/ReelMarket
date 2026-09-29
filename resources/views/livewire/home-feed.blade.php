<div class="space-y-6">

    <!-- 🌟 STORIES SECTION (Users & Profiles) -->
    @if(isset($storyUsers) && $storyUsers->isNotEmpty())
        <div class="theme-card border border-slate-800/80 rounded-2xl p-4 shadow-xl">
            <div class="flex items-center gap-4 overflow-x-auto pb-1 no-scrollbar">
                @foreach($storyUsers as $sUser)
                    <div wire:key="story-user-{{ $sUser->id }}" class="flex flex-col items-center gap-1.5 shrink-0 cursor-pointer group">
                        <div class="size-14 rounded-full p-0.5 theme-btn">
                            <div class="w-full h-full rounded-full theme-card p-0.5 overflow-hidden">
                                @if(optional($sUser->profile)->avatar)
                                    <img src="{{ asset('storage/' . $sUser->profile->avatar) }}" alt="{{ $sUser->name }}" class="w-full h-full rounded-full object-cover" />
                                @else
                                    <div class="w-full h-full rounded-full theme-btn flex items-center justify-center font-bold text-xs uppercase">
                                        {{ Str::substr($sUser->name, 0, 1) }}
                                    </div>
                                @endif
                            </div>
                        </div>
                        <span class="text-[11px] text-slate-300 font-medium line-clamp-1">
                            {{ Str::before($sUser->name, ' ') }}
                        </span>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- 🌟 MAIN FEED POSTS -->
    @forelse($posts as $post)
        @php
            $author = $post->user;
            $authorProfile = $author?->profile;
            $isFollowing = auth()->check() && auth()->user()->following->contains('id', $post->user_id);
            $isLiked = auth()->check() && method_exists($post, 'likes') && $post->likes->contains('user_id', auth()->id());
        @endphp

        <div wire:key="post-card-{{ $post->id }}" class="theme-card border border-slate-800/80 rounded-2xl overflow-hidden shadow-xl">
            
            <!-- Post Header -->
            <div class="p-4 flex items-center justify-between border-b border-slate-800/50">
                <div class="flex items-center gap-3">
                    <div class="size-10 rounded-full theme-btn flex items-center justify-center font-bold text-sm uppercase overflow-hidden">
                        @if($authorProfile?->avatar)
                            <img src="{{ asset('storage/' . $authorProfile->avatar) }}" alt="{{ $author?->name }}" class="w-full h-full object-cover" />
                        @else
                            {{ Str::substr($author?->name ?? 'U', 0, 2) }}
                        @endif
                    </div>
                    <div>
                        <h4 class="font-bold text-sm text-white flex items-center gap-1">
                            {{ $authorProfile?->username ?? $author?->name }}
                            @if($authorProfile?->is_verified)
                                <x-heroicon-s-check-circle class="size-4 theme-text" />
                            @endif
                        </h4>
                        <p class="text-[11px] text-slate-400">
                            {{ $post->created_at->diffForHumans() }}
                        </p>
                    </div>
                </div>

                <!-- Dynamic Follow/Unfollow Button -->
                @if(auth()->check() && auth()->id() !== $post->user_id)
                    <button wire:click="toggleFollow({{ $post->user_id }})" 
                            type="button" 
                            class="cursor-pointer theme-soft-bg theme-text border theme-border px-3 py-1 rounded-full text-xs font-semibold hover:opacity-80 transition">
                        {{ $isFollowing ? 'Following' : 'Follow' }}
                    </button>
                @endif
            </div>

            <!-- Media Section -->
            <div class="w-full aspect-square bg-slate-900 flex items-center justify-center relative overflow-hidden">
                @if($post->image)
                    <img src="{{ asset('storage/' . $post->image) }}" alt="Post Content" class="w-full h-full object-cover" />
                @else
                    <span class="text-slate-500 font-bold text-sm">Media Content Placeholder</span>
                @endif
            </div>

            <!-- Post Footer & Actions -->
            <div class="p-4 space-y-4">
                
                <!-- Like Button -->
                <div class="flex items-center justify-between text-slate-200">
                    <div class="flex items-center gap-4">
                        <button wire:click="toggleLike({{ $post->id }})" 
                                type="button" 
                                class="cursor-pointer flex items-center gap-1.5 hover:theme-text transition">
                            @if($isLiked)
                                <x-heroicon-s-heart class="size-6 text-rose-500" />
                            @else
                                <x-heroicon-o-heart class="size-6" />
                            @endif
                            <span class="text-xs font-bold">{{ number_format($post->likes_count ?? ($post->relationLoaded('likes') ? $post->likes->count() : 0)) }}</span>
                        </button>
                    </div>
                </div>

                <!-- Caption -->
                @if($post->content)
                    <p class="text-xs text-slate-300">
                        <span class="font-bold text-white mr-1">{{ $authorProfile?->username ?? $author?->name }}</span> 
                        {{ $post->content }}
                    </p>
                @endif

                <!-- SHOPPED PRODUCT ITEM -->
                @if($post->product)
                    <div class="theme-inner border border-slate-800 p-3 rounded-xl flex items-center justify-between gap-3">
                        <div class="flex items-center gap-3">
                            <div class="size-12 rounded-lg theme-soft-bg shrink-0 flex items-center justify-center font-bold theme-text overflow-hidden">
                                @if($post->product->image)
                                    <img src="{{ asset('storage/' . $post->product->image) }}" alt="{{ $post->product->name }}" class="w-full h-full object-cover" />
                                @else
                                    {{ Str::substr($post->product->name, 0, 2) }}
                                @endif
                            </div>
                            <div>
                                <h5 class="text-xs font-bold text-white line-clamp-1">{{ $post->product->name }}</h5>
                                <p class="text-xs font-semibold theme-text">₹{{ number_format($post->product->price) }}</p>
                            </div>
                        </div>
                        <a href="{{ route('product.detail', $post->product->slug ?? $post->product->id) }}" class="theme-btn px-4 py-2 rounded-xl text-xs font-bold cursor-pointer hover:scale-105 transition">
                            Shop Now
                        </a>
                    </div>
                @endif

            </div>
        </div>
    @empty
        <div class="theme-card border border-slate-800/80 rounded-2xl p-8 text-center space-y-3">
            <p class="text-2xl">📸</p>
            <h3 class="text-sm font-bold text-white">No Posts Available</h3>
            <p class="text-xs text-slate-400">Start following users or check back later.</p>
        </div>
    @endforelse

    <!-- Pagination Links -->
    @if($posts->hasPages())
        <div class="pt-4">
            {{ $posts->links() }}
        </div>
    @endif

</div>