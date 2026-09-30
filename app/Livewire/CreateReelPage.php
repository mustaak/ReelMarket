<?php

namespace App\Livewire;

use App\Models\Product;
use App\Models\Reel;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('components.layouts.app')]
class CreateReelPage extends Component
{
    use WithFileUploads;

    public $video;

    public $thumbnail;

    public string $caption = '';

    public ?int $productId = null;

    public function publish(): mixed
    {
        $user = Auth::user();
        abort_unless($user instanceof User, 403);

        $validated = $this->validate([
            'video' => ['required', 'file', 'mimetypes:video/mp4,video/webm,video/quicktime', 'max:102400'],
            'thumbnail' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'caption' => ['nullable', 'string', 'max:2200'],
            'productId' => [
                'nullable',
                'integer',
                Rule::exists('products', 'id')->where('status', 'active')->where('visibility', 'visible'),
            ],
        ]);

        $videoPath = $this->video->store('reels', 'public');
        $thumbnailPath = $this->thumbnail?->store('reel-thumbnails', 'public');

        $reel = Reel::query()->create([
            'user_id' => $user->id,
            'product_id' => $validated['productId'] ?? null,
            'video_path' => $videoPath,
            'thumbnail' => $thumbnailPath,
            'caption' => $validated['caption'] ?? null,
            'status' => 'published',
        ]);

        return $this->redirectRoute('reels.index', ['reel' => $reel->id]);
    }

    public function render(): View
    {
        return view('livewire.create-reel-page', [
            'products' => Product::active()->orderBy('name')->get(['id', 'name']),
        ]);
    }
}
