<div class="w-full rounded-sm bg-tx-general-0 dark:bg-tx-general-900 border-1 border-tx-general-300 dark:border-tx-general-800">
    @isset($header)
        <div class="p-3">
            {{ $header }}
        </div>
    @endif
    <x-tx::hr/>
    <div class="p-3">
    {{ $slot }}
    </div>
    <x-tx::hr/>
    @isset($footer)
        <div class="p-3">
            {{ $footer }}
        </div>
    @endif
    <x-tx::hr/>
</div>