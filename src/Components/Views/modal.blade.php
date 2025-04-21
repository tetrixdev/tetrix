<div class="fixed inset-0 z-60 flex items-center justify-center m-6">
    <div class="absolute inset-0 bg-black opacity-75 -m-6"></div>
    <div class="relative">
        <x-tx::card>
            @isset($header)
                <x-slot:header>
                    {{ $header }}
                </x-slot:header>
            @endisset
            {{ $slot }}
            @isset($footer)
                <x-slot:footer>
                    {{ $footer }}
                </x-slot:footer>
            @endisset
        </x-tx::card>
    </div>
</div>