<script setup>
import { ref, reactive, computed, onMounted, onBeforeUnmount } from 'vue';
import { get, post, patch, del, toFormData, fmtDate, failMessage } from '../api';
import { toast } from '../../lib/toast';
import ConfirmButton from '../ConfirmButton.vue';
import RichEditor from '../RichEditor.vue';

const props = defineProps({ siteUrl: String });
const posts = ref([]);
const loading = ref(true);
const editing = ref(null);
const image = ref(null);
const imagePreview = ref(null);
const newImages = ref([]);
const errors = reactive({});
const saving = ref(false);
const dirty = ref(false);
const search = ref('');
const cats = ['Projet', 'Chantier', 'Projet livré', 'Conseil BTP', 'Partenariat', 'Événement', "Offre d'emploi", 'Communiqué'];

const load = async () => { loading.value = true; posts.value = await get('/posts'); loading.value = false; };
onMounted(load);

const filtered = computed(() => {
    const q = search.value.trim().toLowerCase();
    return q ? posts.value.filter((p) => `${p.title} ${p.category}`.toLowerCase().includes(q)) : posts.value;
});
const publicUrl = (p) => `${props.siteUrl.replace(/\/$/, '')}/actualites/${p.slug}`;
const titleLength = computed(() => (editing.value?.title || '').length);
const excerptLength = computed(() => (editing.value?.excerpt || '').length);

function edit(p) {
    editing.value = p
        ? { ...p, body: p.body_html ?? p.body, published_at: p.published_at ? p.published_at.slice(0, 10) : '', images: [...(p.images || [])] }
        : { id: null, title: '', category: 'Projet', excerpt: '', body: '', published_at: new Date().toISOString().slice(0, 10), is_published: true, image_url: null, images: [] };
    image.value = null;
    imagePreview.value = null;
    newImages.value = [];
    Object.keys(errors).forEach((k) => delete errors[k]);
    window.scrollTo(0, 0);
    setTimeout(() => { dirty.value = false; }, 0);
}

function close() {
    if (dirty.value && !window.confirm('Des modifications ne sont pas enregistrées. Quitter quand même ?')) return;
    editing.value = null;
    dirty.value = false;
}

function onCover(e) {
    const f = e.target.files[0];
    if (!f) return;
    image.value = f;
    imagePreview.value = URL.createObjectURL(f);
    dirty.value = true;
}

async function save() {
    saving.value = true;
    Object.keys(errors).forEach((k) => delete errors[k]);
    const e = editing.value;
    const res = await post(e.id ? `/posts/${e.slug}` : '/posts', toFormData(
        { title: e.title, category: e.category, excerpt: e.excerpt, body: e.body, published_at: e.published_at, is_published: e.is_published },
        { image_file: image.value, images: newImages.value },
    ));
    saving.value = false;
    if (!res.ok) { Object.assign(errors, res.errors); toast(failMessage(res), 'fa-triangle-exclamation'); return; }
    toast(e.id ? 'Article enregistré.' : 'Article publié.');
    await load();
    // On reste sur la page d'édition, avec les données à jour (galerie, image, adresse)
    edit(res.data);
}

