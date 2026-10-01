<?php

namespace App\Livewire;

use App\Models\Post;
use App\Models\Reel;
use App\Models\Report;
use App\Models\User;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Locked;
use Livewire\Attributes\On;
use Livewire\Component;

class ReportContent extends Component
{
    private const REASONS = [
        'spam' => 'Spam',
        'harassment' => 'Harassment',
        'inappropriate' => 'Inappropriate content',
        'false_information' => 'False information',
        'other' => 'Other',
    ];

    #[Locked]
    public ?string $targetType = null;

    #[Locked]
    public ?int $targetId = null;

    public bool $open = false;

    public string $reason = '';

    public string $description = '';

    #[On('open-report')]
    public function openFor(string $type, int $id): void
    {
        $user = auth()->user();

        if (! $user instanceof User) {
            $this->dispatch('notify', type: 'info', message: 'Please log in to report content.');

            return;
        }

        $target = $this->findVisibleTarget($type, $id, $user);
        abort_if($target->user_id === $user->id, 404);

        $this->targetType = $type;
        $this->targetId = $id;
        $this->reason = '';
        $this->description = '';
        $this->resetValidation();
        $this->open = true;
    }

    public function submit(): void
    {
        $user = auth()->user();
        abort_unless($user instanceof User, 403);

        if (! $this->targetType || ! $this->targetId) {
            abort(404);
        }

        $target = $this->findVisibleTarget($this->targetType, $this->targetId, $user);
        abort_if($target->user_id === $user->id, 404);

        $validated = $this->validate([
            'reason' => ['required', Rule::in(array_keys(self::REASONS))],
            'description' => ['nullable', 'string', 'max:1000'],
        ]);

        Report::query()->create([
            'reportable_type' => $target->getMorphClass(),
            'reportable_id' => $target->getKey(),
            'reported_by' => $user->id,
            'reason' => $validated['reason'],
            'description' => $validated['description'] ?: null,
            'status' => 'pending',
        ]);

        $this->open = false;
        $this->targetType = null;
        $this->targetId = null;
        $this->reason = '';
        $this->description = '';

        $this->dispatch('notify', type: 'success', message: 'Report submitted.');
    }

    public function close(): void
    {
        $this->open = false;
        $this->reason = '';
        $this->description = '';
        $this->resetValidation();
    }

    public function render()
    {
        return view('livewire.report-content', ['reasons' => self::REASONS]);
    }

    private function findVisibleTarget(string $type, int $id, User $viewer): Post|Reel
    {
        return match ($type) {
            'post' => Post::visibleTo($viewer)->findOrFail($id),
            'reel' => Reel::visibleTo($viewer)->findOrFail($id),
            default => abort(404),
        };
    }
}
