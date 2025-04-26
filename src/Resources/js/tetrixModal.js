export function setupTxModal() {
    // Check all HTML elements with attribute tx-targets="test1,test2,test3" and replace this with hx-header="{"TX-TARGETS": "test1,test2,test3"}" attribute, if hx-headers already exists, append the tx-targets to the existing value
    function replaceTxModal() {
        document.querySelectorAll('[tx-modal]').forEach((element) => {
            let modalUrl = element.getAttribute('tx-modal');

            // Check to make sure that there's no hx-get,hx-post,hx-put,hx-delete attributes
            if (element.hasAttribute('hx-get') || element.hasAttribute('hx-post') || element.hasAttribute('hx-put') || element.hasAttribute('hx-delete')) {
                throw new Error(`Element with tx-modal="${modalUrl}" must not have hx-get, hx-post, hx-put or hx-delete attributes.`);
            }

            // Check for hx-swap and ensure it is exactly "beforeend" if present
            if (element.hasAttribute('hx-swap')) {
                const swapValue = element.getAttribute('hx-swap');
                if (swapValue !== 'beforeend') {
                    throw new Error(`Element with tx-modal="${modalUrl}" has hx-swap="${swapValue}", but only "beforeend" is allowed.`);
                }
            }

            // Check for hx-target and ensure it is exactly "modals" if present
            if (element.hasAttribute('hx-target')) {
                const targetValue = element.getAttribute('hx-target');
                if (targetValue !== 'modals') {
                    throw new Error(`Element with tx-modal="${modalUrl}" has hx-target="${targetValue}", but only "modals" is allowed.`);
                }
            }

            // add hx-get, hx-target, hx-swap attributes
            element.setAttribute('hx-get', modalUrl);
            element.setAttribute('hx-target', '#modals');
            element.setAttribute('hx-swap', 'beforeend');

            element.removeAttribute('tx-modal');
        });
    }
    replaceTxModal();
    document.addEventListener('htmx:afterSwap', replaceTxModal);
}