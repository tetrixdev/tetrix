<header class=" w-full
                h-12
                min-h-12
                flex flex-row items-center
                text-tx-general-700 bg-tx-general-100
                dark:text-tx-general-300 dark:bg-tx-general-900
                border-b-[1px] border-b-tx-general-300 dark:border-b-tx-general-800">
    @isset($left)
        <div class="flex-none">
            {{ $left }}
        </div>
    @endif
    <div class="flex-1"></div>
    @isset($right)
        <div class="flex-none">
            {{ $right }}
        </div>
    @endif
</header>
