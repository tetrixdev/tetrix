@php
    $useLink = $attributes->has('href');

    // Base classes
    // We've removed focus:outline-none and focus:ring-offset-1 here as they interfere with ColorClasses
    $baseClasses = 'p-3 pt-1 pb-1
                    rounded-sm cursor-pointer
                    disabled:cursor-not-allowed
                    focus:ring-2
                    focus:ring-offset-tx-bg focus:ring-tx-txt
                    dark:focus:ring-offset-tx-bg-dark dark:focus:ring-tx-txt-dark
                    disabled:opacity-50
                    transition-colors duration-200 ease-in-out relative flex ';

    // Color Classes
    $colorClasses = 'outline-1 shadow-inner focus:ring-offset-2
                     outline-tx-bg-dark
                     dark:outline-tx-bg ';

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
