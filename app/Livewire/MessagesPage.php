<?php

namespace App\Livewire;

use App\Events\MessageSent;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class MessagesPage extends Component
{
    public ?int $activeConversationId = null;

    public ?int $newRecipientId = null;

    public string $messageContent = '';

    public ?int $lastMessageId = null;

    public function mount(): void
    {
        $conversationId = request()->integer('conversation');
        $recipientId = request()->integer('user');

        if ($conversationId > 0) {
            $this->selectConversation($conversationId);

            return;
        }

        if ($recipientId > 0) {
            $this->startConversation($recipientId);

            return;
        }

        $firstConversation = $this->currentUser()->conversations()
            ->orderByDesc('conversations.updated_at')
            ->orderByDesc('conversations.id')
            ->first();

        if ($firstConversation) {
            $this->selectConversation($firstConversation->id);
        }
    }

    public function selectConversation(int $conversationId): void
    {
        $conversation = $this->findConversationForCurrentUser($conversationId);

        // dd($conversation);

        $this->activeConversationId = $conversation->id;
        $this->newRecipientId = null;
        $this->messageContent = '';
        $latestMessageId = Message::query()
            ->where('conversation_id', $conversation->id)
            ->max('id');
        $this->lastMessageId = $latestMessageId === null ? null : (int) $latestMessageId;
        $this->markAsRead($conversation->id);
        $this->dispatch('conversation-scroll-bottom');
    }

    public function showInbox(): void
    {
        $this->activeConversationId = null;
        $this->newRecipientId = null;
        $this->messageContent = '';
        $this->lastMessageId = null;
    }

    public function startConversation(int $userId): void
    {
        $sender = $this->currentUser();
        $recipient = User::query()->findOrFail($userId);
        $this->authorizeMessage($sender, $recipient);

        $conversation = $this->findDirectConversation($sender, $recipient);
        if ($conversation) {
            $this->selectConversation($conversation->id);

            return;
        }
        $this->activeConversationId = null;
        $this->newRecipientId = $recipient->id;
        $this->messageContent = '';
        $this->lastMessageId = null;
    }

    public function sendMessage(): void
    {

        $validated = $this->validate([
            'messageContent' => ['required', 'string', 'max:2000'],
        ]);
        $sender = $this->currentUser();

        if (! $this->activeConversationId && ! $this->newRecipientId) {
            $this->addError('messageContent', 'Choose a conversation first.');

            return;
        }

        $result = DB::transaction(function () use ($sender, $validated): array {
            if ($this->activeConversationId) {
                $conversation = $this->findConversationForCurrentUser($this->activeConversationId);
            } else {
                $recipient = User::query()->findOrFail($this->newRecipientId);
                $this->authorizeMessage($sender, $recipient);
                $conversation = Conversation::query()->firstOrCreate([
                    'direct_pair_key' => $this->directPairKey($sender, $recipient),
                ]);
                $conversation->users()->syncWithoutDetaching([$sender->id, $recipient->id]);
            }

            $message = $conversation->messages()->create([
                'sender_id' => $sender->id,
                'content' => trim($validated['messageContent']),
            ]);
            $conversation->touch();

            return [
                'conversation_id' => $conversation->id,
                'message' => $message,
            ];
        });

        $this->activeConversationId = $result['conversation_id'];
        $this->newRecipientId = null;
        $this->messageContent = '';
        $this->lastMessageId = $result['message']->id;
        $this->markAsRead($this->activeConversationId);

        MessageSent::dispatch($result['message']);
        $this->dispatch('conversation-scroll-bottom');
    }

    public function refreshMessages(): void
    {
        if (! $this->activeConversationId) {
            return;
        }

        $conversation = $this->findConversationForCurrentUser($this->activeConversationId);
        $this->markAsRead($conversation->id);

        $latestMessageId = Message::query()
            ->where('conversation_id', $conversation->id)
            ->max('id');
        $latestMessageId = $latestMessageId === null ? null : (int) $latestMessageId;

        if ($latestMessageId !== $this->lastMessageId) {
            $this->lastMessageId = $latestMessageId;
            $this->dispatch('conversation-scroll-bottom');
        }
    }

    public function render()
    {
        $viewer = $this->currentUser();
        $conversations = $viewer->conversations()
            ->with(['users.profile', 'latestMessage.sender'])
            ->withCount([
                'messages as unread_messages_count' => fn (Builder $query) => $query
                    ->where('sender_id', '!=', $viewer->id)
                    ->whereNull('read_at'),
            ])
            ->orderByDesc('conversations.updated_at')
            ->orderByDesc('conversations.id')
            ->get()
            ->unique(fn (Conversation $conversation): string => $conversation->direct_pair_key
                ? 'direct:'.$conversation->direct_pair_key
                : 'group:'.$conversation->id)
            ->values();

        $activeConversation = null;
        $recipient = null;
        $messages = collect();

        if ($this->activeConversationId) {
            $activeConversation = $this->findConversationForCurrentUser($this->activeConversationId)
                ->load('users.profile');
            $messages = Message::query()
                ->where('conversation_id', $activeConversation->id)
                ->with('sender.profile')
                ->orderByDesc('id')
                ->limit(100)
                ->get()
                ->reverse()
                ->values();
        } elseif ($this->newRecipientId) {
            $recipient = User::query()->with('profile')->findOrFail($this->newRecipientId);
            $this->authorizeMessage($viewer, $recipient);
        }

        return view('livewire.messages-page', [
            'conversations' => $conversations,
            'activeConversation' => $activeConversation,
            'recipient' => $recipient,
            'messages' => $messages,
        ]);
    }

    private function currentUser(): User
    {
        $user = Auth::user();
        abort_unless($user instanceof User, 403);

        return $user;
    }

    private function findConversationForCurrentUser(int $conversationId): Conversation
    {
        return Conversation::query()
            ->whereKey($conversationId)
            ->whereHas('users', fn (Builder $query) => $query->whereKey($this->currentUser()->id))
            ->firstOrFail();
    }

    private function findDirectConversation(User $sender, User $recipient): ?Conversation
    {
        return Conversation::query()
            ->where('direct_pair_key', $this->directPairKey($sender, $recipient))
            ->first();
    }

    private function directPairKey(User $sender, User $recipient): string
    {
        $userIds = [$sender->id, $recipient->id];
        sort($userIds);

        return $userIds[0].':'.$userIds[1];
    }

    private function authorizeMessage(User $sender, User $recipient): void
    {
        abort_if($sender->is($recipient), 404);

        $isPublic = (bool) ($recipient->profile?->is_public ?? true);
        abort_unless($isPublic || $sender->isFollowing($recipient), 403);
    }

    private function markAsRead(int $conversationId): void
    {
        Message::query()
            ->where('conversation_id', $conversationId)
            ->where('sender_id', '!=', $this->currentUser()->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
    }
}
