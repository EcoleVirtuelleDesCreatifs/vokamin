<script setup>
import { ref, reactive, onMounted } from 'vue';
import { get, post, del, toFormData } from '../api';
import { toast } from '../../lib/toast';
import Drawer from '../Drawer.vue';
import ConfirmButton from '../ConfirmButton.vue';

const refs = ref([]);
const projects = ref([]);
const editing = ref(null);
const logo = ref(null);
const errors = reactive({});
const load = async () => { [refs.value, projects.value] = await Promise.all([get('/references'), get('/projects')]); };
onMounted(load);
function edit(r) {
    editing.value = r ? { ...r } : { id: null, name: '', sector: '', prestation: '', project_id: '', logo_authorized: false, is_published: true, logo_url: null };
    logo.value = null;
    Object.keys(errors).forEach((k) => delete errors[k]);
}
async function save() {
    const e = editing.value;
    const res = await post(e.id ? `/references/${e.id}` : '/references', toFormData({ name: e.name, sector: e.sector, prestation: e.prestation, project_id: e.project_id || '', logo_authorized: e.logo_authorized, is_published: e.is_published }, { logo_file: logo.value }));
    if (!res.ok) { Object.assign(errors, res.errors); toast('Vérifiez les champs signalés.', 'fa-triangle-exclamation'); return; }
    toast('Référence enregistrée.'); editing.value = null; load();
}
async function destroy() { const res = await del(`/references/${editing.value.id}`); if (res.ok) { editing.value = null; toast('Référence supprimée.'); load(); } }
</script>

<template>
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2"><p class="mb-0 small text-muted-vk">Le logo d'un client ne s'affiche que si son autorisation est cochée.</p><button class="btn-vk sm" type="button" @click="edit(null)"><i class="fa-solid fa-plus"></i> Nouvelle référence</button></div>
    <div class="adm-card"><div class="table-wrap"><table class="table adm-table">
        <thead><tr><th>Client</th><th>Secteur</th><th>Logo autorisé</th><th>Affiché</th><th></th></tr></thead>
        <tbody><tr v-for="r in refs" :key="r.id"><td><strong>{{ r.name }}</strong><br><span class="small text-muted-vk">{{ r.prestation }}</span></td><td>{{ r.sector }}</td>
            <td><span class="st" :class="r.logo_authorized ? 'done' : 'prog'">{{ r.logo_authorized ? 'Oui' : 'Non' }}</span></td><td><span class="st" :class="r.is_published ? 'done' : 'prog'">{{ r.is_published ? 'Oui' : 'Non' }}</span></td>
            <td class="text-end"><button class="icon-btn" type="button" aria-label="Modifier" @click="edit(r)"><i class="fa-solid fa-pen"></i></button></td></tr></tbody>
    </table></div></div>
    <Drawer v-if="editing" :title="editing.id ? editing.name : 'Nouvelle référence'" @close="editing = null">
        <form class="form-vk adm-editor" novalidate @submit.prevent="save"><div class="row g-3">
            <div class="col-md-6"><label class="form-label" for="r-name">Client *</label><input id="r-name" v-model="editing.name" class="form-control" :class="{ 'is-invalid': errors.name }"><p v-if="errors.name" class="field-error">{{ errors.name }}</p></div>
            <div class="col-md-6"><label class="form-label" for="r-sector">Secteur *</label><input id="r-sector" v-model="editing.sector" class="form-control" :class="{ 'is-invalid': errors.sector }"><p v-if="errors.sector" class="field-error">{{ errors.sector }}</p></div>
            <div class="col-12"><label class="form-label" for="r-presta">Prestation réalisée *</label><input id="r-presta" v-model="editing.prestation" class="form-control" :class="{ 'is-invalid': errors.prestation }"><p v-if="errors.prestation" class="field-error">{{ errors.prestation }}</p></div>
            <div class="col-md-6"><label class="form-label" for="r-proj">Projet associé</label><select id="r-proj" v-model="editing.project_id" class="form-select"><option value="">Aucun</option><option v-for="p in projects" :key="p.id" :value="p.id">{{ p.name }}</option></select></div>
            <div class="col-md-6"><label class="form-label" for="r-logo">Logo</label><img v-if="editing.logo_url" :src="editing.logo_url" alt="" style="max-height:48px" class="d-block mb-2"><input id="r-logo" class="form-control form-control-sm" type="file" accept=".jpg,.jpeg,.png,.webp" @change="logo = $event.target.files[0]"></div>
            <div class="col-12 d-flex gap-4"><div class="form-check form-switch"><input id="r-auth" v-model="editing.logo_authorized" class="form-check-input" type="checkbox"><label class="form-check-label" for="r-auth">Logo autorisé par le client</label></div><div class="form-check form-switch"><input id="r-pub" v-model="editing.is_published" class="form-check-input" type="checkbox"><label class="form-check-label" for="r-pub">Afficher</label></div></div>
        </div>
        <div class="d-flex flex-wrap gap-2 justify-content-between mt-4"><button class="btn-vk" type="submit">Enregistrer</button><ConfirmButton v-if="editing.id" @confirm="destroy" /></div></form>
    </Drawer>
</template>
