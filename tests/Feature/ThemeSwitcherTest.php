<?php

namespace Tests\Feature;

use App\Livewire\ThemeSwitcher;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ThemeSwitcherTest extends TestCase
{
    use RefreshDatabase;

    public function test_black_and_gray_page_tones_are_saved_in_the_session(): void
    {
        Livewire::test(ThemeSwitcher::class)
            ->call('setBg', 'black')
            ->assertSet('activeBg', 'black');
        $this->assertSame('black', session('user_bg'));

        Livewire::test(ThemeSwitcher::class)
            ->call('setBg', 'gray')
            ->assertSet('activeBg', 'gray');
        $this->assertSame('gray', session('user_bg'));
    }

    public function test_unsupported_theme_values_are_rejected(): void
    {
        Livewire::test(ThemeSwitcher::class)
            ->call('setBg', 'url-javascript')
            ->assertStatus(404);
    }

    public function test_auth_pages_use_the_selected_dark_page_tone(): void
    {
        $this->withSession(['user_bg' => 'black'])
            ->get('/login')
            ->assertSee('data-theme="black"', false);

        $this->withSession(['user_bg' => 'gray'])
            ->get('/register')
            ->assertSee('data-theme="gray"', false);
    }
}
