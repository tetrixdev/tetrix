import Alpine from 'alpinejs';
import htmx from 'htmx.org';

Alpine.start()
window.Alpine = Alpine

// Prevent nested out-of-body swaps
// If an element with hx-swap-oob is nested within another element retrieved by htmx, it will not work without this configuration.
htmx.config.allowNestedOobSwaps = false;
window.htmx = htmx;