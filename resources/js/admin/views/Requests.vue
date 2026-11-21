<script setup>
import { ref, reactive, watch, onMounted } from 'vue';
import { get, patch, del, fmtDate } from '../api';
import { toast } from '../../lib/toast';
import Drawer from '../Drawer.vue';
import ConfirmButton from '../ConfirmButton.vue';

const props = defineProps({ quoteFileUrl: String, cvUrl: String });
const tabs = [['quote', 'Devis'], ['contact', 'Messages'], ['application', 'Candidatures'], ['brochure', 'Plaquette'], ['newsletter', 'Newsletter']];
const labels = { construction: 'Construction', conception: 'Conception & plans', promotion: 'Promotion immobilière', renovation: 'Rénovation & décoration', maintenance: 'Building Maintenance', negoce: 'Négoce de matériaux', design: 'Design & enseignes', autre: 'Autre besoin' };
const statuses = { new: ['new', 'Nouvelle'], progress: ['prog', 'En traitement'], done: ['done', 'Traitée'] };
const state = reactive({ type: 'quote', q: '', status: '', page: 1 });
const list = ref(null);
const loading = ref(false);
const selected = ref(null);
let t;

async function load() {
    loading.value = true;
    try { list.value = await get('/requests', { type: state.type, q: state.q, status: state.status, page: state.page }); }
    catch (e) { toast('Chargement impossible.', 'fa-triangle-exclamation'); }
    loading.value = false;
}
watch(() => [state.type, state.status, state.page], load);
watch(() => state.q, () => { clearTimeout(t); t = setTimeout(() => { state.page = 1; load(); }, 300); });
onMounted(load);

async function save(item, changes) {
    const res = await patch(`/requests/${state.type}/${item.id}`, changes);
    if (res.ok) { Object.assign(item, res.data); toast('Demande mise à jour.'); } else toast(res.message || 'Mise à jour impossible.', 'fa-triangle-exclamation');
}
async function remove(item) {
    const res = await del(`/requests/${state.type}/${item.id}`);
    if (res.ok) { selected.value = null; toast('Demande supprimée.'); load(); }
}
const subject = (r) => state.type === 'quote' ? (labels[r.prestation] || r.prestation) : state.type === 'contact' ? r.sujet : r.poste;
const mb = (n) => (n / 1048576).toFixed(1) + ' Mo';
</script>

