import { initAlpine } from './alpineSetup.js';
import { initHtmx } from './htmxSetup.js';
import { setupTxTargets } from './tetrixTargets.js';
import { setupTxModal } from './tetrixModal.js';
import { setupTxDependsOn } from './tetrixDependsOn.js';

initAlpine();
initHtmx();
setupTxTargets();
setupTxModal();
setupTxDependsOn();
setupTxDependsOn();