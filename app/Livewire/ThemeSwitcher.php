<?php

namespace App\Livewire;

use Livewire\Component;

class ThemeSwitcher extends Component
{
    public $activeTheme = 'amber';
    public $activeBg = 'midnight';

    public function mount()
    {
        $this->activeTheme = session('user_theme', 'amber');
        $this->activeBg = session('user_bg', 'midnight');
    }

    public function setTheme($color)
    {
        $this->activeTheme = $color;
        session(['user_theme' => $color]);
        return redirect(request()->header('Referer'));
    }

    public function setBg($bg)
    {
        $this->activeBg = $bg;
        session(['user_bg' => $bg]);
        return redirect(request()->header('Referer'));
    }

    public function render()
    {
        return view('livewire.theme-switcher');
    }
}