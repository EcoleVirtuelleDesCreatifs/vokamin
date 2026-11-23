<script setup>
import { ref, reactive, onMounted } from 'vue';
import { get, post, patch, del, toFormData, fmtDate, failMessage } from '../api';
import { toast } from '../../lib/toast';
import Drawer from '../Drawer.vue';
import ConfirmButton from '../ConfirmButton.vue';

const props = defineProps({ siteUrl: String });
const posts = ref([]);
const editing = ref(null);
const image = ref(null);
const newImages = ref([]);
const errors = reactive({});
const saving = ref(false);
const cats = ['Projet', 'Chantier', 'Projet livré', 'Conseil BTP', 'Partenariat', 'Événement', "Offre d'emploi", 'Communiqué'];

const load = async () => { posts.value = await get('/posts'); };
onMounted(load);
function edit(p) {
    editing.value = p ? { ...p, published_at: p.published_at ? p.published_at.slice(0, 10) : '' } : { id: null, title: '', category: 'Projet', excerpt: '', body: '', published_at: new Date().toISOString().slice(0, 10), is_published: true, image_url: null, images: [] };
    if (!editing.value.images) editing.value.images = [];
    image.value = null;
    newImages.value = [];
    Object.keys(errors).forEach((k) => delete errors[k]);
}
async function save() {
    saving.value = true;
    const e = editing.value;
    const res = await post(e.id ? `/posts/${e.slug}` : '/posts', toFormData({ title: e.title, category: e.category, excerpt: e.excerpt, body: e.body, published_at: e.published_at, is_published: e.is_published }, { image_file: image.value, images: newImages.value }));
    saving.value = false;
    if (!res.ok) { Object.assign(errors, res.errors); toast(failMessage(res), 'fa-triangle-exclamation'); return; }
    toast(e.id ? 'Article enregistré.' : 'Article publié.');
    editing.value = null;
    load();
}
async function caption(img) { await patch(`/post-images/${img.id}`, { caption: img.caption }); toast('Légende enregistrée.'); }
async function removeImage(img) {
    const res = await del(`/post-images/${img.id}`);
    if (res.ok) { editing.value.images = editing.value.images.filter((i) => i.id !== img.id); toast('Photo supprimée.'); }
}
async function moveImage(i, d) {
    const imgs = editing.value.images; const j = i + d;
    if (j < 0 || j >= imgs.length) return;
    [imgs[i], imgs[j]] = [imgs[j], imgs[i]];
    await Promise.all(imgs.map((img, k) => patch(`/post-images/${img.id}`, { position: k + 1 })));
}
const onImages = (e) => { newImages.value = [...newImages.value, ...Array.from(e.target.files)]; e.target.value = ''; };
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
            <td><img class="thumb" :src="p.cover_url" alt=""></td><td><strong>{{ p.title }}</strong></td><td>{{ p.category }}<span v-if="p.images.length" class="small text-muted-vk d-block">{{ p.images.length }} photo(s)</span></td><td>{{ fmtDate(p.published_at) }}</td>
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
            <div class="col-md-6"><label class="form-label" for="n-img">Image</label><img v-if="editing.image_url" :src="editing.image_url" alt="" class="w-100 mb-2" style="aspect-ratio:3/2;object-fit:cover"><input id="n-img" class="form-control form-control-sm" type="file" accept=".jpg,.jpeg,.png,.webp" @change="image = $event.target.files[0]"><p v-if="errors.image_file" class="field-error">{{ errors.image_file }}</p></div>
            <div class="col-12">
                <label class="form-label">Galerie photo</label>
                <div v-if="editing.images.length" class="adm-thumbs mb-3">
                    <div v-for="(img, i) in editing.images" :key="img.id" class="adm-thumb">
                        <img :src="img.url" alt="">
                        <div class="actions">
                            <button type="button" title="Déplacer à gauche" @click="moveImage(i, -1)"><i class="fa-solid fa-arrow-left"></i></button>
                            <button type="button" title="Supprimer" @click="removeImage(img)"><i class="fa-solid fa-trash"></i></button>
                        </div>
                        <input v-model="img.caption" placeholder="Légende" @change="caption(img)">
                    </div>
                </div>
                <label class="btn-vk sm ghost"><i class="fa-solid fa-images"></i> Ajouter des photos<input type="file" hidden multiple accept=".jpg,.jpeg,.png,.webp" @change="onImages"></label>
                <span v-if="newImages.length" class="small ms-2">{{ newImages.length }} photo(s) à envoyer à l'enregistrement</span>
                <p v-if="errors.images" class="field-error">{{ errors.images }}</p>
                <p class="small text-muted-vk mt-2 mb-0">Plusieurs photos du même événement : elles s'affichent en galerie sous l'article (cliquez pour agrandir). Sélectionnez-en plusieurs d'un coup avec Cmd ou Maj.</p>
            </div>
        </div>
        <div class="d-flex flex-wrap gap-2 justify-content-between mt-4"><button class="btn-vk" type="submit" :disabled="saving"><span v-if="saving" class="spinner"></span> Enregistrer</button><ConfirmButton v-if="editing.id" @confirm="destroy" /></div>
        </form>
    </Drawer>
</template>
