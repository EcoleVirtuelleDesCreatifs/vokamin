<script setup>
import { ref, reactive, onMounted } from 'vue';
import { get, post, toFormData } from '../api';
import { toast } from '../../lib/toast';

const s = ref(null);
const brochure = ref(null);
const errors = reactive({});
const pwd = reactive({ current_password: '', password: '', password_confirmation: '' });
const pwdErrors = reactive({});
const groups = [
    ['Coordonnées', [['tel1', 'Téléphone principal'], ['tel2', 'Téléphone secondaire'], ['fax', 'Fax'], ['email', 'Email affiché'], ['whatsapp', 'Numéro WhatsApp'], ['address', 'Adresse'], ['bp', 'Boîte postale'], ['city', 'Ville']]],
    ['WhatsApp & accueil', [['whatsapp_message', 'Message WhatsApp pré-rempli', true], ['tagline', 'Signature (titre de l\'accueil)', true], ['hero_lead', 'Texte sous la signature', true]]],
    ['Horaires', [['hours_week', 'Lundi – vendredi'], ['hours_sat', 'Samedi'], ['hours_sun', 'Dimanche']]],
    ['Réseaux sociaux (laisser vide pour masquer)', [['facebook', 'Facebook (URL)'], ['linkedin', 'LinkedIn (URL)'], ['instagram', 'Instagram (URL)'], ['youtube', 'YouTube (URL)']]],
    ['Informations légales', [['company_name', 'Raison sociale'], ['brand', 'Nom commercial'], ['forme', 'Forme juridique'], ['capital', 'Capital'], ['rccm', 'RCCM'], ['cc', 'Compte contribuable'], ['creation', 'Date de création'], ['gerant', 'Gérant'], ['host_name', 'Hébergeur (mentions légales)', true]]],
];

onMounted(async () => { s.value = await get('/settings'); });
async function save() {
    Object.keys(errors).forEach((k) => delete errors[k]);
    const keys = groups.flatMap(([, f]) => f.map(([k]) => k));
    const res = await post('/settings', toFormData(Object.fromEntries(keys.map((k) => [k, s.value[k] ?? ''])), { brochure_file: brochure.value }));
    if (!res.ok) { Object.assign(errors, res.errors); toast('Vérifiez les champs signalés.', 'fa-triangle-exclamation'); return; }
    s.value = res.data; brochure.value = null;
    toast('Paramètres enregistrés : le site est à jour.');
}
async function changePassword() {
    Object.keys(pwdErrors).forEach((k) => delete pwdErrors[k]);
    const res = await post('/account', { ...pwd });
    if (!res.ok) { Object.assign(pwdErrors, res.errors); return; }
    Object.keys(pwd).forEach((k) => { pwd[k] = ''; });
    toast('Mot de passe modifié.');
}
</script>

<template>
    <div v-if="!s" class="adm-empty"><span class="spinner"></span></div>
    <div v-else class="row g-3">
        <div class="col-xl-8"><form class="form-vk" novalidate @submit.prevent="save">
            <div v-for="[title, fields] in groups" :key="title" class="adm-card mb-3"><h2 class="h6 mb-3">{{ title }}</h2><div class="row g-3">
                <div v-for="[key, label, wide] in fields" :key="key" :class="wide ? 'col-12' : 'col-md-6'">
                    <label class="form-label" :for="`st-${key}`">{{ label }}</label>
                    <input :id="`st-${key}`" v-model="s[key]" class="form-control" :class="{ 'is-invalid': errors[key] }">
                    <p v-if="errors[key]" class="field-error">{{ errors[key] }}</p>
                </div>
            </div></div>
            <div class="adm-card mb-3"><h2 class="h6 mb-3">Plaquette institutionnelle (PDF)</h2>
                <p v-if="s.brochure_url" class="small"><a :href="s.brochure_url" target="_blank" rel="noopener"><i class="fa-solid fa-file-pdf me-1"></i> Plaquette actuelle</a></p>
                <p v-else class="small text-muted-vk">Aucune plaquette en ligne : les demandes sont transmises par email à l'équipe.</p>
                <input class="form-control form-control-sm" type="file" accept=".pdf" @change="brochure = $event.target.files[0]">
                <p v-if="errors.brochure_file" class="field-error">{{ errors.brochure_file }}</p>
            </div>
            <button class="btn-vk" type="submit">Enregistrer les paramètres</button>
        </form></div>
        <div class="col-xl-4"><form class="adm-card form-vk" novalidate @submit.prevent="changePassword">
            <h2 class="h6 mb-3">Mot de passe administrateur</h2>
            <div class="mb-3"><label class="form-label" for="pw-cur">Mot de passe actuel</label><input id="pw-cur" v-model="pwd.current_password" class="form-control" type="password" autocomplete="current-password"><p v-if="pwdErrors.current_password" class="field-error">{{ pwdErrors.current_password }}</p></div>
            <div class="mb-3"><label class="form-label" for="pw-new">Nouveau mot de passe</label><input id="pw-new" v-model="pwd.password" class="form-control" type="password" autocomplete="new-password"><p v-if="pwdErrors.password" class="field-error">{{ pwdErrors.password }}</p></div>
            <div class="mb-3"><label class="form-label" for="pw-conf">Confirmation</label><input id="pw-conf" v-model="pwd.password_confirmation" class="form-control" type="password" autocomplete="new-password"></div>
            <p class="small text-muted-vk">10 caractères minimum, avec lettres et chiffres.</p>
            <button class="btn-vk sm" type="submit">Modifier le mot de passe</button>
        </form></div>
    </div>
</template>
