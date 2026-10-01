<?php

namespace App\Livewire\Story;

use App\Models\Story;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithFileUploads;

class CreateStory extends Component
{
    use WithFileUploads;

    public bool $showModal = false;

    public $media;

    public string $caption = '';

    public function createStory(): void
    {
        $this->validate([
            'media' => [
                'required',
                'file',
                'mimes:jpg,jpeg,png,webp,mp4,mov,webm',
                'max:51200',
            ],
            'caption' => [
                'nullable',
                'string',
                'max:500',
            ],
        ]);

        $mediaType = str_starts_with($this->media->getMimeType(), 'image/')
            ? 'image'
            : 'video';

        $path = $this->media->store('stories', 'public');

        Story::create([
            'user_id' => Auth::id(),
            'media_path' => $path,
            'media_type' => $mediaType,
            'caption' => $this->caption ?: null,
            'expires_at' => now()->addHours(24),
        ]);

        $this->reset('media', 'caption');

        $this->showModal = false;

        $this->dispatch('story-created');
    }

    public function render()
    {
        return view('livewire.story.create-story');
    }
}