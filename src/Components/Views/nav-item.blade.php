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

<div @isset($dropdown) x-data="{ open: false }" @click="open = !open" x-ref="down" @endisset class="flex-none flex flex-row justify-center items-center gap-2 relative cursor-pointer">
    {{ $slot }}
    @isset($dropdown)
        <div
            x-show="open"
            x-cloak
            x-anchor.bottom-start="$refs.down"
            @click.outside="open = false"
            class="absolute top-full right-0 mt-2 w-max mr-8
                               bg-tx-general-50 dark:bg-tx-general-800 border
                               border-tx-general-200 dark:border-tx-general-700
                               rounded shadow-md
                               grid {{ $columnClass }}">
            {{ $dropdown }}
        </div>
    @endisset
</div>
