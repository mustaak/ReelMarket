<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Post | YourBrand</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-[#0b0813] text-slate-100">
    <div class="mx-auto max-w-2xl px-4 py-8 sm:px-6 lg:px-8">
        <div class="rounded-3xl border border-white/10 bg-[#111827] p-5 shadow-2xl shadow-black/30 sm:p-8">
            <div class="mb-6 flex items-center justify-between gap-3">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.24em] text-amber-400">Create post</p>
                    <h1 class="mt-2 text-2xl font-black text-white">Share your moment</h1>
                </div>
                <a href="{{ route('profile') }}" class="rounded-full border border-white/10 px-3 py-1.5 text-sm font-medium text-slate-300 transition hover:border-white/20 hover:text-white">
                    Back to profile
                </a>
            </div>

            <form action="{{ route('posts.store') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
                @csrf

                <div>
                    <label for="content" class="mb-2 block text-sm font-medium text-slate-200">Caption</label>
                    <textarea
                        id="content"
                        name="content"
                        rows="6"
                        placeholder="What are you sharing today?"
                        class="w-full rounded-2xl border border-white/10 bg-[#0b1220] px-4 py-3 text-sm text-white placeholder:text-slate-400 focus:border-amber-400 focus:outline-none focus:ring-2 focus:ring-amber-400/30"
                    >{{ old('content') }}</textarea>
                    @error('content')
                        <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="image" class="mb-2 block text-sm font-medium text-slate-200">Photo</label>
                    <div class="rounded-2xl border border-dashed border-white/10 bg-[#0b1220] p-4">
                        <input
                            id="image"
                            type="file"
                            name="image"
                            accept="image/*"
                            class="block w-full cursor-pointer text-sm text-slate-300 file:mr-4 file:rounded-full file:border-0 file:bg-amber-400 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-black file:transition hover:file:brightness-110"
                        >
                    </div>
                    @error('image')
                        <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center justify-end gap-3 pt-2">
                    <a href="{{ route('profile') }}" class="rounded-xl border border-white/10 px-4 py-2.5 text-sm font-medium text-slate-300 transition hover:border-white/20 hover:text-white">
                        Cancel
                    </a>
                    <button type="submit" class="rounded-xl bg-[#f59e0b] px-4 py-2.5 text-sm font-bold text-black transition hover:brightness-110">
                        Publish post
                    </button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
