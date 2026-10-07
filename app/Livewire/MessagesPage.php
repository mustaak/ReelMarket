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

    /*
    |--------------------------------------------------------------------------
    | New message recipient
    |--------------------------------------------------------------------------
    | IMPORTANT:
    | Iska matlab sirf ye hai ki message box kis user ke liye open hai.
    | Is stage par database me conversation create NAHI hoti.
    */
    public ?int $newRecipientId = null;

    public string $messageContent = '';

    public ?int $lastMessageId = null;

    public function mount(): void
    {
        $conversationId = request()->integer('conversation');
        $recipientId = request()->integer('user');

        /*
        |--------------------------------------------------------------------------
        | Existing conversation
        |--------------------------------------------------------------------------
        */
        if ($conversationId > 0) {
            $this->selectConversation($conversationId);

            return;
        }

        /*
        |--------------------------------------------------------------------------
        | New message
        |--------------------------------------------------------------------------
        |
        | IMPORTANT:
        | startConversation() sirf recipient set karega.
        | Conversation yahan create nahi hogi.
        |
        */
        if ($recipientId > 0) {
            $this->startConversation($recipientId);

            return;
        }

        /*
        |--------------------------------------------------------------------------
        | Open latest conversation
        |--------------------------------------------------------------------------
        */
        $firstConversation = $this->currentUser()
            ->conversations()
            ->orderByDesc('conversations.updated_at')
            ->orderByDesc('conversations.id')
            ->first();

        if ($firstConversation) {
            $this->selectConversation($firstConversation->id);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Select existing conversation
    |--------------------------------------------------------------------------
    */
    public function selectConversation(int $conversationId): void
    {
        $conversation = $this->findConversationForCurrentUser($conversationId);

        $this->activeConversationId = $conversation->id;

        /*
        | Existing conversation select ho gayi,
        | isliye new recipient mode band.
        */
        $this->newRecipientId = null;

        $this->messageContent = '';

        $latestMessageId = Message::query()
            ->where('conversation_id', $conversation->id)
            ->max('id');

        $this->lastMessageId = $latestMessageId === null
            ? null
            : (int) $latestMessageId;

        /*
        | Existing conversation open karne par unread messages read.
        */
        $this->markAsRead($conversation->id);

        $this->dispatch('conversation-scroll-bottom');
    }

    /*
    |--------------------------------------------------------------------------
    | Close conversation on mobile / show inbox
    |--------------------------------------------------------------------------
    */
    public function showInbox(): void
    {
        $this->activeConversationId = null;
        $this->newRecipientId = null;
        $this->messageContent = '';
        $this->lastMessageId = null;
    }

    /*
    |--------------------------------------------------------------------------
    | OPEN NEW MESSAGE BOX
    |--------------------------------------------------------------------------
    |
    | IMPORTANT:
    |
    | User profile se "Message" click karega.
    |
    | Yahan conversation CREATE nahi hogi.
    | Sirf recipient store hoga.
    |
    */
    public function startConversation(int $userId): void
    {
        $sender = $this->currentUser();

        $recipient = User::query()->findOrFail($userId);

        $this->authorizeMessage($sender, $recipient);

        /*
        |--------------------------------------------------------------------------
        | Agar already direct conversation hai
        |--------------------------------------------------------------------------
        */
        $conversation = $this->findDirectConversation(
            $sender,
            $recipient
        );

        if ($conversation) {
            $this->selectConversation($conversation->id);

            return;
        }

        /*
        |--------------------------------------------------------------------------
        | NEW conversation:
        |
        | Sirf recipient ID rakho.
        | DB me kuch CREATE nahi karna.
        |--------------------------------------------------------------------------
        */
        $this->activeConversationId = null;
        $this->newRecipientId = $recipient->id;
        $this->messageContent = '';
        $this->lastMessageId = null;
    }

    /*
    |--------------------------------------------------------------------------
    | SEND MESSAGE
    |--------------------------------------------------------------------------
    |
    | Conversation sirf yahan create hogi.
    |
    */
    public function sendMessage(): void
    {
        $validated = $this->validate([
            'messageContent' => [
                'required',
                'string',
                'max:2000',
            ],
        ]);

        $sender = $this->currentUser();

        /*
        |--------------------------------------------------------------------------
        | Na existing conversation hai,
        | na new recipient hai.
        |--------------------------------------------------------------------------
        */
        if (! $this->activeConversationId && ! $this->newRecipientId) {
            $this->addError(
                'messageContent',
                'Choose a conversation first.'
            );

            return;
        }

        $result = DB::transaction(function () use (
            $sender,
            $validated
        ): array {

            /*
            |--------------------------------------------------------------------------
            | CASE 1:
            | Existing conversation
            |--------------------------------------------------------------------------
            */
            if ($this->activeConversationId) {

                $conversation = $this->findConversationForCurrentUser(
                    $this->activeConversationId
                );

            } else {

                /*
                |--------------------------------------------------------------------------
                | CASE 2:
                | First message to a new user
                |--------------------------------------------------------------------------
                */

                $recipient = User::query()->findOrFail(
                    $this->newRecipientId
                );

                $this->authorizeMessage(
                    $sender,
                    $recipient
                );

                /*
                |--------------------------------------------------------------------------
                | Check again whether direct conversation already exists.
                |
                | This prevents duplicate conversations.
                |--------------------------------------------------------------------------
                */
                $conversation = $this->findDirectConversation(
                    $sender,
                    $recipient
                );

                /*
                |--------------------------------------------------------------------------
                | NEW conversation
                |--------------------------------------------------------------------------
                */
                if (! $conversation) {

                    $conversation = Conversation::query()->create([
                        'direct_pair_key' => $this->directPairKey(
                            $sender,
                            $recipient
                        ),
                    ]);

                    /*
                    |--------------------------------------------------------------------------
                    | IMPORTANT:
                    |
                    | Sender:
                    | accepted_at = NOW
                    |
                    | Recipient:
                    | accepted_at = NULL
                    |
                    | Therefore recipient ko REQUEST milegi.
                    |--------------------------------------------------------------------------
                    */
                    $conversation->users()->attach([
                        $sender->id => [
                            'accepted_at' => now(),
                        ],

                        $recipient->id => [
                            'accepted_at' => null,
                        ],
                    ]);

                } else {

                    /*
                    |--------------------------------------------------------------------------
                    | Existing conversation mil gayi.
                    |
                    | Ensure sender is attached and accepted.
                    |--------------------------------------------------------------------------
                    */
                    $senderPivotExists = $conversation
                        ->users()
                        ->whereKey($sender->id)
                        ->exists();

                    if (! $senderPivotExists) {
                        $conversation->users()->attach(
                            $sender->id,
                            [
                                'accepted_at' => now(),
                            ]
                        );
                    } else {
                        $conversation->users()->updateExistingPivot(
                            $sender->id,
                            [
                                'accepted_at' => now(),
                            ]
                        );
                    }
                }
            }

            /*
            |--------------------------------------------------------------------------
            | CREATE MESSAGE
            |--------------------------------------------------------------------------
            */
            $message = $conversation->messages()->create([
                'sender_id' => $sender->id,
                'content' => trim($validated['messageContent']),
            ]);

            /*
            |--------------------------------------------------------------------------
            | Conversation updated_at update
            |--------------------------------------------------------------------------
            */
            $conversation->touch();

            return [
                'conversation_id' => $conversation->id,
                'message' => $message,
            ];
        });

        /*
        |--------------------------------------------------------------------------
        | Switch UI from "new message" to actual conversation
        |--------------------------------------------------------------------------
        */
        $this->activeConversationId = $result['conversation_id'];

        $this->newRecipientId = null;

        $this->messageContent = '';

        $this->lastMessageId = $result['message']->id;

        /*
        |--------------------------------------------------------------------------
        | Sender ke liye message read hai.
        |--------------------------------------------------------------------------
        */
        $this->markAsRead(
            $this->activeConversationId
        );

        /*
        |--------------------------------------------------------------------------
        | Broadcast event
        |--------------------------------------------------------------------------
        */
        MessageSent::dispatch(
            $result['message']
        );

        $this->dispatch(
            'conversation-scroll-bottom'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Refresh messages
    |--------------------------------------------------------------------------
    */
    public function refreshMessages(): void
    {
        if (! $this->activeConversationId) {
            return;
        }

        $conversation = $this->findConversationForCurrentUser(
            $this->activeConversationId
        );

        $this->markAsRead(
            $conversation->id
        );

        $latestMessageId = Message::query()
            ->where('conversation_id', $conversation->id)
            ->max('id');

        $latestMessageId = $latestMessageId === null
            ? null
            : (int) $latestMessageId;

        if ($latestMessageId !== $this->lastMessageId) {
            $this->lastMessageId = $latestMessageId;

            $this->dispatch(
                'conversation-scroll-bottom'
            );
        }
    }

    /*
    |--------------------------------------------------------------------------
    | REQUESTS
    |--------------------------------------------------------------------------
    |
    | Current user ke pivot par:
    |
    | accepted_at = NULL
    |
    | iska matlab incoming request.
    |
    */
    public function getRequestsProperty()
    {
        return Conversation::query()
            ->whereHas('users', function (Builder $query) {
                $query
                    ->whereKey($this->currentUser()->id)
                    ->whereNull('conversation_user.accepted_at');
            })
            ->with([
                'users.profile',
                'latestMessage.sender',
            ])
            ->orderByDesc('conversations.updated_at')
            ->orderByDesc('conversations.id')
            ->get();
    }

    /*
    |--------------------------------------------------------------------------
    | ACCEPT REQUEST
    |--------------------------------------------------------------------------
    */
    public function acceptRequest(int $conversationId): void
    {
        $conversation = Conversation::query()
            ->whereKey($conversationId)
            ->whereHas('users', function (Builder $query) {
                $query
                    ->whereKey($this->currentUser()->id)
                    ->whereNull('conversation_user.accepted_at');
            })
            ->firstOrFail();

        /*
        |--------------------------------------------------------------------------
        | Current user's request accepted.
        |--------------------------------------------------------------------------
        */
        $conversation->users()->updateExistingPivot(
            $this->currentUser()->id,
            [
                'accepted_at' => now(),
            ]
        );

        $conversation->touch();

        /*
        |--------------------------------------------------------------------------
        | Open accepted conversation.
        |--------------------------------------------------------------------------
        */
        $this->selectConversation(
            $conversation->id
        );
    }

    /*
    |--------------------------------------------------------------------------
    | DECLINE REQUEST
    |--------------------------------------------------------------------------
    */
    public function declineRequest(int $conversationId): void
    {
        $conversation = Conversation::query()
            ->whereKey($conversationId)
            ->whereHas('users', function (Builder $query) {
                $query
                    ->whereKey($this->currentUser()->id)
                    ->whereNull('conversation_user.accepted_at');
            })
            ->firstOrFail();

        /*
        |--------------------------------------------------------------------------
        | Receiver conversation se remove.
        |--------------------------------------------------------------------------
        */
        $conversation->users()->detach(
            $this->currentUser()->id
        );

        /*
        |--------------------------------------------------------------------------
        | Agar koi user remaining nahi hai to conversation delete.
        |--------------------------------------------------------------------------
        */
        if (! $conversation->users()->exists()) {
            $conversation->delete();
        }

        $this->activeConversationId = null;
        $this->newRecipientId = null;
        $this->messageContent = '';
        $this->lastMessageId = null;
    }

    /*
    |--------------------------------------------------------------------------
    | Render
    |--------------------------------------------------------------------------
    */
    public function render()
    {
        $viewer = $this->currentUser();

        /*
        |--------------------------------------------------------------------------
        | ACCEPTED CONVERSATIONS ONLY
        |--------------------------------------------------------------------------
        |
        | IMPORTANT:
        |
        | Requests yahan nahi aayengi.
        |
        | User tabhi Messages me dikhega jab current user's
        | accepted_at NOT NULL ho.
        |
        |--------------------------------------------------------------------------
        */
        $conversations = $viewer
            ->conversations()
            ->whereNotNull('conversation_user.accepted_at')
            ->with([
                'users.profile',
                'latestMessage.sender',
            ])
            ->withCount([
                'messages as unread_messages_count' =>
                    fn (Builder $query) => $query
                        ->where('sender_id', '!=', $viewer->id)
                        ->whereNull('read_at'),
            ])
            ->orderByDesc('conversations.updated_at')
            ->orderByDesc('conversations.id')
            ->get()
            ->unique(
                fn (Conversation $conversation): string =>
                    $conversation->direct_pair_key
                        ? 'direct:' . $conversation->direct_pair_key
                        : 'group:' . $conversation->id
            )
            ->values();

        $activeConversation = null;

        $recipient = null;

        $messages = collect();

        /*
        |--------------------------------------------------------------------------
        | Existing conversation
        |--------------------------------------------------------------------------
        */
        if ($this->activeConversationId) {

            $activeConversation = $this->findConversationForCurrentUser(
                $this->activeConversationId
            )->load('users.profile');

            $messages = Message::query()
                ->where(
                    'conversation_id',
                    $activeConversation->id
                )
                ->with('sender.profile')
                ->orderByDesc('id')
                ->limit(100)
                ->get()
                ->reverse()
                ->values();

        }

        /*
        |--------------------------------------------------------------------------
        | NEW MESSAGE BOX
        |--------------------------------------------------------------------------
        |
        | Yahan conversation nahi hai.
        |
        | Sirf recipient hai.
        |--------------------------------------------------------------------------
        */
        elseif ($this->newRecipientId) {

            $recipient = User::query()
                ->with('profile')
                ->findOrFail($this->newRecipientId);

            $this->authorizeMessage(
                $viewer,
                $recipient
            );
        }

        return view(
            'livewire.messages-page',
            [
                'conversations' => $conversations,
                'activeConversation' => $activeConversation,
                'recipient' => $recipient,
                'messages' => $messages,
                'requests' => $this->requests,
            ]
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Current User
    |--------------------------------------------------------------------------
    */
    private function currentUser(): User
    {
        $user = Auth::user();

        abort_unless(
            $user instanceof User,
            403
        );

        return $user;
    }

    /*
    |--------------------------------------------------------------------------
    | Find conversation belonging to current user
    |--------------------------------------------------------------------------
    */
    private function findConversationForCurrentUser(
        int $conversationId
    ): Conversation {
        return Conversation::query()
            ->whereKey($conversationId)
            ->whereHas(
                'users',
                fn (Builder $query) =>
                    $query->whereKey(
                        $this->currentUser()->id
                    )
            )
            ->firstOrFail();
    }

    /*
    |--------------------------------------------------------------------------
    | Find direct conversation
    |--------------------------------------------------------------------------
    */
    private function findDirectConversation(
        User $sender,
        User $recipient
    ): ?Conversation {
        return Conversation::query()
            ->where(
                'direct_pair_key',
                $this->directPairKey(
                    $sender,
                    $recipient
                )
            )
            ->first();
    }

    /*
    |--------------------------------------------------------------------------
    | Unique direct pair key
    |--------------------------------------------------------------------------
    */
    private function directPairKey(
        User $sender,
        User $recipient
    ): string {
        $userIds = [
            $sender->id,
            $recipient->id,
        ];

        sort($userIds);

        return $userIds[0] . ':' . $userIds[1];
    }

    /*
    |--------------------------------------------------------------------------
    | Message authorization
    |--------------------------------------------------------------------------
    */
    private function authorizeMessage(
        User $sender,
        User $recipient
    ): void {
        /*
        | Apne aap ko message nahi.
        */
        abort_if(
            $sender->is($recipient),
            404
        );

        /*
        | Public profile OR following.
        */
        $isPublic = (bool) (
            $recipient->profile?->is_public ?? true
        );

        abort_unless(
            $isPublic || $sender->isFollowing($recipient),
            403
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Mark messages as read
    |--------------------------------------------------------------------------
    */
    private function markAsRead(
        int $conversationId
    ): void {
        Message::query()
            ->where(
                'conversation_id',
                $conversationId
            )
            ->where(
                'sender_id',
                '!=',
                $this->currentUser()->id
            )
            ->whereNull('read_at')
            ->update([
                'read_at' => now(),
            ]);
    }
}