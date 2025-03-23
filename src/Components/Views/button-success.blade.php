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
    $colorClasses = 'text-green-50 bg-green-500 hover:bg-green-600
                     disabled:text-green-300 disabled:bg-green-600 disabled:hover:bg-green-600
                     dark:bg-green-600 dark:hover:bg-green-700
                     dark:disabled:text-green-400 dark:disabled:bg-green-700 dark:disabled:hover:bg-green-700';

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
