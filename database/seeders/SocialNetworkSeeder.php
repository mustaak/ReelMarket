<?php

namespace Database\Seeders;

use App\Models\Comment;
use App\Models\Conversation;
use App\Models\Follow;
use App\Models\Like;
use App\Models\Message;
use App\Models\Post;
use App\Models\Reel;
use App\Models\Report;
use App\Models\User;
use Illuminate\Database\Seeder;

class SocialNetworkSeeder extends Seeder
{
    public function run(): void
    {
        // Ensure at least 10 normal users exist for realistic data
        $userRole = \Spatie\Permission\Models\Role::where('name', 'User')->first();

        if (User::role('User')->count() < 10) {
            User::factory(10)->create()->each(function ($user) use ($userRole) {
                $user->assignRole($userRole);
            });
        }

        $users = User::role('User')->get();

        // Posts
        $posts = Post::factory(20)->create();

        // Reels
        $reels = Reel::factory(15)->create();

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
}