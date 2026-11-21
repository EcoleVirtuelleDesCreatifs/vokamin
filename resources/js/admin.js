import { createApp } from 'vue';
import AdminApp from './admin/AdminApp.vue';
import './lib/toast';

const el = document.getElementById('admin-app');
if (el) {
    let props = {};
    try { props = JSON.parse(el.dataset.props || '{}'); } catch (e) { /* props invalides */ }
    createApp(AdminApp, props).mount(el);
}
