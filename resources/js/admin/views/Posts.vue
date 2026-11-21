<script setup>
import { ref, reactive, onMounted } from 'vue';
import { get, post, del, toFormData, fmtDate } from '../api';
import { toast } from '../../lib/toast';
import Drawer from '../Drawer.vue';
import ConfirmButton from '../ConfirmButton.vue';

const props = defineProps({ siteUrl: String });
const posts = ref([]);
const editing = ref(null);
const image = ref(null);
const errors = reactive({});
const saving = ref(false);
const cats = ['Projet', 'Chantier', 'Projet livré', 'Conseil BTP', 'Partenariat', 'Événement', "Offre d'emploi", 'Communiqué'];

const load = async () => { posts.value = await get('/posts'); };
onMounted(load);
function edit(p) {
    editing.value = p ? { ...p, published_at: p.published_at ? p.published_at.slice(0, 10) : '' } : { id: null, title: '', category: 'Projet', excerpt: '', body: '', published_at: new Date().toISOString().slice(0, 10), is_published: true, image_url: null };
    image.value = null;
    Object.keys(errors).forEach((k) => delete errors[k]);
}
async function save() {
    saving.value = true;
    const e = editing.value;
    const res = await post(e.id ? `/posts/${e.slug}` : '/posts', toFormData({ title: e.title, category: e.category, excerpt: e.excerpt, body: e.body, published_at: e.published_at, is_published: e.is_published }, { image_file: image.value }));
    saving.value = false;
    if (!res.ok) { Object.assign(errors, res.errors); toast('Vérifiez les champs signalés.', 'fa-triangle-exclamation'); return; }
    toast(e.id ? 'Article enregistré.' : 'Article publié.');
    editing.value = null;
    load();
}
async function destroy() {
    const res = await del(`/posts/${editing.value.slug}`);
    if (res.ok) { editing.value = null; toast('Article supprimé.'); load(); }
}
</script>

<template>
    <div class="d-flex justify-content-end mb-3"><button class="btn-vk sm" type="button" @click="edit(null)"><i class="fa-solid fa-plus"></i> Nouvelle actualité</button></div>
    <div class="adm-card"><div class="table-wrap"><table class="table adm-table">
        <thead><tr><th></th><th>Titre</th><th>Catégorie</th><th>Date</th><th>Statut</th><th></th></tr></thead>
        <tbody><tr v-for="p in posts" :key="p.id">
            <td><img class="thumb" :src="p.image_url" alt=""></td><td><strong>{{ p.title }}</strong></td><td>{{ p.category }}</td><td>{{ fmtDate(p.published_at) }}</td>
            <td><span class="st" :class="p.is_published ? 'done' : 'prog'">{{ p.is_published ? 'Publié' : 'Brouillon' }}</span></td>
            <td class="text-end text-nowrap"><a class="icon-btn" :href="`${siteUrl.replace(/\/$/, '')}/actualites/${p.slug}`" target="_blank" rel="noopener" aria-label="Voir"><i class="fa-regular fa-eye"></i></a><button class="icon-btn" type="button" aria-label="Modifier" @click="edit(p)"><i class="fa-solid fa-pen"></i></button></td>
        </tr><tr v-if="!posts.length"><td colspan="6" class="adm-empty">Aucune actualité.</td></tr></tbody>
    </table></div></div>

    <Drawer v-if="editing" :title="editing.id ? 'Modifier l\'actualité' : 'Nouvelle actualité'" @close="editing = null">
        <form class="form-vk adm-editor" novalidate @submit.prevent="save"><div class="row g-3">
            <div class="col-12"><label class="form-label" for="n-title">Titre *</label><input id="n-title" v-model="editing.title" class="form-control" :class="{ 'is-invalid': errors.title }"><p v-if="errors.title" class="field-error">{{ errors.title }}</p></div>
            <div class="col-md-4"><label class="form-label" for="n-cat">Catégorie *</label><input id="n-cat" v-model="editing.category" class="form-control" list="n-cats"><datalist id="n-cats"><option v-for="c in cats" :key="c" :value="c" /></datalist></div>
            <div class="col-md-4"><label class="form-label" for="n-date">Date de publication</label><input id="n-date" v-model="editing.published_at" class="form-control" type="date"></div>
            <div class="col-md-4 d-flex align-items-end"><div class="form-check form-switch"><input id="n-pub" v-model="editing.is_published" class="form-check-input" type="checkbox"><label class="form-check-label" for="n-pub">Publié</label></div></div>
            <div class="col-12"><label class="form-label" for="n-exc">Résumé *</label><textarea id="n-exc" v-model="editing.excerpt" class="form-control" style="min-height:70px" :class="{ 'is-invalid': errors.excerpt }"></textarea><p v-if="errors.excerpt" class="field-error">{{ errors.excerpt }}</p></div>
            <div class="col-12"><label class="form-label" for="n-body">Contenu *</label><textarea id="n-body" v-model="editing.body" class="form-control" style="min-height:300px" :class="{ 'is-invalid': errors.body }"></textarea>
                <p class="small text-muted-vk mt-1 mb-0">Séparez les paragraphes par une ligne vide. « ## Titre » crée un intertitre, « &gt; Texte » une citation, « - élément » une liste.</p><p v-if="errors.body" class="field-error">{{ errors.body }}</p></div>
            <div class="col-md-6"><label class="form-label" for="n-img">Image</label><img v-if="editing.image_url" :src="editing.image_url" alt="" class="w-100 mb-2" style="aspect-ratio:3/2;object-fit:cover"><input id="n-img" class="form-control form-control-sm" type="file" accept=".jpg,.jpeg,.png,.webp" @change="image = $event.target.files[0]"></div>
        </div>
        <div class="d-flex flex-wrap gap-2 justify-content-between mt-4"><button class="btn-vk" type="submit" :disabled="saving"><span v-if="saving" class="spinner"></span> Enregistrer</button><ConfirmButton v-if="editing.id" @confirm="destroy" /></div>
        </form>
    </Drawer>
</template>
