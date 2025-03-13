import htmx from 'htmx.org';

export function initHtmx() {
    htmx.config.allowNestedOobSwaps = false;
    window.htmx = htmx;
}