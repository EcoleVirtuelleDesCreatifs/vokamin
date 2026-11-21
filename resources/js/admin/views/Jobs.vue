<script setup>
import { ref, reactive, onMounted } from 'vue';
import { get, post, del, toFormData, failMessage } from '../api';
import { toast } from '../../lib/toast';
import Drawer from '../Drawer.vue';
import ListEditor from '../ListEditor.vue';
import ConfirmButton from '../ConfirmButton.vue';

const jobs = ref([]);
const editing = ref(null);
const errors = reactive({});
const load = async () => { jobs.value = await get('/jobs'); };
onMounted(load);
function edit(j) {
    editing.value = j ? JSON.parse(JSON.stringify(j)) : { id: null, title: '', contract: 'CDI', location: 'Abidjan', experience: '', missions: [], is_published: true };
    editing.value.missions ??= [];
    Object.keys(errors).forEach((k) => delete errors[k]);
}
async function save() {
    const e = editing.value;
    const res = await post(e.id ? `/jobs/${e.id}` : '/jobs', toFormData({ title: e.title, contract: e.contract, location: e.location, experience: e.experience, missions: e.missions.filter(Boolean), is_published: e.is_published }));
    if (!res.ok) { Object.assign(errors, res.errors); toast(failMessage(res), 'fa-triangle-exclamation'); return; }
    toast('Offre enregistrée.'); editing.value = null; load();
}
async function destroy() { const res = await del(`/jobs/${editing.value.id}`); if (res.ok) { editing.value = null; toast('Offre supprimée.'); load(); } }
</script>

<template>
    <div class="d-flex justify-content-end mb-3"><button class="btn-vk sm" type="button" @click="edit(null)"><i class="fa-solid fa-plus"></i> Nouvelle offre</button></div>
    <div class="adm-card"><div class="table-wrap"><table class="table adm-table">
        <thead><tr><th>Poste</th><th>Contrat</th><th>Lieu</th><th>Statut</th><th></th></tr></thead>
        <tbody><tr v-for="j in jobs" :key="j.id"><td><strong>{{ j.title }}</strong></td><td>{{ j.contract }}</td><td>{{ j.location }}</td><td><span class="st" :class="j.is_published ? 'done' : 'prog'">{{ j.is_published ? 'En ligne' : 'Masquée' }}</span></td>
            <td class="text-end"><button class="icon-btn" type="button" aria-label="Modifier" @click="edit(j)"><i class="fa-solid fa-pen"></i></button></td></tr>
            <tr v-if="!jobs.length"><td colspan="5" class="adm-empty">Aucune offre. La page Recrutement propose alors la candidature spontanée.</td></tr></tbody>
    </table></div></div>
    <Drawer v-if="editing" :title="editing.id ? editing.title : 'Nouvelle offre'" @close="editing = null">
        <form class="form-vk adm-editor" novalidate @submit.prevent="save"><div class="row g-3">
            <div class="col-12"><label class="form-label" for="j-title">Intitulé du poste *</label><input id="j-title" v-model="editing.title" class="form-control" :class="{ 'is-invalid': errors.title }"><p v-if="errors.title" class="field-error">{{ errors.title }}</p></div>
            <div class="col-md-4"><label class="form-label" for="j-contract">Contrat *</label><input id="j-contract" v-model="editing.contract" class="form-control"></div>
            <div class="col-md-4"><label class="form-label" for="j-loc">Lieu *</label><input id="j-loc" v-model="editing.location" class="form-control"></div>
            <div class="col-md-4"><label class="form-label" for="j-xp">Expérience *</label><input id="j-xp" v-model="editing.experience" class="form-control" :class="{ 'is-invalid': errors.experience }"><p v-if="errors.experience" class="field-error">{{ errors.experience }}</p></div>
            <div class="col-12"><label class="form-label">Missions</label><ListEditor v-model="editing.missions" add-label="Ajouter une mission" /></div>
            <div class="col-12"><div class="form-check form-switch"><input id="j-pub" v-model="editing.is_published" class="form-check-input" type="checkbox"><label class="form-check-label" for="j-pub">En ligne</label></div></div>
        </div>
        <div class="d-flex flex-wrap gap-2 justify-content-between mt-4"><button class="btn-vk" type="submit">Enregistrer</button><ConfirmButton v-if="editing.id" @confirm="destroy" /></div></form>
    </Drawer>
</template>
