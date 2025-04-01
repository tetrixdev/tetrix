{{-- Desktop --}}
<nav class="hidden
            px-3
            {{ config('tetrix.nav.breakpoint') }}:flex flex-none flex-row items-center">
    {{ $slot }}
</nav>
{{-- Mobile --}}
<nav class="block {{ config('tetrix.nav.breakpoint') }}:hidden">
    <div x-data="{ navOpen: false }"
         class="pr-6">
        <div @click="navOpen = !navOpen"
             x-ref="hamburgericon"
             :class="{'bg-tx-general-150 dark:bg-tx-general-850': navOpen }"
             class="p-1
                    bg-tx-general-50 hover:bg-tx-general-150 dark:bg-tx-general-950 dark:hover:bg-tx-general-850
                    border border-tx-general-300 dark:border-tx-general-800 rounded-lg
                    cursor-pointer">
            <i class="fa-solid fa-bars fa-lg fa-fw"></i>
        </div>
        <div
                x-show="navOpen"
                x-cloak
                @click.outside="navOpen = false"
                x-transition:enter="transition ease-out duration-100"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-75"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"
                class="absolute top-12 left-0 w-dvh w-full flex flex-col justify-start
                bg-tx-general-0 dark:bg-tx-general-900
                border-b-[1px] border-b-tx-general-300 dark:border-b-tx-general-800
                z-50">
            {{ $slot }}
        </div>
    </div>
</nav>
