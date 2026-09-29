<?php

namespace Database\Seeders;

use App\Models\Comment;
use App\Models\Conversation;
use App\Models\Follow;
use App\Models\Like;
use App\Models\Message;
use App\Models\Post;
use App\Models\PostImage;
use App\Models\Reel;
use App\Models\Report;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class SocialNetworkSeeder extends Seeder
{
    public function run(): void
    {
        // Ensure at least 10 normal users exist for realistic data
        $userRole = \Spatie\Permission\Models\Role::firstOrCreate([
            'name' => 'User',
            'guard_name' => 'web',
        ]);

        if (User::role('User')->count() < 10) {
            User::factory(10)->create()->each(function ($user) use ($userRole) {
                $user->assignRole($userRole);
            });
        }

        $users = User::role('User')->get();

        $postImages = [
            'https://images.unsplash.com/photo-1521572267360-ee0c2909d518?auto=format&fit=crop&w=1200&q=80',
            'https://images.unsplash.com/photo-1542291026-7eec264c27ff?auto=format&fit=crop&w=1200&q=80',
            'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?auto=format&fit=crop&w=1200&q=80',
            'https://images.unsplash.com/photo-1496181133206-80ce9b88a853?auto=format&fit=crop&w=1200&q=80',
            'https://images.unsplash.com/photo-1517836357463-d25dfeac3438?auto=format&fit=crop&w=1200&q=80',
        ];

        $reelVideos = [
            'https://www.w3schools.com/html/mov_bbb.mp4',
            'https://www.w3schools.com/html/movie.mp4',
            'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/BigBuckBunny.mp4',
            'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/ElephantsDream.mp4',
        ];

        $posts = Post::factory(20)->create();

        foreach ($posts as $index => $post) {
            if ($post->images()->count() === 0) {
                $imageUrl = $postImages[$index % count($postImages)];
                $imagePath = $this->downloadMedia($imageUrl, 'posts/post-' . $post->id . '.jpg');

                PostImage::create([
                    'post_id' => $post->id,
                    'image_path' => $imagePath,
                    'sort_order' => 1,
                ]);

                $post->update(['image' => $imagePath]);
            }
        }

        $reels = Reel::factory(15)->create();

        foreach ($reels as $index => $reel) {
            $videoUrl = $reelVideos[$index % count($reelVideos)];
            $videoPath = $this->downloadMedia($videoUrl, 'reels/reel-' . $reel->id . '.mp4');
            $thumbnailPath = $this->downloadMedia(
                $postImages[$index % count($postImages)],
                'reels/reel-' . $reel->id . '-thumb.jpg'
            );

            $reel->update([
                'video_path' => $videoPath,
                'thumbnail' => $thumbnailPath,
                'status' => 'published',
            ]);
        }

        // Likes (random users liking random posts/reels)
        foreach ($posts as $post) {
            $likers = $users->random(min(fake()->numberBetween(0, 8), $users->count()));
            foreach ($likers as $liker) {
                Like::firstOrCreate([
                    'likeable_type' => Post::class,
                    'likeable_id' => $post->id,
                    'user_id' => $liker->id,
                ]);
            }
        }

        foreach ($reels as $reel) {
            $likers = $users->random(min(fake()->numberBetween(0, 10), $users->count()));
            foreach ($likers as $liker) {
                Like::firstOrCreate([
                    'likeable_type' => Reel::class,
                    'likeable_id' => $reel->id,
                    'user_id' => $liker->id,
                ]);
            }
        }

        // Comments (random users commenting on random posts/reels)
        foreach ($posts as $post) {
            $commentCount = fake()->numberBetween(0, 5);
            for ($i = 0; $i < $commentCount; $i++) {
                Comment::factory()->create([
                    'commentable_type' => Post::class,
                    'commentable_id' => $post->id,
                ]);
            }
        }

        foreach ($reels as $reel) {
            $commentCount = fake()->numberBetween(0, 6);
            for ($i = 0; $i < $commentCount; $i++) {
                Comment::factory()->create([
                    'commentable_type' => Reel::class,
                    'commentable_id' => $reel->id,
                ]);
            }
        }

        // Follows (random follow relationships)
        foreach ($users as $user) {
            $following = $users->where('id', '!=', $user->id)->random(min(fake()->numberBetween(0, 5), $users->count() - 1));
            foreach ($following as $followedUser) {
                Follow::firstOrCreate([
                    'follower_id' => $user->id,
                    'following_id' => $followedUser->id,
                ]);
            }
        }

        // Conversations + Messages (random pairs chatting)
        for ($i = 0; $i < 8; $i++) {
            $pair = $users->random(2);
            $conversation = Conversation::create();
            $conversation->users()->attach($pair->pluck('id'));

            $messageCount = fake()->numberBetween(2, 10);
            for ($j = 0; $j < $messageCount; $j++) {
                Message::create([
                    'conversation_id' => $conversation->id,
                    'sender_id' => $pair->random()->id,
                    'content' => fake()->sentence(fake()->numberBetween(3, 15)),
                ]);
            }
        }

        // Reports (random users reporting random posts/reels)
        for ($i = 0; $i < 10; $i++) {
            $reportable = fake()->boolean() ? $posts->random() : $reels->random();

            Report::create([
                'reportable_type' => get_class($reportable),
                'reportable_id' => $reportable->id,
                'reported_by' => $users->random()->id,
                'reason' => fake()->randomElement(['spam', 'abuse', 'nudity', 'harassment', 'other']),
                'description' => fake()->sentence(),
                'status' => fake()->randomElement(['pending', 'pending', 'reviewed', 'actioned']),
            ]);
        }
    }

    private function downloadMedia(string $url, string $relativePath): string
    {
        $storage = Storage::disk('public');
        $directory = dirname($relativePath);

        if ($directory !== '.') {
            $storage->makeDirectory($directory);
        }

        $fullPath = $storage->path($relativePath);

        if (! file_exists($fullPath)) {
            $contents = @file_get_contents($url);

            if ($contents !== false) {
                file_put_contents($fullPath, $contents);
            }
        }

        return $relativePath;
    }
}