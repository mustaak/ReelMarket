<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CreatePostTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_create_a_post_with_image(): void
    {
        Storage::fake('public');

        $user = User::factory()->create([
            'type' => 'user',
        ]);

        $this->actingAs($user);

        $response = $this->post(route('posts.store'), [
            'content' => 'This is my first product post.',
            'image' => UploadedFile::fake()->image('post.jpg', 600, 800),
        ]);

        $response->assertRedirect(route('profile'));
        $this->assertDatabaseHas('posts', [
            'user_id' => $user->id,
            'content' => 'This is my first product post.',
        ]);
    }
}
