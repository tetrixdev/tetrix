@php
    $useLink = $attributes->get('href') !== null;
    $baseClasses = 'p-3 pt-1 pb-1 border rounded-sm';
    $variantClasses = match($variant) {
        'primary' => 'text-white bg-primary-500 dark:bg-primary-600 focus:ring-primary-500',
        'secondary' => 'text-gray-800 dark:text-gray-200bg-gray-200 dark:bg-gray-700 focus:ring-gray-500',
        'success' => 'text-white bg-green-500 dark:bg-green-600 focus:ring-green-500',
        'danger' => 'text-white bg-red-500 dark:bg-red-600 focus:ring-red-500',
        'warning' => 'text-white bg-yellow-500 dark:bg-yellow-600 focus:ring-yellow-500',
        'info' => 'text-white bg-sky-500 dark:bg-sky-600 focus:ring-sky-500',
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
