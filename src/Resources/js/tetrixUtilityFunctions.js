export function findTxRefererInAncestors(element) {
    let currentElement = element;

    while (currentElement) {
        const hxHeadersAttr = currentElement.getAttribute?.('hx-headers');
        if (hxHeadersAttr) {
            try {
                const hxHeaders = JSON.parse(hxHeadersAttr);
                if (hxHeaders['TX-Referer']) {
                    return hxHeaders['TX-Referer'];
                }
            } catch (error) {
                console.warn('Invalid JSON in hx-headers attribute:', error, currentElement);
            }
        }
        currentElement = currentElement.parentElement;
    }

    return null;
}
