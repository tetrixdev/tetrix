import { findTxRefererInAncestors } from './tetrixUtilityFunctions.js';
import { TxPrecognitionRequest } from './tetrixPrecognition.js';

export function setupTxDependsOn() {
    // We need to track which input IDs are affected by all cycles of dependency updates.
    // This is used for the final precognition request(s).
    const affectedInputIds = new Set();

    /**
     * Finds all elements that depend on any of the given input IDs.
     */
    function findDependents(inputIds) {
        const inputIdSet = new Set(inputIds);
        const dependents = [];

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
     * Collects input-like elements by ID and validates them.
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
     * Extracts hx-get or falls back to modal-level referer header.
     */
    function extractHxGet(el) {
        // Technically we're not expecting hx-get to be present on the element itself.
        // Leave this here for future-proofing. Might remove it or might expand it to other hx-* attributes.

        // TODO: Make this work outside of MODAL? maybe abstract the logic from tetrixTargets and here
        return el.getAttribute('hx-get') || findTxRefererInAncestors(el);
    }

    /**
     * Extracts all input-like element IDs inside a container.
     */
    function extractInputIdsFromElement(el) {
        const inputs = el.querySelectorAll('input, select, textarea');
        return Array.from(inputs)
            .filter(input => input.id)
            .map(input => input.id);
    }

    /**
     * Runs a full dependency update cycle and tracks what changed.
     */
    function runDependencyCycle(inputIds) {
        // Track input IDs globally for post-update precognition
        inputIds.forEach(id => affectedInputIds.add(id));

        const dependents = findDependents(inputIds);

        if (dependents.length === 0) {
            TxPrecognitionRequest(affectedInputIds);
            affectedInputIds.clear();
            return;
        }

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

        // --------------------------------------
        // STEP 1: Collect input elements based on dependency declarations
        // --------------------------------------
        const inputElements = collectInputs(Array.from(allInputIds));

        // --------------------------------------
        // STEP 2: Collect input elements from within targets (to ensure we include their current state)
        //         Also include the target itself if it is an input/select/textarea
        // --------------------------------------
        allTargetIds.forEach(id => {
            const el = document.getElementById(id);
            if (!el) return;

            // ✅ If the target itself is an input-like element, include it
            if (
                (el.tagName === 'INPUT' || el.tagName === 'SELECT' || el.tagName === 'TEXTAREA') &&
                el.name &&
                document.contains(el)
            ) {
                inputElements.push(el);
            }

            // ✅ Also include any input-like children of the element
            const embeddedInputs = el.querySelectorAll('input, select, textarea');
            embeddedInputs.forEach(input => {
                if (input.name && document.contains(input)) {
                    inputElements.push(input);
                }
            });
        });

        // --------------------------------------
        // STEP 3: Extract name/value pairs from all collected inputs (deduplicated by name)
        // --------------------------------------
        const values = {};
        const seenNames = new Set();

        inputElements.forEach(input => {
            if (!input.name || seenNames.has(input.name)) return;
            if ((input.type === 'checkbox' || input.type === 'radio') && !input.checked) return;

            seenNames.add(input.name);
            values[input.name] = input.value;
        });

        // --------------------------------------
        // STEP 4: Fire HTMX request to update dependent elements
        // --------------------------------------
        const targetSelector = allTargetIds.map(id => `#${id}`).join(', ');
        const headers = { 'TX-Targets': allTargetIds.join(',') };

        htmx.ajax('GET', finalHxGet, {
            target: targetSelector,
            headers,
            values,
            swap: 'outerHTML',
        });

        // --------------------------------------
        // STEP 5: After HTMX finishes updating the DOM, see if the newly updated elements contain more inputs
        //         If so, re-run this logic. Otherwise, fire final precognition.
        // --------------------------------------
        document.addEventListener('htmx:afterSettle', function handleSettle() {
            document.removeEventListener('htmx:afterSettle', handleSettle);

            const nextInputIds = new Set();
            allTargetIds.forEach(targetId => {
                const el = document.getElementById(targetId);
                if (el) {
                    extractInputIdsFromElement(el).forEach(id => {
                        nextInputIds.add(id);
                        affectedInputIds.add(id);
                    });
                }
            });

            if (nextInputIds.size > 0) {
                runDependencyCycle(Array.from(nextInputIds));
            } else {
                TxPrecognitionRequest(affectedInputIds);
                affectedInputIds.clear();
            }
        });
    }

    /**
     * Initial entry point: listens globally for input changes.
     */
    document.addEventListener('change', (event) => {
        const inputId = event?.target?.id;
        if (!inputId) return;
        runDependencyCycle([inputId]);
    });
}
