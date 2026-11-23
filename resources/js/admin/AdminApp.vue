<script setup>
import { ref, computed, onMounted, onBeforeUnmount } from 'vue';
import { setBase } from './api';
import Dashboard from './views/Dashboard.vue';
import Requests from './views/Requests.vue';
import Projects from './views/Projects.vue';
import Services from './views/Services.vue';
import Posts from './views/Posts.vue';
import References from './views/References.vue';
import Jobs from './views/Jobs.vue';
import Settings from './views/Settings.vue';

const props = defineProps({ user: Object, base: String, siteUrl: String, logoutUrl: String, logo: String, quoteFileUrl: String, cvUrl: String });
setBase(props.base);

const views = [
    { id: 'tableau-de-bord', label: 'Tableau de bord', icon: 'fa-chart-simple', comp: Dashboard },
    { id: 'demandes', label: 'Demandes', icon: 'fa-inbox', comp: Requests },
    { id: 'realisations', label: 'Réalisations', icon: 'fa-images', comp: Projects },
    { id: 'services', label: 'Services', icon: 'fa-layer-group', comp: Services },
    { id: 'actualites', label: 'Actualités', icon: 'fa-newspaper', comp: Posts },
    { id: 'references', label: 'Références', icon: 'fa-handshake', comp: References },
    { id: 'recrutement', label: 'Offres d\'emploi', icon: 'fa-briefcase', comp: Jobs },
    { id: 'parametres', label: 'Coordonnées & textes', icon: 'fa-gear', comp: Settings },
];
const current = ref(views[0].id);
// Sous-page de la vue (ex. #actualites/nouvelle → « nouvelle »)
const subpath = ref('');
const menuOpen = ref(false);
const pending = ref(0);
const view = computed(() => views.find((v) => v.id === current.value) || views[0]);
const pageTitle = computed(() => {
    if (current.value === 'actualites' && subpath.value === 'nouvelle') return 'Nouvelle actualité';
    if (current.value === 'actualites' && subpath.value.startsWith('modifier/')) return 'Modifier l\'actualité';
    return view.value.label;
});
const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
const today = new Date().toLocaleDateString('fr-FR', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' });

function syncHash() {
    const [h, ...rest] = decodeURIComponent(window.location.hash.replace('#', '')).split('/');
    current.value = views.some((v) => v.id === h) ? h : views[0].id;
    subpath.value = rest.join('/');
    menuOpen.value = false;
    window.scrollTo(0, 0);
}
function go(id) { window.location.hash = id; }
onMounted(() => { syncHash(); window.addEventListener('hashchange', syncHash); });
onBeforeUnmount(() => window.removeEventListener('hashchange', syncHash));
</script>

<template>
    <div class="adm-app">
        <aside class="adm-side" :class="{ open: menuOpen }">
            <a :href="siteUrl" class="d-block mb-4" target="_blank" rel="noopener"><img :src="logo" alt="VOKAMIN" style="height:48px;width:auto"></a>
            <nav class="adm-nav">
                <template v-for="v in views" :key="v.id">
                    <a :href="`#${v.id}`" :class="{ active: current === v.id && !(v.id === 'actualites' && subpath === 'nouvelle') }">
                        <i class="fa-solid" :class="v.icon"></i>{{ v.label }}
                        <span v-if="v.id === 'demandes' && pending" class="pill">{{ pending }}</span>
                    </a>
                    <a v-if="v.id === 'actualites'" href="#actualites/nouvelle" class="adm-nav-sub" :class="{ active: current === 'actualites' && subpath === 'nouvelle' }">
                        <i class="fa-solid fa-pen-to-square"></i>Écrire une actualité
                    </a>
                </template>
            </nav>
            <div class="mt-auto pt-4 small" style="color:var(--vk-on-ink-muted)">
                <div class="d-flex align-items-center gap-2 mb-3"><span class="adm-avatar">{{ (user?.name || 'A').slice(0, 2).toUpperCase() }}</span><span>{{ user?.name }}<br><span style="font-size:.75rem">{{ user?.email }}</span></span></div>
                <form :action="logoutUrl" method="post"><input type="hidden" name="_token" :value="csrf"><button class="btn p-0 small" style="color:var(--vk-on-ink-muted)" type="submit"><i class="fa-solid fa-right-from-bracket me-2"></i>Se déconnecter</button></form>
            </div>
        </aside>
        <main class="adm-main">
            <header class="adm-top">
                <button class="adm-burger" type="button" aria-label="Menu" @click="menuOpen = !menuOpen"><i class="fa-solid fa-bars"></i></button>
                <div><h1 class="h4 mb-0">{{ pageTitle }}</h1><span class="small text-muted-vk text-capitalize">{{ today }}</span></div>
                <div class="ms-auto"><a class="btn-vk sm ghost" :href="siteUrl" target="_blank" rel="noopener"><i class="fa-solid fa-arrow-up-right-from-square"></i> <span class="d-none d-sm-inline">Voir le site</span></a></div>
            </header>
            <section class="adm-view active">
                <component :is="view.comp" :key="view.id" :quote-file-url="quoteFileUrl" :cv-url="cvUrl" :site-url="siteUrl" :subpath="subpath" @pending="pending = $event" @go="go" />
            </section>
        </main>
    </div>
</template>
