@php
    $useLink = $attributes->has('href');

    // Base classes
    $baseClasses = 'p-3 pt-1 pb-1
                    rounded-sm cursor-pointer
                    disabled:cursor-not-allowed focus:outline-none
                    focus:ring-2 focus:ring-offset-1
                    focus:ring-offset-tx-bg focus:ring-tx-txt
                    dark:focus:ring-offset-tx-bg-dark dark:focus:ring-tx-txt-dark
                    disabled:opacity-50
                    transition-colors duration-200 ease-in-out relative flex ';

    // Color Classes
    $colorClasses = 'text-tx-primary-50 bg-tx-primary-500 hover:bg-tx-primary-600
                     disabled:text-tx-primary-300 disabled:bg-tx-primary-600 disabled:hover:bg-tx-primary-600
                     dark:bg-tx-primary-600 dark:hover:bg-tx-primary-700
                     dark:disabled:text-tx-primary-400 dark:disabled:bg-tx-primary-700 dark:disabled:hover:bg-tx-primary-700';

    // User Classes
    $customClasses = $attributes->get('class') ?? '';

    // Combined Classes
    $combinedClasses = trim("$baseClasses $colorClasses $customClasses");
@endphp

@if ($useLink)
    <a href="{{ $attributes->get('href') }}" class="{{ $combinedClasses }}" {{ $attributes->except(['class']) }}>
        {{ $slot }}
    </a>
@else
    <button type="{{ $type }}" class="{{ $combinedClasses }}" {{ $attributes->except(['class']) }}>
        {{ $slot }}
    </button>
@endif
