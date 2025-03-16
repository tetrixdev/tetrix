@php
    $useLink = $attributes->has('href');

    // Base classes
    $baseClasses = 'p-3 pt-1 pb-1
                    rounded-sm cursor-pointer
                    disabled:cursor-not-allowed focus-visible:outline-none
                    focus-visible:ring-2 focus-visible:ring-offset-1 focus-visible:ring-offset-tx-bg dark:focus-visible:ring-offset-tx-bg-dark drop-shadow-lg
                    disabled:opacity-50
                    transition-colors duration-200 ease-in-out relative flex ';

    // Color Classes
    $colorClasses = 'text-tx-primary-50 bg-tx-primary-500 hover:bg-tx-primary-600
                     focus-visible:ring-tx-primary-500/75
                     disabled:text-tx-primary-300 disabled:bg-tx-primary-600 disabled:hover:bg-tx-primary-600
                     dark:bg-tx-primary-600 dark:hover:bg-tx-primary-700
                     dark:focus-visible:ring-tx-primary-600/75
                     dark:disabled:text-tx-primary-400 dark:disabled:bg-tx-primary-700 dark:disabled:hover:bg-tx-primary-700';

    // User Classes
    $customClasses = $attributes->get('class') ?? '';

    // Combined Classes
    $combinedClasses = trim("$baseClasses $colorClasses $customClasses");


//    // Variant-specific styles
//    $variantClasses = match ($variant) {
//        'tx-primary' => 'text-tx-primary-50 bg-tx-primary-500 hover:bg-tx-primary-600
//                      focus-visible:ring-tx-primary-500/75
//                      disabled:text-tx-primary-300 disabled:bg-tx-primary-600 disabled:hover:bg-tx-primary-600
//                      dark:bg-tx-primary-600 dark:hover:bg-tx-primary-700
//                      dark:focus-visible:ring-tx-primary-600/75
//                      dark:disabled:text-tx-primary-400 dark:disabled:bg-tx-primary-700 dark:disabled:hover:bg-tx-primary-700',
//        'secondary' => 'text-gray-800 bg-gray-200 dark:text-gray-200 dark:bg-gray-700 focus:ring-gray-500',
//        'success'   => 'text-white bg-green-500 dark:bg-green-600 focus:ring-green-500',
//        'danger'    => 'text-white bg-red-500 dark:bg-red-600 focus:ring-red-500',
//        'warning'   => 'text-white bg-yellow-500 dark:bg-yellow-600 focus:ring-yellow-500',
//        'info'      => 'text-white bg-sky-500 dark:bg-sky-600 focus:ring-sky-500',
//        default     => '',
//    };
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
