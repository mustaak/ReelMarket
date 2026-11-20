<?php

namespace Tests\Feature;

use App\Models\Conversation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class MessageAttachmentControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_accepted_conversation_participant_can_view_an_image_attachment(): void
    {
        Storage::fake('local');
        $sender = User::factory()->create();
        $recipient = User::factory()->create();
        $conversation = $this->createAcceptedConversation($sender, $recipient);
        $file = UploadedFile::fake()->image('photo.jpg');
        $path = $file->store('message-attachments/'.$conversation->id, 'local');
        $message = $conversation->messages()->create([
            'sender_id' => $sender->id,
            'content' => '',
            'attachment_path' => $path,
            'attachment_name' => 'photo.jpg',
            'attachment_mime' => 'image/jpeg',
        ]);

        $this->actingAs($recipient)
            ->get(route('messages.attachments.show', $message))
            ->assertOk()
            ->assertHeader('content-type', 'image/jpeg');
    }

    public function test_pending_participants_and_non_members_cannot_retrieve_attachments(): void
    {
        Storage::fake('local');
        $sender = User::factory()->create();
        $recipient = User::factory()->create();
        $outsider = User::factory()->create();
        $conversation = $this->createAcceptedConversation($sender, $recipient, false);
        $path = 'message-attachments/'.$conversation->id.'/private-test.txt';
        Storage::disk('local')->put($path, 'private');
        $message = $conversation->messages()->create([
            'sender_id' => $sender->id,
            'content' => '',
            'attachment_path' => $path,
            'attachment_name' => 'private-test.txt',
            'attachment_mime' => 'text/plain',
        ]);

        $this->actingAs($recipient)
            ->get(route('messages.attachments.show', $message))
            ->assertNotFound();

        $this->actingAs($outsider)
            ->get(route('messages.attachments.show', $message))
            ->assertNotFound();
    }

    public function test_missing_attachment_file_returns_not_found(): void
    {
        Storage::fake('local');
        $sender = User::factory()->create();
        $recipient = User::factory()->create();
        $conversation = $this->createAcceptedConversation($sender, $recipient);
        $message = $conversation->messages()->create([
            'sender_id' => $sender->id,
            'content' => '',
            'attachment_path' => 'message-attachments/missing.txt',
            'attachment_name' => 'missing.txt',
            'attachment_mime' => 'text/plain',
        ]);

        $this->actingAs($recipient)
            ->get(route('messages.attachments.show', $message))
            ->assertNotFound();
    }

    private function createAcceptedConversation(
        User $sender,
        User $recipient,
        bool $recipientAccepted = true,
    ): Conversation {
        $userIds = [$sender->id, $recipient->id];
        sort($userIds);
        $conversation = Conversation::query()->create([
            'direct_pair_key' => implode(':', $userIds),
        ]);
        $conversation->users()->attach([
            $sender->id => ['accepted_at' => now()],
            $recipient->id => [
                'accepted_at' => $recipientAccepted ? now() : null,
            ],
        ]);

        return $conversation;
    }
}
