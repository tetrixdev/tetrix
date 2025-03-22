import Alpine from 'alpinejs';
import anchor from '@alpinejs/anchor'

export function initAlpine() {
    Alpine.plugin(anchor)
    Alpine.start();

    window.Alpine = Alpine;
}