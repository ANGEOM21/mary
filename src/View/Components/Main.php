<?php

namespace Mary\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Main extends Component
{
    public string $url;

    public function __construct(

        // Slots
        public mixed $sidebar = null,
        public mixed $content = null,
        public mixed $footer = null,
        public mixed $topbar = null,
        public ?bool $fullWidth = false,
        public ?bool $withNav = false,
        public ?string $collapseText = 'Collapse',
        public ?string $collapseIcon = 'o-bars-3-bottom-right',
        public ?bool $collapsible = false,
    ) {
        $this->url = route('mary.toogle-sidebar', absolute: false);
    }

    public function render(): View|Closure|string
    {
        return <<<'HTML'
                <main @class(["w-full mx-auto", "max-w-screen-2xl" => !$fullWidth])>
                    <div @class([
                        "drawer lg:drawer-open",
                        "drawer-end" => $sidebar?->attributes['right'],
                        "max-sm:drawer-end" => $sidebar?->attributes['right-mobile'],
                    ])
                    >
                        <input id="{{ $sidebar?->attributes['drawer'] }}" type="checkbox" class="drawer-toggle" />
                        <div {{ $content->attributes->class(["drawer-content w-full mx-auto"]) }}>
                            <!-- TOPBAR -->
                            @if($topbar)
                                {{ $topbar }}
                            @endif
                            <!-- MAIN CONTENT -->
                            <div class="p-5 lg:px-10 lg:p-5">
                            {{ $content }}
                            </div>
                            <div class="bg-base-100 pointer-events-none sticky bottom-0 flex h-40 [mask-image:linear-gradient(transparent,#000000)]"></div>
                        </div>

                        <!-- SIDEBAR -->
                        @if($sidebar)
                        <!-- SIDEBAR COLLAPSE -->
                            <div
                                x-data="{
                                    collapsed: {{ session('mary-sidebar-collapsed', 'false') }},
                                    collapseText: '{{ $collapseText }}',
                                    toggle() {
                                        this.collapsed = !this.collapsed;
                                        fetch('{{ $url }}?collapsed=' + this.collapsed);
                                        this.$dispatch('sidebar-toggled', this.collapsed);
                                    }
                                }"
                                @toggle-sidebar.window="toggle()"
                                @menu-sub-clicked="if(collapsed) { toggle() }"
                                @class(["drawer-side z-50 lg:z-auto", "top-0 lg:top-[73px] lg:h-[calc(100vh-73px)]" => $withNav])
                            >

                                <label for="{{ $sidebar?->attributes['drawer'] }}" aria-label="close sidebar" class="drawer-overlay"></label>

                                <!-- SIDEBAR CONTENT -->
                                <div
                                    :class="collapsed
                                        ? '!w-[70px] [&>*_summary::after]:!hidden [&_.mary-hideable]:!hidden [&_.display-when-collapsed]:!block [&_.hidden-when-collapsed]:!hidden'
                                        : '!w-[270px] [&>*_summary::after]:!block [&_.mary-hideable]:!block [&_.hidden-when-collapsed]:!block [&_.display-when-collapsed]:!hidden'"
                                    {{
                                        $sidebar->attributes->class([
                                            "flex flex-col !transition-all !duration-100 ease-out overflow-x-hidden overflow-y-auto h-screen",
                                            "w-[70px] [&>*_summary::after]:hidden [&_.mary-hideable]:hidden [&_.display-when-collapsed]:block [&_.hidden-when-collapsed]:hidden" => session('mary-sidebar-collapsed') == 'true',
                                            "w-[270px] [&>*_summary::after]:block [&_.mary-hideable]:block [&_.hidden-when-collapsed]:block [&_.display-when-collapsed]:hidden" => session('mary-sidebar-collapsed') != 'true',
                                            "lg:h-[calc(100vh-73px)]" => $withNav
                                        ])
                                    }}
                                >
                                    <div class="">
                                        {{ $sidebar }}
                                    </div>
                                </div>
                            </div>
                        @endif
                        <!-- END SIDEBAR-->

                    </div>
                </main>

                 <!-- FOOTER -->
                 @if($footer)
                    <footer {{ $footer?->attributes->class(["mx-auto w-full", "max-w-screen-2xl" => !$fullWidth ]) }}>
                        {{ $footer }}
                    </footer>
                @endif
                HTML;
    }
}