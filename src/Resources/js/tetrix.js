import { initAlpine } from './alpineSetup.js';
import { initHtmx } from './htmxSetup.js';
import { setupTxTargets } from './tetrixTargets.js';
import { setupTxModal } from './tetrixModal.js';

initAlpine();
initHtmx();
setupTxTargets();
setupTxModal();