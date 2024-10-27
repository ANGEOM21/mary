<?php

namespace Mary\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Topbar extends Component
{

    public function __construct(
        public ?bool $sticky = false,
        public ?bool $fullWidth = false,

        // Slots
        public mixed $brand = null,
        public mixed $search = null,
        public mixed $actions = null,
        public mixed $dropdownProfile = null,
        public mixed $color = "#cccccc",
    ) {
        $this->nameToColor(auth()->user()->name);
    }

    public function nameToColor($name)
    {
        $hash = md5($name);
        $color = '#' . substr($hash, 0, 6);

        // Convert hex to RGB
        $r = hexdec(substr($color, 1, 2));
        $g = hexdec(substr($color, 3, 2));
        $b = hexdec(substr($color, 5, 2));

        // Calculate brightness
        $brightness = ($r * 0.299 + $g * 0.587 + $b * 0.114);

        if ($brightness > 200) {
            $r = $g = $b = 204; // #cccccc
        } elseif ($brightness < 55) {
            $r = $g = $b = 204; // #cccccc
        }

        // Return color in rgba format with opacity
        return $this->color = "rgba($r, $g, $b, 0.75)"; // Adjust the last parameter for opacity
    }


    public function render(): View|Closure|string
    {
        return <<<'HTML'
                <div x-data="{ isScrolled: false }"
                    x-init="window.addEventListener('scroll', () => { isScrolled = window.scrollY > 0 })"
                    x-bind:class="isScrolled ? 'bg-base-100 bg-opacity-90 shadow-sm' : 'bg-base-100'"
                    class="text-base-content backdrop-blur flex h-16 w-full justify-center transition-shadow duration-100 [transform:translate3d(0,0,0)] {{ $sticky ? 'sticky top-0 z-30' : '' }}"
                >
                    <nav class="navbar w-full gap-3">
                        <div {{ $actions?->attributes->class(["flex items-center gap-4"]) }}>
                            {{ $actions }}
                        </div>
                        <div class="flex-1 justify-between py-1">
                            <div {{ $search?->attributes->class(["flex-1 flex items-center"]) }}>
                                {{ $search }}
                            </div>
                            <div class="flex items-center justify-center gap-3 dropdown dropdown-bottom dropdown-end">
                                <span class="text-lg font-semibold hidden lg:block">
                                    {{ Str::before(auth()->user()->name, ' ') }}
                                </span>
                                <div tabindex="0" role="button" class="normal-case avatar btn btn-ghost btn-circle ">
                                    <div class="w-10 rounded-full flex">
                                        <div class="h-10 w-10 flex items-center justify-center"
                                        style="background-color: {{ $color }};"
                                        >
                                            {{ Str::substr(auth()->user()->name, 0, 1) }}
                                        </div>
                                    </div>
                                </div>
                                <x-mary-menu class="dropdown-content bg-base-100 rounded-box z-[1] w-52  p-2 mt-2 shadow">
                                    <x-mary-list-item :item="auth()->user()" value="name" sub-value="email" no-separator
                                        no-hover class="-mx-2 !-my-2 rounded">
                                    </x-mary-list-item>
                                    <x-mary-menu-separator />
                                    {{ $dropdownProfile }}
                                </x-mary-menu>
                            </div>
                        </div>
                    </nav>
                </div>
            HTML;
    }
}
