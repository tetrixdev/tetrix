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

?>

<div @isset($dropdown)
     x-data="{ open: false }"
     @endisset
     class="relative">
    <div @isset($dropdown)
         @click="open = !open"
         x-ref="navitem"
         :class="{'bg-tx-general-200 dark:bg-tx-general-700': open }"
         @endisset
         class="h-[47px] px-3
                hover:bg-tx-general-200 dark:hover:bg-tx-general-700
                cursor-pointer select-none
                flex-none flex flex-row justify-center items-center gap-2">
        {{ $slot }}
    </div>
    @isset($dropdown)
        <div
            x-show="open"
            x-cloak
            x-anchor.bottom-start.offset.8="$refs.navitem"
            @click.outside="open = false"
            class="absolute
                   w-max
                   bg-tx-general-50 dark:bg-tx-general-800 border
                   rounded border-tx-general-300 dark:border-tx-general-700
                   shadow-md
                   z-10
                   grid {{ $columnClass }}">
            {{ $dropdown }}
        </div>
    @endisset
</div>
