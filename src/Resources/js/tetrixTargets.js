export function setupTxTargets() {
    // Check all HTML elements with attribute tx-targets="test1,test2,test3" and replace this with hx-header="{"TX-TARGETS": "test1,test2,test3"}" attribute, if hx-headers already exists, append the tx-targets to the existing value
    function replaceTxTargets() {
        document.querySelectorAll('[tx-targets]').forEach((element) => {
            let targets = element.getAttribute('tx-targets');
            let headers = element.getAttribute('hx-headers');
            if (headers) {
                headers = JSON.parse(headers);
                headers['TX-Targets'] = targets;
            } else {
                headers = { 'TX-Targets': targets };
            }
            element.setAttribute('hx-headers', JSON.stringify(headers));
            element.removeAttribute('tx-targets');
        });
    }
    replaceTxTargets();
    document.addEventListener('htmx:afterSwap', replaceTxTargets);
}