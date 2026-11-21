import * as bootstrap from 'bootstrap';
import { createApp } from 'vue';
import { initSite } from './site';
import { getConsent, loadAnalytics } from './lib/consent';
import './lib/toast';

window.bootstrap = bootstrap;

/*
 * Îlots Vue : chaque élément [data-vue="Nom"] du HTML rendu par Laravel
 * devient un composant Vue.
 */
const modules = import.meta.glob('./components/*.vue', { eager: true });
const components = Object.fromEntries(
    Object.entries(modules).map(([path, mod]) => [path.split('/').pop().replace('.vue', ''), mod.default]),
);

function mountIslands() {
    document.querySelectorAll('[data-vue]').forEach((el) => {
        const component = components[el.dataset.vue];
        if (!component) return;
        let props = {};
        try { props = JSON.parse(el.dataset.props || '{}'); } catch (e) { /* props invalides */ }
        createApp(component, props).mount(el);
    });
}

document.addEventListener('DOMContentLoaded', () => {
    initSite();
    mountIslands();
    if (getConsent()?.value === 'accept') loadAnalytics();
});
