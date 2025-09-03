<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class LanguageSelector extends Component
{
    /**
     * The available locales.
     *
     * @var array
     */
    public $locales;

    /**
     * Create a new component instance.
     */
    public function __construct()
    {
        // Define available locales with their names
        $this->locales = [
            'en' => 'English',
            'es' => 'Español',
            'fr' => 'Français',
            'de' => 'Deutsch',
            'ja' => '日本語',
            'zh' => '中文',
        ];
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.language-selector');
    }
}
