@php
    $useLink = $attributes->has('href');
    $isLoading = $attributes->has('loading');

    // Base classes
    $baseClasses = 'p-3 pt-1 pb-1 rounded-sm cursor-pointer
                    disabled:cursor-not-allowed focus-visible:outline-none
                    focus-visible:ring-2 drop-shadow-lg
                    transition-colors duration-200 ease-in-out relative flex ';

    // Variant-specific styles
    $variantClasses = match ($variant) {
        'primary' => 'text-primary-50 bg-primary-500 hover:bg-primary-600
                      focus-visible:ring-primary-500/75
                      disabled:text-primary-300 disabled:bg-primary-600 disabled:hover:bg-primary-600 disabled:opacity-50
                      dark:bg-primary-600 dark:hover:bg-primary-700
                      dark:disabled:text-primary-400 dark:disabled:bg-primary-700 dark:disabled:hover:bg-primary-700
                      dark:focus-visible:ring-primary-700/75',
        'secondary' => 'text-gray-800 bg-gray-200 dark:text-gray-200 dark:bg-gray-700 focus:ring-gray-500',
        'success'   => 'text-white bg-green-500 dark:bg-green-600 focus:ring-green-500',
        'danger'    => 'text-white bg-red-500 dark:bg-red-600 focus:ring-red-500',
        'warning'   => 'text-white bg-yellow-500 dark:bg-yellow-600 focus:ring-yellow-500',
        'info'      => 'text-white bg-sky-500 dark:bg-sky-600 focus:ring-sky-500',
        default     => '',
    };

    // Loading styles (opacity and preventing interaction)
    $loadingClasses = $isLoading ? 'opacity-75 pointer-events-none' : '';

    // Final class merging
    $finalClasses = trim("$baseClasses $variantClasses $loadingClasses");

    // Add additional user-defined classes
    if ($attributes->has('class')) {
        $finalClasses .= ' ' . $attributes->get('class');
    }
@endphp

@if ($useLink)
    <a href="{{ $attributes->get('href') }}" class="{{ $finalClasses }}" {{ $attributes->except(['class', 'loading']) }}>
        @if($isLoading)
            <div class="w-8 h-8 border-4 border-gray-300 border-t-gray-800 rounded-full animate-spin"></div> Loading...
        @else
            {{ $slot }}
        @endif
    </a>
@else
    <button type="{{ $type }}" class="{{ $finalClasses }} flex items-center justify-center gap-2" {{ $attributes->except(['class', 'loading']) }} {{ $isLoading ? 'disabled' : '' }}>
        @if($isLoading)
            <div class="w-4 h-4 border-2 border-gray-300 border-t-gray-800 rounded-full animate-spin"></div>
            <span class="text-sm">Loading...</span>
        @else
            {{ $slot }}
        @endif
    </button>
@endif
