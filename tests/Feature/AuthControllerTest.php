<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_render_the_login_form_and_find_password_recovery(): void
    {
        $this->get(route('login'))
            ->assertOk()
            ->assertSee('Forgot password?')
            ->assertSee(route('password.request'));
    }

    public function test_guest_can_render_the_registration_form(): void
    {
        $this->get(route('register'))
            ->assertOk()
            ->assertSee('Join ReelMarket')
            ->assertSee('Confirm password');
    }

    public function test_guest_can_render_the_password_reset_request_form(): void
    {
        $this->get(route('password.request'))
            ->assertOk()
            ->assertSee('Send password reset link');
    }

    public function test_guest_can_render_the_password_reset_form(): void
    {
        $this->get(route('password.reset', [
            'token' => 'reset-token',
            'email' => 'person@example.com',
        ]))
            ->assertOk()
            ->assertSee('Choose a new password')
            ->assertSee('person@example.com');
    }

    public function test_registration_creates_a_user_and_authenticates_the_session(): void
    {
        $response = $this->post(route('register'), [
            'name' => 'Reel Market User',
            'email' => 'person@example.com',
            'password' => 'correct-horse-battery',
            'password_confirmation' => 'correct-horse-battery',
        ]);

        $user = User::query()->where('email', 'person@example.com')->sole();

        $response->assertRedirectToRoute('home');
        $this->assertAuthenticatedAs($user);
        $this->assertTrue(Hash::check('correct-horse-battery', $user->password));
        $this->assertTrue($user->hasRole('User'));
    }

    public function test_password_reset_request_sends_a_link_without_revealing_unknown_emails(): void
    {
        Notification::fake();
        $user = User::factory()->create();

        $this->from(route('password.request'))
            ->post(route('password.email'), ['email' => $user->email])
            ->assertRedirect(route('password.request'))
            ->assertSessionHas('status');

        Notification::assertSentTo($user, ResetPassword::class);

        $this->from(route('password.request'))
            ->post(route('password.email'), ['email' => 'unknown@example.com'])
            ->assertRedirect(route('password.request'))
            ->assertSessionHas('status');
    }

    public function test_valid_reset_token_changes_the_password_and_can_be_used_to_log_in(): void
    {
        $user = User::factory()->create();
        $token = Password::createToken($user);

        $this->post(route('password.update'), [
            'token' => $token,
            'email' => $user->email,
            'password' => 'brand-new-password',
            'password_confirmation' => 'brand-new-password',
        ])->assertRedirectToRoute('login');

        $this->assertTrue(Hash::check('brand-new-password', $user->fresh()->password));

        $this->post(route('login'), [
            'email' => $user->email,
            'password' => 'brand-new-password',
        ])->assertRedirectToRoute('home');

        $this->assertAuthenticatedAs($user);
    }

    public function test_invalid_reset_token_does_not_change_the_password(): void
    {
        $user = User::factory()->create();
        $originalPassword = $user->password;

        $this->from(route('password.reset', [
            'token' => 'invalid-token',
            'email' => $user->email,
        ]))->post(route('password.update'), [
            'token' => 'invalid-token',
            'email' => $user->email,
            'password' => 'brand-new-password',
            'password_confirmation' => 'brand-new-password',
        ])->assertRedirect()
            ->assertSessionHasErrors('email');

        $this->assertSame($originalPassword, $user->fresh()->password);
    }
}
