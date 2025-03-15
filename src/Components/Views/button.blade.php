@php
    $useLink = $attributes->get('href') !== null;
    $baseClasses = 'p-3 pt-1 pb-1 border rounded-sm';
    $variantClasses = match($variant) {
        'primary' => 'text-white bg-primary-500 dark:bg-primary-600 focus:ring-primary-500',
        'secondary' => 'text-gray-800 dark:text-gray-200bg-secondary-200 dark:bg-secondary-700 focus:ring-secondary-500',
        'success' => 'text-white bg-success-500 dark:bg-success-600 focus:ring-success-500',
        'danger' => 'text-white bg-danger-500 dark:bg-danger-600 focus:ring-danger-500',
        'warning' => 'text-white bg-warning-500 dark:bg-warning-600 focus:ring-warning-500',
        'info' => 'text-white bg-info-500 dark:bg-info-600 focus:ring-info-500',
    };
    $finalClasses = trim($baseClasses . ' ' . $variantClasses);
    if($attributes->has('class')) {
        $finalClasses = trim($finalClasses . ' ' . $attributes->get('class'));
    }
@endphp

@if($useLink)
    <a type="{{ $type }}"
       class="{{ $finalClasses }}"
       {{ $attributes->except("class") }}>
        {{ $slot }}
    </a>
@else
    <button type="{{ $type }}"
            class="{{ $finalClasses }}"
            {{ $attributes->except("class") }}>
        {{ $slot }}
    </button>
@endif
