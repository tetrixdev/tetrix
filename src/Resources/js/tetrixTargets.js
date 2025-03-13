export function setupTxTargets() {
    // Check all HTML elements with attribute tx-targets="test1,test2,test3" and replace this with hx-header="{"TX-TARGETS": "test1,test2,test3"}" attribute, if hx-headers already exists, append the tx-targets to the existing value
    function replaceTxTargets() {
        document.querySelectorAll('[tx-targets]').forEach((element) => {
            let targets = element.getAttribute('tx-targets');

            // Creating/Updating the hx-headers attribute
            let headers = element.getAttribute('hx-headers');
            if (headers) {
                headers = JSON.parse(headers);
                headers['TX-Targets'] = targets;
            } else {
                headers = { 'TX-Targets': targets };
            }
            element.setAttribute('hx-headers', JSON.stringify(headers));

            // Adding hx-get attribute if hx-get,hx-post,hx-put,hx-delete do not exist, no value = current page
            if (!element.hasAttribute('hx-get') && !element.hasAttribute('hx-post') && !element.hasAttribute('hx-put') && !element.hasAttribute('hx-delete')) {
                element.setAttribute('hx-get', '');
            }

            element.removeAttribute('tx-targets');
        });
    }
    replaceTxTargets();
    document.addEventListener('htmx:afterSwap', replaceTxTargets);
}