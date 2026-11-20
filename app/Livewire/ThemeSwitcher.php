<?php

namespace App\Livewire;

use Illuminate\Contracts\View\View;
use Livewire\Component;

class ThemeSwitcher extends Component
{
    public string $activeTheme = 'amber';

    public string $activeBg = 'midnight';

    public function mount(): void
    {
        $this->activeTheme = session('user_theme', 'amber');
        $this->activeBg = session('user_bg', 'midnight');
    }

    public function setTheme(string $color): void
    {
        abort_unless(
            in_array($color, ['amber', 'rose', 'emerald', 'indigo', 'cyan', 'purple', 'orange'], true),
            404,
        );

        $this->activeTheme = $color;
        session(['user_theme' => $color]);

        $this->redirect($this->themeRedirectUrl());
    }

    public function setBg(string $bg): void
    {
        abort_unless(
            in_array($bg, ['midnight', 'black', 'gray', 'navy', 'slate'], true),
            404,
        );

        $this->activeBg = $bg;
        session(['user_bg' => $bg]);

        $this->redirect($this->themeRedirectUrl());
    }

    public function render(): View
    {
        return view('livewire.theme-switcher');
    }

    private function themeRedirectUrl(): string
    {
        $referer = request()->header('Referer');

        return is_string($referer)
            && parse_url($referer, PHP_URL_HOST) === request()->getHost()
            ? $referer
            : route('home');
    }
}
