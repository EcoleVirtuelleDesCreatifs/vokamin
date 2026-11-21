<script setup>
import { ref, reactive, computed, onMounted } from 'vue';
import { get, post, patch, del, toFormData } from '../api';
import { toast } from '../../lib/toast';
import Drawer from '../Drawer.vue';
import ConfirmButton from '../ConfirmButton.vue';

const props = defineProps({ siteUrl: String });
const projects = ref([]);
const services = ref([]);
const loading = ref(true);
const editing = ref(null);   // copie en cours d'édition
const saving = ref(false);
const errors = reactive({});
const newImages = ref([]);
const coverFile = ref(null);
const tags = [['construction', 'Construction'], ['promotion', 'Promotion immobilière'], ['renovation', 'Rénovation & décoration'], ['maintenance', 'Building Maintenance'], ['negoce', 'Négoce'], ['design', 'Design & enseignes']];
const categories = computed(() => [...new Set(['Construction', 'Promotion immobilière', 'Rénovation & décoration', 'Building Maintenance', 'Négoce de matériaux', 'Design & enseignes', ...projects.value.map((p) => p.category)])]);
const blank = () => ({ id: null, name: '', slug: '', location: '', category: 'Construction', tags: 'construction', description: '', programme: '', year: '', status: '', service_slugs: [], video_url: '', testimonial: '', testimonial_author: '', ba_before: '', ba_after: '', ba_label_before: '', ba_label_after: '', is_featured: false, is_published: true, images: [], cover: '' });

async function load() {
    loading.value = true;
    [projects.value, services.value] = await Promise.all([get('/projects'), get('/services')]);
    loading.value = false;
}
onMounted(load);

function edit(p) {
    Object.keys(errors).forEach((k) => delete errors[k]);
    editing.value = JSON.parse(JSON.stringify(p ? { ...blank(), ...p } : blank()));
    ['programme', 'year', 'status', 'video_url', 'testimonial', 'testimonial_author', 'ba_before', 'ba_after', 'ba_label_before', 'ba_label_after', 'cover'].forEach((k) => { editing.value[k] ??= ''; });
    editing.value.service_slugs ??= [];
    editing.value.slug_original = p?.slug || null;
    newImages.value = [];
    coverFile.value = null;
}
const hasTag = (t) => (editing.value.tags || '').split(' ').includes(t);
function toggleTag(t) {
    const set = new Set((editing.value.tags || '').split(' ').filter(Boolean));
    set.has(t) ? set.delete(t) : set.add(t);
    editing.value.tags = [...set].join(' ');
}

const fields = ['name', 'slug', 'location', 'category', 'tags', 'description', 'programme', 'year', 'status', 'service_slugs', 'video_url', 'testimonial', 'testimonial_author', 'ba_before', 'ba_after', 'ba_label_before', 'ba_label_after', 'is_featured', 'is_published'];
async function save() {
    saving.value = true;
    Object.keys(errors).forEach((k) => delete errors[k]);
    const e = editing.value;
    const body = Object.fromEntries(fields.map((k) => [k, e[k]]));
    if (e.cover && !coverFile.value) body.cover_from_gallery = e.cover;
    const res = await post(e.id ? `/projects/${e.slug_original || e.slug}` : '/projects', toFormData(body, { images: newImages.value, cover_file: coverFile.value }));
    saving.value = false;
    if (!res.ok) { Object.assign(errors, res.errors); toast(res.message && !Object.keys(res.errors).length ? res.message : 'Vérifiez les champs signalés.', 'fa-triangle-exclamation'); return; }
    toast(e.id ? 'Projet enregistré.' : 'Projet créé.');
    await load();
    edit(projects.value.find((p) => p.id === res.data.id));
}
async function quickToggle(p, key) {
    const body = Object.fromEntries(fields.map((k) => [k, p[k] ?? '']));
    body[key] = !p[key];
    const res = await post(`/projects/${p.slug}`, toFormData(body));
    if (res.ok) { p[key] = !p[key]; toast(key === 'is_published' ? (p[key] ? 'Projet publié.' : 'Projet masqué.') : (p[key] ? 'Mis en avant sur l\'accueil.' : 'Retiré de l\'accueil.')); }
    else toast(res.message || 'Modification impossible.', 'fa-triangle-exclamation');
}
async function caption(img) { await patch(`/project-images/${img.id}`, { caption: img.caption }); toast('Légende enregistrée.'); }
async function removeImage(img) {
    const res = await del(`/project-images/${img.id}`);
    if (res.ok) { editing.value.images = editing.value.images.filter((i) => i.id !== img.id); if (editing.value.cover === img.path) editing.value.cover = ''; toast('Photo supprimée.'); }
}
async function moveImage(i, d) {
    const imgs = editing.value.images; const j = i + d;
    if (j < 0 || j >= imgs.length) return;
    [imgs[i], imgs[j]] = [imgs[j], imgs[i]];
    await Promise.all(imgs.map((img, k) => patch(`/project-images/${img.id}`, { position: k + 1 })));
}
async function destroy() {
    const res = await del(`/projects/${editing.value.slug}`);
    if (res.ok) { editing.value = null; toast('Projet supprimé.'); load(); }
}
const onImages = (e) => { newImages.value = [...newImages.value, ...Array.from(e.target.files)]; e.target.value = ''; };
</script>