async function destroy() {
    const res = await del(`/posts/${editing.value.slug}`);
    if (res.ok) { dirty.value = false; editing.value = null; toast('Article supprimé.'); load(); }
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
const onImages = (e) => { newImages.value = [...newImages.value, ...Array.from(e.target.files)]; e.target.value = ''; dirty.value = true; };
const dropNew = (i) => { newImages.value.splice(i, 1); };
const preview = (f) => URL.createObjectURL(f);

// Cmd/Ctrl + S pour enregistrer, alerte avant de quitter une page modifiée
function onKey(e) {
    if (editing.value && (e.metaKey || e.ctrlKey) && e.key.toLowerCase() === 's') { e.preventDefault(); if (!saving.value) save(); }
}
function onLeave(e) { if (editing.value && dirty.value) { e.preventDefault(); e.returnValue = ''; } }
onMounted(() => { window.addEventListener('keydown', onKey); window.addEventListener('beforeunload', onLeave); });
onBeforeUnmount(() => { window.removeEventListener('keydown', onKey); window.removeEventListener('beforeunload', onLeave); });
</script>

<template>
    <!-- ===== Liste des actualités ===== -->
    <template v-if="!editing">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
            <input v-model="search" class="form-control form-control-sm" style="max-width:280px" type="search" placeholder="Rechercher un article…" aria-label="Rechercher un article">
            <button class="btn-vk sm" type="button" @click="edit(null)"><i class="fa-solid fa-plus"></i> Nouvelle actualité</button>
        </div>
        <div class="adm-card"><div v-if="loading" class="adm-empty"><span class="spinner"></span></div><div v-else class="table-wrap"><table class="table adm-table">
            <thead><tr><th></th><th>Titre</th><th>Catégorie</th><th>Date</th><th>Statut</th><th></th></tr></thead>
            <tbody><tr v-for="p in filtered" :key="p.id" class="pe-row" @click="edit(p)">
                <td><img class="thumb" :src="p.cover_url" alt=""></td>
                <td><strong>{{ p.title }}</strong><span v-if="p.images?.length" class="small text-muted-vk d-block"><i class="fa-regular fa-images me-1"></i>{{ p.images.length }} photo(s) en galerie</span></td>
                <td>{{ p.category }}</td><td>{{ fmtDate(p.published_at) }}</td>
                <td><span class="st" :class="p.is_published ? 'done' : 'prog'">{{ p.is_published ? 'Publié' : 'Brouillon' }}</span></td>
                <td class="text-end text-nowrap" @click.stop><a class="icon-btn" :href="publicUrl(p)" target="_blank" rel="noopener" aria-label="Voir"><i class="fa-regular fa-eye"></i></a><button class="icon-btn" type="button" aria-label="Modifier" @click="edit(p)"><i class="fa-solid fa-pen"></i></button></td>
            </tr><tr v-if="!filtered.length"><td colspan="6" class="adm-empty">Aucune actualité.</td></tr></tbody>
        </table></div></div>
    </template>

    <!-- ===== Page d'édition ===== -->
    <form v-else class="pe form-vk" novalidate @submit.prevent="save" @input="dirty = true">
        <div class="pe-bar">
            <button class="btn-vk sm ghost" type="button" @click="close"><i class="fa-solid fa-arrow-left"></i> Actualités</button>
            <span class="pe-state small"><span class="st" :class="editing.is_published ? 'done' : 'prog'">{{ editing.is_published ? 'Publié' : 'Brouillon' }}</span><span v-if="dirty" class="text-muted-vk ms-2">Modifications non enregistrées</span></span>
            <div class="ms-auto d-flex gap-2">
                <a v-if="editing.id" class="btn-vk sm ghost" :href="publicUrl(editing)" target="_blank" rel="noopener"><i class="fa-regular fa-eye"></i> <span class="d-none d-md-inline">Voir l'article</span></a>
                <button class="btn-vk sm" type="submit" :disabled="saving"><span v-if="saving" class="spinner"></span><i v-else class="fa-solid fa-check"></i> {{ editing.id ? 'Enregistrer' : 'Publier' }}</button>
            </div>
        </div>

        <div class="pe-grid">
            <div class="pe-main">
                <textarea v-model="editing.title" class="pe-title" rows="1" placeholder="Titre de l'article" aria-label="Titre" :class="{ 'is-invalid': errors.title }" @input="$event.target.style.height = 'auto'; $event.target.style.height = $event.target.scrollHeight + 'px'"></textarea>
                <p class="pe-hint" :class="{ warn: titleLength > 65 }">{{ titleLength }} caractères<span v-if="titleLength > 65"> · au-delà de 65, Google coupe le titre</span></p>
                <p v-if="errors.title" class="field-error">{{ errors.title }}</p>

                <label class="form-label mt-3" for="pe-exc">Chapô (résumé affiché sous le titre et dans Google)</label>
                <textarea id="pe-exc" v-model="editing.excerpt" class="form-control" rows="2" :class="{ 'is-invalid': errors.excerpt }" placeholder="Deux phrases qui donnent envie de lire, sans répéter le titre."></textarea>
                <p class="pe-hint" :class="{ warn: excerptLength > 155 || (excerptLength > 0 && excerptLength < 70) }">{{ excerptLength }} / 155 caractères conseillés</p>
                <p v-if="errors.excerpt" class="field-error">{{ errors.excerpt }}</p>

                <label class="form-label mt-3">Contenu</label>
                <RichEditor v-model="editing.body" :invalid="!!errors.body" @update:model-value="dirty = true" />
                <p v-if="errors.body" class="field-error">{{ errors.body }}</p>
            </div>

            <aside class="pe-side">
                <div class="adm-editor">
                    <h3 class="pe-h">Publication</h3>
                    <div class="form-check form-switch mb-3"><input id="pe-pub" v-model="editing.is_published" class="form-check-input" type="checkbox"><label class="form-check-label" for="pe-pub">Visible sur le site</label></div>
                    <label class="form-label" for="pe-date">Date de publication</label>
                    <input id="pe-date" v-model="editing.published_at" class="form-control" type="date">
                    <label class="form-label mt-3" for="pe-cat">Catégorie</label>
                    <input id="pe-cat" v-model="editing.category" class="form-control" list="pe-cats" :class="{ 'is-invalid': errors.category }"><datalist id="pe-cats"><option v-for="c in cats" :key="c" :value="c" /></datalist>
                </div>

                <div class="adm-editor">
                    <h3 class="pe-h">Image principale</h3>
                    <label class="pe-cover">
                        <img v-if="imagePreview || editing.image_url" :src="imagePreview || editing.image_url" alt="">
                        <span v-else class="pe-cover-empty"><i class="fa-regular fa-image"></i>Choisir une image</span>
                        <input type="file" hidden accept=".jpg,.jpeg,.png,.webp" @change="onCover">
                    </label>
                    <p class="pe-hint mb-0">En haut de l'article et sur les vignettes. Sans image, la première photo de la galerie est utilisée.</p>
                    <p v-if="errors.image_file" class="field-error">{{ errors.image_file }}</p>
                </div>

                <div class="adm-editor">
                    <h3 class="pe-h">Galerie « En images »</h3>
                    <div v-if="editing.images.length || newImages.length" class="adm-thumbs pe-thumbs mb-3">
                        <div v-for="(img, i) in editing.images" :key="img.id" class="adm-thumb">
                            <img :src="img.url" alt="">
                            <div class="actions">
                                <button type="button" title="Déplacer à gauche" @click="moveImage(i, -1)"><i class="fa-solid fa-arrow-left"></i></button>
                                <button type="button" title="Supprimer" @click="removeImage(img)"><i class="fa-solid fa-trash"></i></button>
                            </div>
                            <input v-model="img.caption" placeholder="Légende" @change="caption(img)">
                        </div>
                        <div v-for="(f, i) in newImages" :key="'n' + i" class="adm-thumb is-new">
                            <img :src="preview(f)" alt="">
                            <div class="actions"><button type="button" title="Retirer" @click="dropNew(i)"><i class="fa-solid fa-xmark"></i></button></div>
                            <span class="pe-new">À envoyer</span>
                        </div>
                    </div>
                    <label class="btn-vk sm ghost w-100"><i class="fa-solid fa-images"></i> Ajouter des photos<input type="file" hidden multiple accept=".jpg,.jpeg,.png,.webp" @change="onImages"></label>
                    <p v-if="errors.images" class="field-error">{{ errors.images }}</p>
                    <p class="pe-hint mb-0 mt-2">Affichées en galerie sous l'article, avec agrandissement au clic. Pour placer une photo au milieu du texte, utilisez le bouton « Images » de l'éditeur.</p>
                </div>

                <div v-if="editing.id" class="pe-danger"><ConfirmButton @confirm="destroy" /></div>
            </aside>
        </div>
    </form>
</template>
