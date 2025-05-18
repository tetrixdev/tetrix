export function setupTxDependsOn() {
    /**
     * Finds all elements that depend on any of the given input IDs.
     */
    function findDependents(inputIds) {
        const dependents = [];
        const inputIdSet = new Set(inputIds);

        document.querySelectorAll('[tx-depends-on]').forEach((el) => {
            const deps = el.getAttribute('tx-depends-on')
                .split(',')
                .map(s => s.trim());

            if (!el.id) {
                console.error(`[tx-depends-on] Element is missing an 'id' and will be ignored.`, el);
                return;
            }

            if (deps.some(dep => inputIdSet.has(dep))) {
                dependents.push({ el, dependsOn: deps });
            }
        });

        return dependents;
    }

    /**
     * Collects valid input elements for the given input IDs.
     */
    function collectInputs(inputIds) {
        const inputs = [];
        inputIds.forEach(id => {
            const input = document.getElementById(id);
            if (!input) {
                console.error(`[tx-depends-on] Input with id '${id}' not found in DOM.`);
                return;
            }
            if (!input.name) {
                console.error(`[tx-depends-on] Input with id '${id}' has no 'name' attribute and will be ignored.`);
                return;
            }
            if (!document.contains(input)) {
                console.error(`[tx-depends-on] Input with id '${id}' is not attached to the DOM.`);
                return;
            }
            inputs.push(input);
        });
        return inputs;
    }

    /**
     * Resolves the hx-get URL, using the modal referer fallback if needed.
     */
    function extractHxGet(el) {
        return el.getAttribute('hx-get') || findModalReferer(el);
    }

    function findModalReferer(el) {
        let current = el.parentElement;
        while (current) {
            const headersAttr = current.getAttribute('hx-headers');
            if (headersAttr) {
                try {
                    const headers = JSON.parse(headersAttr);
                    if (headers['TX-Referer']) {
                        return headers['TX-Referer'];
                    }
                } catch (e) {
                    console.error('Invalid JSON in hx-headers of ancestor:', e);
                }
            }
            current = current.parentElement;
        }
        return '';
    }

    /**
     * Extracts all input-like element IDs inside a given DOM element.
     */
    function extractInputIdsFromElement(el) {
        const inputs = el.querySelectorAll('input, select, textarea');
        return Array.from(inputs)
            .filter(input => input.id)
            .map(input => input.id);
    }

    /**
     * Core function: evaluates dependents of inputIds and processes in batch.
     */
    function runDependencyCycle(inputIds) {
        const dependents = findDependents(inputIds);
        if (dependents.length === 0) return;

        const allTargetIds = [];
        const allInputIds = new Set();
        const hxGets = new Set();

        dependents.forEach(({ el, dependsOn }) => {
            allTargetIds.push(el.id);
            dependsOn.forEach(dep => allInputIds.add(dep));

            const hxGet = extractHxGet(el);
            if (hxGet) hxGets.add(hxGet);
        });

        if (hxGets.size > 1) {
            throw new Error(
                `tx-depends-on elements must all share the same hx-get URL. Found: ${Array.from(hxGets).join(', ')}`
            );
        }

        const finalHxGet = Array.from(hxGets)[0] || '';
        const inputElements = collectInputs(Array.from(allInputIds));

        const values = {};
        inputElements.forEach(input => {
            if ((input.type === 'checkbox' || input.type === 'radio') && !input.checked) return;
            values[input.name] = input.value;
        });

        const targetSelector = allTargetIds.map(id => `#${id}`).join(', ');
        const headers = { 'TX-Targets': allTargetIds.join(',') };

        htmx.ajax('GET', finalHxGet, {
            target: targetSelector,
            headers,
            values,
            swap: 'outerHTML',
        });

        // After HTMX completes the DOM update, re-evaluate based on new inputs
        document.addEventListener('htmx:afterSettle', function handleSettle() {
            document.removeEventListener('htmx:afterSettle', handleSettle);

            const nextInputIds = new Set();
            allTargetIds.forEach(targetId => {
                const el = document.getElementById(targetId);
                if (el) {
                    extractInputIdsFromElement(el).forEach(id => nextInputIds.add(id));
                }
            });

            if (nextInputIds.size > 0) {
                runDependencyCycle(Array.from(nextInputIds));
            }
        });
    }

    // Trigger bubbling logic on input change
    document.addEventListener('change', (event) => {
        const inputId = event?.target?.id;
        if (!inputId) return;
        runDependencyCycle([inputId]);
    });
}