<template>
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
        <p class="mb-0 text-muted-vk small">Créez une fiche projet, ajoutez la galerie, choisissez la photo de couverture, publiez ou masquez.</p>
        <button class="btn-vk sm" type="button" @click="edit(null)"><i class="fa-solid fa-plus"></i> Nouveau projet</button>
    </div>
    <div class="adm-card">
        <div v-if="loading" class="adm-empty"><span class="spinner"></span></div>
        <div v-else class="table-wrap"><table class="table adm-table">
            <thead><tr><th></th><th>Projet</th><th>Domaine</th><th>Photos</th><th>Vues</th><th>À l'accueil</th><th>Publié</th><th></th></tr></thead>
            <tbody>
                <tr v-for="p in projects" :key="p.id">
                    <td><img class="thumb" :src="p.cover_url" alt=""></td>
                    <td><strong>{{ p.name }}</strong><br><span class="small text-muted-vk">{{ p.location }}</span></td>
                    <td>{{ p.category }}</td><td>{{ p.images.length }}</td><td>{{ p.views }}</td>
                    <td><div class="form-check form-switch m-0"><input class="form-check-input" type="checkbox" :checked="p.is_featured" aria-label="Mettre en avant" @change="quickToggle(p, 'is_featured')"></div></td>
                    <td><div class="form-check form-switch m-0"><input class="form-check-input" type="checkbox" :checked="p.is_published" aria-label="Publier" @change="quickToggle(p, 'is_published')"></div></td>
                    <td class="text-end text-nowrap">
                        <a class="icon-btn" :href="`${siteUrl.replace(/\/$/, '')}/realisations/${p.slug}`" target="_blank" rel="noopener" aria-label="Voir"><i class="fa-regular fa-eye"></i></a>
                        <button class="icon-btn" type="button" aria-label="Modifier" @click="edit(p)"><i class="fa-solid fa-pen"></i></button>
                    </td>
                </tr>
            </tbody>
        </table></div>
    </div>

    <Drawer v-if="editing" :title="editing.id ? editing.name : 'Nouveau projet'" @close="editing = null">
        <form class="form-vk" novalidate @submit.prevent="save">
            <div class="adm-editor mb-3"><div class="row g-3">
                <div class="col-md-6"><label class="form-label" for="p-name">Nom du projet *</label><input id="p-name" v-model="editing.name" class="form-control" :class="{ 'is-invalid': errors.name }"><p v-if="errors.name" class="field-error">{{ errors.name }}</p></div>
                <div class="col-md-6"><label class="form-label" for="p-loc">Localisation *</label><input id="p-loc" v-model="editing.location" class="form-control" :class="{ 'is-invalid': errors.location }"><p v-if="errors.location" class="field-error">{{ errors.location }}</p></div>
                <div class="col-md-6"><label class="form-label" for="p-cat">Domaine affiché *</label><input id="p-cat" v-model="editing.category" class="form-control" list="p-cats"><datalist id="p-cats"><option v-for="c in categories" :key="c" :value="c" /></datalist></div>
                <div class="col-md-6"><label class="form-label" for="p-slug">Adresse de la page (slug)</label><input id="p-slug" v-model="editing.slug" class="form-control" placeholder="généré automatiquement" :class="{ 'is-invalid': errors.slug }"><p v-if="errors.slug" class="field-error">{{ errors.slug }}</p></div>
                <div class="col-12"><label class="form-label">Filtres du portfolio</label><div class="d-flex flex-wrap gap-3">
                    <div v-for="[t, l] in tags" :key="t" class="form-check"><input :id="`tg-${t}`" class="form-check-input" type="checkbox" :checked="hasTag(t)" @change="toggleTag(t)"><label class="form-check-label small" :for="`tg-${t}`">{{ l }}</label></div>
                </div></div>
                <div class="col-12"><label class="form-label" for="p-desc">Description *</label><textarea id="p-desc" v-model="editing.description" class="form-control" :class="{ 'is-invalid': errors.description }"></textarea><p v-if="errors.description" class="field-error">{{ errors.description }}</p></div>
                <div class="col-md-4"><label class="form-label" for="p-prog">Programme</label><input id="p-prog" v-model="editing.programme" class="form-control" placeholder="Ex. 10 villas haut standing"></div>
                <div class="col-md-4"><label class="form-label" for="p-year">Année</label><input id="p-year" v-model="editing.year" class="form-control" placeholder="Facultatif"></div>
                <div class="col-md-4"><label class="form-label" for="p-status">Statut</label><input id="p-status" v-model="editing.status" class="form-control" list="p-statuses" placeholder="Facultatif"><datalist id="p-statuses"><option value="En cours" /><option value="Livré" /></datalist></div>
                <div class="col-12"><label class="form-label">Services liés</label><div class="d-flex flex-wrap gap-3">
                    <div v-for="s in services" :key="s.slug" class="form-check"><input :id="`ps-${s.slug}`" v-model="editing.service_slugs" class="form-check-input" type="checkbox" :value="s.slug"><label class="form-check-label small" :for="`ps-${s.slug}`">{{ s.short }}</label></div>
                </div></div>
                <div class="col-md-6"><label class="form-label" for="p-video">Lien vidéo (YouTube…)</label><input id="p-video" v-model="editing.video_url" class="form-control" type="url" :class="{ 'is-invalid': errors.video_url }"><p v-if="errors.video_url" class="field-error">{{ errors.video_url }}</p></div>
                <div class="col-md-6 d-flex gap-4 align-items-end">
                    <div class="form-check form-switch"><input id="p-feat" v-model="editing.is_featured" class="form-check-input" type="checkbox"><label class="form-check-label" for="p-feat">À l'accueil</label></div>
                    <div class="form-check form-switch"><input id="p-pub" v-model="editing.is_published" class="form-check-input" type="checkbox"><label class="form-check-label" for="p-pub">Publié</label></div>
                </div>
                <div class="col-md-8"><label class="form-label" for="p-testi">Témoignage client</label><textarea id="p-testi" v-model="editing.testimonial" class="form-control" style="min-height:80px" placeholder="Uniquement un témoignage réel, avec l'accord du client"></textarea></div>
                <div class="col-md-4"><label class="form-label" for="p-testi-a">Auteur</label><input id="p-testi-a" v-model="editing.testimonial_author" class="form-control"></div>
            </div></div>

            <div class="adm-editor mb-3">
                <h3 class="h6 mb-3">Galerie photo</h3>
                <div v-if="editing.images.length" class="adm-thumbs mb-3">
                    <div v-for="(img, i) in editing.images" :key="img.id" class="adm-thumb" :class="{ 'is-cover': editing.cover === img.path }">
                        <img :src="img.url" alt="">
                        <div class="actions">
                            <button type="button" title="Photo de couverture" @click="editing.cover = img.path"><i class="fa-solid fa-star" :style="{ color: editing.cover === img.path ? '#0a7322' : '#999' }"></i></button>
                            <button type="button" title="Déplacer à gauche" @click="moveImage(i, -1)"><i class="fa-solid fa-arrow-left"></i></button>
                            <button type="button" title="Supprimer" @click="removeImage(img)"><i class="fa-solid fa-trash"></i></button>
                        </div>
                        <input v-model="img.caption" placeholder="Légende" @change="caption(img)">
                    </div>
                </div>
                <label class="btn-vk sm ghost"><i class="fa-solid fa-images"></i> Ajouter des photos<input type="file" hidden multiple accept=".jpg,.jpeg,.png,.webp" @change="onImages"></label>
                <span v-if="newImages.length" class="small ms-2">{{ newImages.length }} photo(s) à envoyer à l'enregistrement</span>
                <p v-if="errors.images" class="field-error">{{ errors.images }}</p>
                <p class="small text-muted-vk mt-2 mb-0">Les photos sont redimensionnées automatiquement (1600 px). L'étoile désigne la photo de couverture.</p>
            </div>

            <div v-if="editing.images.length > 1" class="adm-editor mb-3"><h3 class="h6 mb-3">Comparaison avant / après</h3><div class="row g-3">
                <div class="col-md-6"><label class="form-label" for="p-bab">Photo « avant »</label><select id="p-bab" v-model="editing.ba_before" class="form-select"><option value="">Aucune</option><option v-for="img in editing.images" :key="img.id" :value="img.path">{{ img.caption || img.path }}</option></select></div>
                <div class="col-md-6"><label class="form-label" for="p-baa">Photo « après »</label><select id="p-baa" v-model="editing.ba_after" class="form-select"><option value="">Aucune</option><option v-for="img in editing.images" :key="img.id" :value="img.path">{{ img.caption || img.path }}</option></select></div>
                <div class="col-md-6"><input v-model="editing.ba_label_before" class="form-control" placeholder="Libellé avant (ex. Chantier)" aria-label="Libellé avant"></div>
                <div class="col-md-6"><input v-model="editing.ba_label_after" class="form-control" placeholder="Libellé après (ex. Finitions)" aria-label="Libellé après"></div>
            </div></div>

            <div class="d-flex flex-wrap gap-2 justify-content-between">
                <button class="btn-vk" type="submit" :disabled="saving"><span v-if="saving" class="spinner"></span> Enregistrer</button>
                <ConfirmButton v-if="editing.id" label="Supprimer le projet" @confirm="destroy" />
            </div>
        </form>
    </Drawer>
</template>