<template>
    <div class="adm-card">
        <div class="d-flex flex-wrap gap-2 justify-content-between align-items-center mb-3">
            <div class="filters"><button v-for="[k, l] in tabs" :key="k" type="button" :class="{ active: state.type === k }" @click="state.type = k; state.page = 1; state.status = ''">{{ l }}</button></div>
            <div v-if="['quote', 'contact', 'application'].includes(state.type)" class="d-flex gap-2">
                <select v-model="state.status" class="form-select form-select-sm" style="width:auto" aria-label="Statut"><option value="">Tous les statuts</option><option v-for="(v, k) in statuses" :key="k" :value="k">{{ v[1] }}</option></select>
                <input v-model="state.q" class="form-control form-control-sm" style="max-width:240px" placeholder="Nom, email, référence…" aria-label="Rechercher">
            </div>
        </div>

        <div v-if="loading && !list" class="adm-empty"><span class="spinner"></span></div>
        <div v-else-if="list && !list.data.length" class="adm-empty">Aucune donnée pour le moment.</div>
        <div v-else-if="list" class="table-wrap">
            <table v-if="['brochure', 'newsletter'].includes(state.type)" class="table adm-table">
                <thead><tr><th>Date</th><th v-if="state.type === 'brochure'">Nom</th><th>Email</th><th v-if="state.type === 'brochure'">Entreprise</th></tr></thead>
                <tbody><tr v-for="r in list.data" :key="r.id"><td>{{ fmtDate(r.created_at) }}</td><td v-if="state.type === 'brochure'">{{ r.nom }}</td><td><a :href="`mailto:${r.email}`">{{ r.email }}</a></td><td v-if="state.type === 'brochure'">{{ r.entreprise || '—' }}</td></tr></tbody>
            </table>
            <table v-else class="table adm-table">
                <thead><tr><th>Référence</th><th>Date</th><th>Demandeur</th><th>Objet</th><th>Statut</th><th></th></tr></thead>
                <tbody>
                    <tr v-for="r in list.data" :key="r.id" style="cursor:pointer" @click="selected = r">
                        <td class="fw-bold" style="color:var(--vk-blue)">{{ r.reference }}</td><td>{{ fmtDate(r.created_at) }}</td>
                        <td>{{ r.nom }}<br><span class="small text-muted-vk">{{ r.email }}</span></td>
                        <td>{{ subject(r) }}<span v-if="r.localisation" class="text-muted-vk"> · {{ r.localisation }}</span></td>
                        <td><span class="st" :class="statuses[r.status]?.[0]">{{ statuses[r.status]?.[1] }}</span></td>
                        <td class="text-end"><i class="fa-solid fa-chevron-right text-muted-vk"></i></td>
                    </tr>
                </tbody>
            </table>
            <div v-if="list.last_page > 1" class="d-flex justify-content-between align-items-center mt-2 small">
                <button class="btn-vk sm ghost" :disabled="state.page <= 1" @click="state.page--">Précédent</button>
                <span>Page {{ list.current_page }} / {{ list.last_page }} · {{ list.total }} éléments</span>
                <button class="btn-vk sm ghost" :disabled="state.page >= list.last_page" @click="state.page++">Suivant</button>
            </div>
        </div>
    </div>

    <Drawer v-if="selected" :title="selected.reference" @close="selected = null">
        <div class="d-flex flex-wrap gap-2 align-items-center mb-4">
            <label class="small fw-bold me-1" for="req-status">Statut</label>
            <select id="req-status" class="form-select form-select-sm" style="width:auto" :value="selected.status" @change="save(selected, { status: $event.target.value })">
                <option v-for="(v, k) in statuses" :key="k" :value="k">{{ v[1] }}</option>
            </select>
            <a class="btn-vk sm ghost ms-auto" :href="`mailto:${selected.email}?subject=${encodeURIComponent('VOKAMIN — ' + selected.reference)}`"><i class="fa-solid fa-reply"></i> Répondre</a>
            <a v-if="selected.tel" class="btn-vk sm ghost" :href="`tel:${selected.tel.replace(/[^0-9+]/g, '')}`"><i class="fa-solid fa-phone"></i> Appeler</a>
            <a v-if="selected.whatsapp || selected.tel" class="btn-vk sm wa" target="_blank" rel="noopener" :href="`https://wa.me/${(selected.whatsapp || selected.tel).replace(/[^0-9]/g, '')}`"><i class="fa-brands fa-whatsapp"></i></a>
        </div>
        <div class="adm-card"><dl class="adm-detail row mb-0">
            <template v-if="state.type === 'quote'">
                <div class="col-sm-6"><dt>Prestation</dt><dd>{{ labels[selected.prestation] || selected.prestation }}</dd></div>
                <div class="col-sm-6"><dt>Nature du bâtiment</dt><dd>{{ selected.nature }}</dd></div>
                <div class="col-sm-6"><dt>Localisation</dt><dd>{{ selected.localisation }}</dd></div>
                <div class="col-sm-6"><dt>Surface</dt><dd>{{ selected.surface ? selected.surface + ' m²' : '—' }}</dd></div>
                <div class="col-sm-6"><dt>Délai</dt><dd>{{ selected.delai || '—' }}</dd></div>
                <div class="col-sm-6"><dt>Budget</dt><dd>{{ selected.budget || '—' }}</dd></div>
                <div class="col-sm-6"><dt>Entreprise</dt><dd>{{ selected.entreprise || '—' }}</dd></div>
                <div class="col-sm-6"><dt>Rendez-vous</dt><dd>{{ selected.rdv || '—' }}</dd></div>
            </template>
            <div class="col-sm-6"><dt>Nom</dt><dd>{{ selected.nom }}</dd></div>
            <div class="col-sm-6"><dt>Email</dt><dd>{{ selected.email }}</dd></div>
            <div class="col-sm-6"><dt>Téléphone</dt><dd>{{ selected.tel || '—' }}</dd></div>
            <div v-if="state.type === 'quote'" class="col-sm-6"><dt>WhatsApp</dt><dd>{{ selected.whatsapp || '—' }}</dd></div>
            <div v-if="state.type === 'contact'" class="col-sm-6"><dt>Sujet</dt><dd>{{ selected.sujet }}</dd></div>
            <div v-if="state.type === 'application'" class="col-sm-6"><dt>Poste</dt><dd>{{ selected.poste }}</dd></div>
            <div class="col-12"><dt>{{ state.type === 'quote' ? 'Description' : 'Message' }}</dt><dd style="white-space:pre-line;font-weight:400">{{ selected.description || selected.message || '—' }}</dd></div>
        </dl></div>
        <div v-if="state.type === 'quote' && selected.files?.length" class="adm-card mt-3"><h3 class="h6">Pièces jointes</h3>
            <ul class="file-list"><li v-for="f in selected.files" :key="f.id"><span><i class="fa-solid fa-paperclip me-2"></i>{{ f.original_name }}</span><small class="text-muted-vk ms-auto">{{ mb(f.size) }}</small><a :href="`${quoteFileUrl}/${f.id}`" aria-label="Télécharger"><i class="fa-solid fa-download"></i></a></li></ul>
        </div>
        <div v-if="state.type === 'application' && selected.cv_path" class="adm-card mt-3"><a class="btn-vk sm" :href="`${cvUrl}/${selected.id}`"><i class="fa-solid fa-download"></i> Télécharger le CV ({{ selected.cv_name }})</a></div>
        <div v-if="state.type === 'quote'" class="adm-card mt-3 form-vk">
            <label class="form-label" for="req-notes">Notes internes</label>
            <textarea id="req-notes" v-model="selected.notes" class="form-control" placeholder="Suivi, rendez-vous, montant du devis…"></textarea>
            <button class="btn-vk sm mt-3" type="button" @click="save(selected, { notes: selected.notes })">Enregistrer les notes</button>
        </div>
        <div class="mt-4"><ConfirmButton @confirm="remove(selected)" /></div>
    </Drawer>
</template>
