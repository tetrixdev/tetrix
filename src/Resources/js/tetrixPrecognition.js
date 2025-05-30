import { findTxRefererInAncestors } from './tetrixUtilityFunctions.js';

export function TxPrecognitionRequest(inputIds) {
    const forms = new Set();

    inputIds.forEach(id => {
        const input = document.getElementById(id);
        if (!input) return;

        const form = input.closest('form');
        if (form && form.hasAttribute('hx-post')) {
            forms.add(form);
        }
    });

    forms.forEach(form => {
        const headers = {
            Precognition: 'true'
        };

        const referer = findTxRefererInAncestors(form);
        if (referer) headers['TX-Referer'] = referer;

        const formId = form.id;
        if (formId) {
            headers['TX-Targets'] = formId;
        }

        const previousHxHeaders = form.getAttribute('hx-headers');
        form.setAttribute('hx-headers', JSON.stringify(headers));

        // 🧠 Use requestSubmit to trigger HTMX submit, not native .submit()
        form.requestSubmit();

        // 🧼 Cleanup immediately after to avoid polluting future submits
        if (previousHxHeaders) {
            form.setAttribute('hx-headers', previousHxHeaders);
        } else {
            form.removeAttribute('hx-headers');
        }
    });
}
