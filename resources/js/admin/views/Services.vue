<script setup>
import { ref, reactive, onMounted } from 'vue';
import { get, post, toFormData } from '../api';
import { toast } from '../../lib/toast';
import Drawer from '../Drawer.vue';
import ListEditor from '../ListEditor.vue';

const props = defineProps({ siteUrl: String });
const services = ref([]);
const editing = ref(null);
const files = reactive({ image_file: null, image2_file: null, hero_file: null });
const errors = reactive({});
const saving = ref(false);
const iconFields = [{ key: 'icon', label: 'Icône (fa-…)' }, { key: 'title', label: 'Titre' }, { key: 'text', label: 'Texte' }];

onMounted(async () => { services.value = await get('/services'); });
function edit(s) {
    editing.value = JSON.parse(JSON.stringify(s));
    ['prestations', 'beneficiaires', 'methode', 'avantages'].forEach((k) => { editing.value[k] ??= []; });
    Object.keys(files).forEach((k) => { files[k] = null; });
    Object.keys(errors).forEach((k) => delete errors[k]);
}
async function save() {
    saving.value = true;
    const e = editing.value;
    const body = { name: e.name, short: e.short, icon: e.icon, teaser: e.teaser, intro: e.intro, intro2: e.intro2 || '', prestations: e.prestations, beneficiaires: e.beneficiaires, methode: e.methode, avantages: e.avantages, is_published: e.is_published, position: e.position };
    const res = await post(`/services/${e.slug}`, toFormData(body, files));
    saving.value = false;
    if (!res.ok) { Object.assign(errors, res.errors); toast('Vérifiez les champs signalés.', 'fa-triangle-exclamation'); return; }
    const i = services.value.findIndex((s) => s.id === res.data.id);
    services.value[i] = res.data;
    toast('Service enregistré.');
    editing.value = null;
}
</script>

<template>
    <div class="row g-3">
        <div v-for="s in services" :key="s.id" class="col-md-6 col-xl-4"><div class="adm-card svc-adm">
            <img :src="s.image_url" alt="">
            <div class="flex-grow-1" style="min-width:0"><strong class="d-block">{{ s.name }}</strong><span class="st mt-1" :class="s.is_published ? 'done' : 'prog'">{{ s.is_published ? 'Publié' : 'Masqué' }}</span></div>
            <a class="icon-btn" :href="`${siteUrl.replace(/\/$/, '')}/services/${s.slug}`" target="_blank" rel="noopener" aria-label="Voir"><i class="fa-regular fa-eye"></i></a>
            <button class="icon-btn" type="button" aria-label="Modifier" @click="edit(s)"><i class="fa-solid fa-pen"></i></button>
        </div></div>
    </div>

    <Drawer v-if="editing" :title="editing.name" @close="editing = null">
        <form class="form-vk" novalidate @submit.prevent="save">
            <div class="adm-editor mb-3"><div class="row g-3">
                <div class="col-md-8"><label class="form-label" for="s-name">Nom complet *</label><input id="s-name" v-model="editing.name" class="form-control" :class="{ 'is-invalid': errors.name }"><p v-if="errors.name" class="field-error">{{ errors.name }}</p></div>
                <div class="col-md-4"><label class="form-label" for="s-short">Nom court (menu) *</label><input id="s-short" v-model="editing.short" class="form-control"></div>
                <div class="col-md-4"><label class="form-label" for="s-icon">Icône</label><div class="input-group"><span class="input-group-text"><i class="fa-solid" :class="editing.icon"></i></span><input id="s-icon" v-model="editing.icon" class="form-control"></div></div>
                <div class="col-md-4"><label class="form-label" for="s-pos">Ordre</label><input id="s-pos" v-model.number="editing.position" class="form-control" type="number"></div>
                <div class="col-md-4 d-flex align-items-end"><div class="form-check form-switch"><input id="s-pub" v-model="editing.is_published" class="form-check-input" type="checkbox"><label class="form-check-label" for="s-pub">Publié</label></div></div>
                <div class="col-12"><label class="form-label" for="s-teaser">Accroche *</label><textarea id="s-teaser" v-model="editing.teaser" class="form-control" style="min-height:70px"></textarea></div>
                <div class="col-12"><label class="form-label" for="s-intro">Description *</label><textarea id="s-intro" v-model="editing.intro" class="form-control"></textarea></div>
                <div class="col-12"><label class="form-label" for="s-intro2">Complément</label><textarea id="s-intro2" v-model="editing.intro2" class="form-control" style="min-height:80px"></textarea></div>
            </div></div>
            <div class="adm-editor mb-3"><h3 class="h6 mb-3">Prestations proposées</h3><ListEditor v-model="editing.prestations" add-label="Ajouter une prestation" /></div>
            <div class="adm-editor mb-3"><h3 class="h6 mb-3">Pour qui ?</h3><ListEditor v-model="editing.beneficiaires" :fields="iconFields" add-label="Ajouter un bénéficiaire" /></div>
            <div class="adm-editor mb-3"><h3 class="h6 mb-3">Méthode d'intervention</h3><ListEditor v-model="editing.methode" :fields="[{ key: 'title', label: 'Étape' }, { key: 'text', label: 'Détail' }]" add-label="Ajouter une étape" /></div>
            <div class="adm-editor mb-3"><h3 class="h6 mb-3">Avantages</h3><ListEditor v-model="editing.avantages" :fields="iconFields" add-label="Ajouter un avantage" /></div>
            <div class="adm-editor mb-3"><h3 class="h6 mb-3">Images</h3><div class="row g-3">
                <div v-for="[key, label, url] in [['image_file', 'Image principale', editing.image_url], ['image2_file', 'Image secondaire', editing.image2_url], ['hero_file', 'Bandeau de page', editing.hero_url]]" :key="key" class="col-md-4">
                    <img :src="url" alt="" class="w-100 mb-2" style="aspect-ratio:4/3;object-fit:cover">
                    <label class="small fw-bold d-block mb-1">{{ label }}</label>
                    <input class="form-control form-control-sm" type="file" accept=".jpg,.jpeg,.png,.webp" @change="files[key] = $event.target.files[0]">
                </div>
            </div></div>
            <button class="btn-vk" type="submit" :disabled="saving"><span v-if="saving" class="spinner"></span> Enregistrer</button>
        </form>
    </Drawer>
</template>
