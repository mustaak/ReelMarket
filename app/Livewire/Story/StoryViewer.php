<?php

namespace App\Livewire\Story;

use App\Models\Story;
use Livewire\Component;

class StoryViewer extends Component
{
    public ?Story $story = null;

    public bool $showViewer = false;

    public bool $showViewers = false;

    public array $viewers = [];

    public array $userStories = [];

    public int $currentStoryIndex = 0;

    protected $listeners = [
        'open-story' => 'openStory',
    ];

    public function openStory(int $storyId): void
    {
        $clickedStory = Story::query()
            ->whereKey($storyId)
            ->where('expires_at', '>', now())
            ->first();

        if (! $clickedStory) {
            return;
        }

        /*
         * Load all active stories of the same user.
         */
        $stories = Story::query()
            ->where('user_id', $clickedStory->user_id)
            ->where('expires_at', '>', now())
            ->orderBy('created_at')
            ->get();

        if ($stories->isEmpty()) {
            return;
        }

        /*
         * Store stories in Livewire state.
         */
        $this->userStories = $stories
            ->map(fn (Story $story) => [
                'id' => $story->id,
                'user_id' => $story->user_id,
                'media_path' => $story->media_path,
                'media_type' => $story->media_type,
                'caption' => $story->caption,
                'expires_at' => $story->expires_at?->toISOString(),
                'created_at' => $story->created_at?->toISOString(),
            ])
            ->values()
            ->toArray();

        /*
         * Start from the story that user clicked.
         */
        $clickedIndex = $stories
            ->values()
            ->search(
                fn (Story $story) => $story->id === $clickedStory->id
            );

        $this->currentStoryIndex = $clickedIndex === false
            ? 0
            : $clickedIndex;

        $this->showViewer = true;
        $this->showViewers = false;

        $this->loadCurrentStory();
    }

    public function nextStory(): void
    {
        if (! $this->showViewer) {
            return;
        }

        /*
         * No more stories.
         */
        if ($this->currentStoryIndex >= count($this->userStories) - 1) {
            $this->closeViewer();

            return;
        }

        $this->currentStoryIndex++;

        $this->showViewers = false;

        $this->loadCurrentStory();

        $this->dispatch('story-changed');
    }

    public function previousStory(): void
    {
        if (! $this->showViewer) {
            return;
        }

        /*
         * Already at first story.
         */
        if ($this->currentStoryIndex <= 0) {
            return;
        }

        $this->currentStoryIndex--;

        $this->showViewers = false;

        $this->loadCurrentStory();

        $this->dispatch('story-changed');
    }

    protected function loadCurrentStory(): void
    {
        $storyData = $this->userStories[$this->currentStoryIndex] ?? null;

        if (! $storyData) {
            $this->closeViewer();

            return;
        }

        /*
         * Fetch fresh story data from database.
         */
        $this->story = Story::query()
            ->with('user.profile')
            ->whereKey($storyData['id'])
            ->where('expires_at', '>', now())
            ->first();

        /*
         * Story expired/deleted while viewer was open.
         */
        if (! $this->story) {
            array_splice(
                $this->userStories,
                $this->currentStoryIndex,
                1
            );

            if (empty($this->userStories)) {
                $this->closeViewer();

                return;
            }

            if (
                $this->currentStoryIndex >=
                count($this->userStories)
            ) {
                $this->currentStoryIndex =
                    count($this->userStories) - 1;
            }

            $this->loadCurrentStory();

            return;
        }

        /*
         * Record story view.
         *
         * Story owner is not counted as a viewer.
         */
        if (
            auth()->check()
            && auth()->id() !== $this->story->user_id
        ) {
            $this->story->views()->firstOrCreate(
                [
                    'user_id' => auth()->id(),
                ],
                [
                    'viewed_at' => now(),
                ]
            );
        }

        $this->loadViewers();
    }

    protected function loadViewers(): void
    {
        if (! $this->story) {
            $this->viewers = [];

            return;
        }

        $this->story->loadCount('views');

        $this->viewers = $this->story
            ->views()
            ->with('user.profile')
            ->latest('viewed_at')
            ->get()
            ->map(fn ($view) => [
                'id' => $view->user->id,
                'name' => $view->user->name,
                'profile_picture' =>
                    optional($view->user->profile)->profile_picture,
                'viewed_at' =>
                    $view->viewed_at?->diffForHumans(),
            ])
            ->toArray();
    }

    public function closeViewer(): void
    {
        $this->showViewer = false;
        $this->showViewers = false;

        $this->story = null;

        $this->viewers = [];

        $this->userStories = [];

        $this->currentStoryIndex = 0;
    }

    public function render()
    {
        return view('livewire.story.story-viewer');
    }
}