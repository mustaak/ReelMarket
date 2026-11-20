<?php

namespace Tests\Feature;

use App\Events\MessageSent;
use App\Livewire\MessagesPage;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class MessagesPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_photo_can_be_sent_without_message_text_and_is_stored_privately(): void
    {
        Storage::fake('local');
        $sender = User::factory()->create();
        $recipient = User::factory()->create();
        $conversation = $this->createAcceptedConversation($sender, $recipient);
        Event::fake([MessageSent::class]);

        Livewire::actingAs($sender);
        Livewire::test(MessagesPage::class)
            ->set('activeConversationId', $conversation->id)
            ->set('attachment', UploadedFile::fake()->image('shared-photo.jpg'))
            ->call('sendMessage')
            ->assertHasNoErrors();

        $message = Message::query()->sole();

        $this->assertSame('', $message->content);
        $this->assertSame('shared-photo.jpg', $message->attachment_name);
        Storage::disk('local')->assertExists($message->attachment_path);
        Event::assertDispatched(MessageSent::class);
    }

    public function test_gif_can_be_sent_without_message_text(): void
    {
        Storage::fake('local');
        $sender = User::factory()->create();
        $recipient = User::factory()->create();
        $conversation = $this->createAcceptedConversation($sender, $recipient);
        $gifContents = base64_decode('R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs=', true);
        Event::fake([MessageSent::class]);

        Livewire::actingAs($sender);
        Livewire::test(MessagesPage::class)
            ->set('activeConversationId', $conversation->id)
            ->set('attachment', UploadedFile::fake()->createWithContent('reaction.gif', $gifContents))
            ->call('sendMessage')
            ->assertHasNoErrors();

        $message = Message::query()->sole();

        $this->assertSame('image/gif', $message->attachment_mime);
        Storage::disk('local')->assertExists($message->attachment_path);
        Event::assertDispatched(MessageSent::class);
    }

    public function test_giphy_gif_can_be_selected_and_sent_without_message_text(): void
    {
        $sender = User::factory()->create();
        $recipient = User::factory()->create();
        $conversation = $this->createAcceptedConversation($sender, $recipient);
        $gifUrl = 'https://media3.giphy.com/media/example/giphy.gif';
        Event::fake([MessageSent::class]);

        Livewire::actingAs($sender);
        Livewire::test(MessagesPage::class)
            ->set('activeConversationId', $conversation->id)
            ->call('selectGif', $gifUrl)
            ->assertSet('gifUrl', $gifUrl)
            ->call('sendMessage')
            ->assertHasNoErrors();

        $message = Message::query()->sole();

        $this->assertSame('', $message->content);
        $this->assertSame($gifUrl, $message->gif_url);
        Event::assertDispatched(MessageSent::class);
    }

    public function test_message_rejects_gif_urls_outside_giphys_media_hosts(): void
    {
        $sender = User::factory()->create();
        $recipient = User::factory()->create();
        $conversation = $this->createAcceptedConversation($sender, $recipient);
        $untrustedGifUrl = 'https://media.giphy.com.evil.example/reaction.gif';

        Livewire::actingAs($sender);
        Livewire::test(MessagesPage::class)
            ->set('activeConversationId', $conversation->id)
            ->call('selectGif', $untrustedGifUrl)
            ->assertHasErrors(['gifUrl'])
            ->call('selectGif', 'https://attacker@media3.giphy.com/media/example/giphy.gif')
            ->assertHasErrors(['gifUrl'])
            ->set('gifUrl', $untrustedGifUrl)
            ->call('sendMessage')
            ->assertHasErrors(['gifUrl']);

        $this->assertDatabaseCount('messages', 0);
    }

    public function test_message_attachment_rejects_unsupported_file_types(): void
    {
        Storage::fake('local');
        $sender = User::factory()->create();
        $recipient = User::factory()->create();
        $conversation = $this->createAcceptedConversation($sender, $recipient);
        Event::fake([MessageSent::class]);

        Livewire::actingAs($sender);
        Livewire::test(MessagesPage::class)
            ->set('activeConversationId', $conversation->id)
            ->set('attachment', UploadedFile::fake()->createWithContent('script.svg', '<svg></svg>'))
            ->call('sendMessage')
            ->assertHasErrors(['attachment' => 'mimes']);

        $this->assertDatabaseCount('messages', 0);
        Event::assertNotDispatched(MessageSent::class);
    }

    public function test_empty_message_without_an_attachment_is_rejected(): void
    {
        $sender = User::factory()->create();
        $recipient = User::factory()->create();
        $conversation = $this->createAcceptedConversation($sender, $recipient);

        Livewire::actingAs($sender);
        Livewire::test(MessagesPage::class)
            ->set('activeConversationId', $conversation->id)
            ->set('messageContent', '')
            ->call('sendMessage')
            ->assertHasErrors(['messageContent' => 'required_without_all']);

        $this->assertDatabaseCount('messages', 0);
    }

    public function test_chat_screen_does_not_render_call_controls(): void
    {
        $sender = User::factory()->create();
        $recipient = User::factory()->create();
        $conversation = $this->createAcceptedConversation($sender, $recipient);

        Livewire::actingAs($sender);
        Livewire::test(MessagesPage::class)
            ->set('activeConversationId', $conversation->id)
            ->assertDontSee('Start voice call')
            ->assertDontSee('Start video call')
            ->assertDontSee('Call connected');
    }

    public function test_message_composer_uses_emoji_library_and_gif_search(): void
    {
        $sender = User::factory()->create();
        $recipient = User::factory()->create();
        $conversation = $this->createAcceptedConversation($sender, $recipient);
        Livewire::actingAs($sender);

        Livewire::test(MessagesPage::class)
            ->set('activeConversationId', $conversation->id)
            ->assertSee('<emoji-picker', false)
            ->assertSee('Search GIFs')
            ->assertSee('Powered by GIPHY');
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
