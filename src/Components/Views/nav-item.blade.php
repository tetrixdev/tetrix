<?PHP

$dom = new DOMDocument();

// Suppress warnings about invalid HTML tags etc.
@$dom->loadHTML("<body>$dropdown</body>");

$body = $dom->getElementsByTagName('body')->item(0);

$columnCount = 0;
if ($body !== null) {
    foreach ($body->childNodes as $node) {
        // Only count if this is a top-level element node
        if ($node->nodeType === XML_ELEMENT_NODE) {
            $columnCount++;
        }
    }
}

// This might look odd, but otherwise vite will not compile the class correctly (classes need to be mentioned in full)
$columnClass = '';
switch($columnCount) {
    case 1:
        $columnClass = 'grid-cols-1';
        break;
    case 2:
        $columnClass = 'grid-cols-2';
        break;
    case 3:
        $columnClass = 'grid-cols-3';
        break;
    case 4:
        $columnClass = 'grid-cols-4';
        break;
    case 5:
        $columnClass = 'grid-cols-5';
        break;
    case 6:
        $columnClass = 'grid-cols-6';
        break;
    case 7:
        $columnClass = 'grid-cols-7';
        break;
    case 8:
        $columnClass = 'grid-cols-8';
        break;
}

if(isset($dropdown)) {
    $rawDropdown = $dropdown;

    // Remove outer x-tx::sub-nav-column
    $flattenedHtml = preg_replace([
        '/<x-tx::sub-nav-column[^>]*>/i',  // opening tag with optional attributes
        '/<\/x-tx::sub-nav-column>/i'      // closing tag
    ], '', $rawDropdown);
}


?>

<div @isset($dropdown)
         x-data="{ open: false }"
     @else
         @click="navOpen = false"
     @endisset
     class="relative w-full {{ config('tetrix.nav.breakpoint') }}:w-auto">
    <div @isset($dropdown)
             @click="open = !open"
         x-ref="navitem"
         :class="{'bg-tx-general-150 dark:bg-tx-general-800': open }"
         @endisset
         class="h-[47px] px-3
                w-full {{ config('tetrix.nav.breakpoint') }}:w-auto
                hover:bg-tx-general-150 dark:hover:bg-tx-general-800
                cursor-pointer select-none
                flex-none flex flex-row {{ config('tetrix.nav.breakpoint') }}:justify-center justify-start items-center gap-2">
        {{ $slot }}
    </div>
    @isset($dropdown)
        {{-- Desktop --}}
        <div class="hidden {{ config('tetrix.nav.breakpoint') }}:block">
            <div
                    x-show="open"
                    x-cloak
                    x-anchor.placement.bottom-start.offset.8="$refs.navitem"
                    @click.outside="open = false"

                    class="absolute
                   w-max
                   bg-tx-general-0 dark:bg-tx-general-900 border
                   rounded border-tx-general-300 dark:border-tx-general-800
                   shadow-md
                   z-10
                   grid {{ $columnClass }}">
                {{ $dropdown }}
            </div>
        </div>
        {{-- Mobile --}}
        <div class="block {{ config('tetrix.nav.breakpoint') }}:hidden">
            <div
                    x-show="open"
                    x-cloak
                    @click.outside="open = false"
                    x-transition:enter="transition ease-out duration-100"
                    x-transition:enter-start="opacity-0 scale-95"
                    x-transition:enter-end="opacity-100 scale-100"
                    x-transition:leave="transition ease-in duration-75"
                    x-transition:leave-start="opacity-100 scale-100"
                    x-transition:leave-end="opacity-0 scale-95"
                    class="absolute
                       left-0 top-full m-2
                       w-[calc(100%-16px)]
                       bg-tx-general-0 dark:bg-tx-general-900
                       border rounded border-tx-general-300 dark:border-tx-general-800
                       shadow-md
                       z-10
                       grid grid-cols-1">
                {!! $flattenedHtml !!}
            </div>
        </div>
    @endisset
</div>
