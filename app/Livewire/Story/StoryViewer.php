<?php

namespace App\Livewire\Story;

use App\Models\Story;
use Livewire\Component;

class StoryViewer extends Component
{
    public ?Story $story = null;

    public bool $showViewer = false;

    protected $listeners = [
        'open-story' => 'openStory',
    ];

    public array $viewers = [];
    public bool $showViewers = false;

    public function openStory(int $storyId): void
    {
        $this->story = Story::with('user.profile')
            ->whereKey($storyId)
            ->where('expires_at', '>', now())
            ->first();

        if (! $this->story) {
            return;
        }

        $this->showViewer = true;

        if (auth()->check() && auth()->id() !== $this->story->user_id) {
            $this->story->views()->firstOrCreate(
                [
                    'user_id' => auth()->id(),
                ],
                [
                    'viewed_at' => now(),
                ]
            );
        }

        $this->story->loadCount('views');

        $this->viewers = $this->story->views()
        ->with('user.profile')
        ->latest('viewed_at')
        ->get()
        ->map(fn ($view) => [
            'id' => $view->user->id,
            'name' => $view->user->name,
            'profile_picture' => optional($view->user->profile)->profile_picture,
            'viewed_at' => $view->viewed_at?->diffForHumans(),
        ])
        ->toArray();
    }

    public function closeViewer(): void
    {
        $this->showViewer = false;
        $this->showViewers = false;
        $this->story = null;
        $this->viewers = [];
    }

    public function render()
    {
        return view('livewire.story.story-viewer');
    }
}