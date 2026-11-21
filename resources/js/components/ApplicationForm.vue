<script setup>
import { ref, onMounted, onBeforeUnmount } from 'vue';
import { useForm } from '../lib/useForm';
import FileDrop from './FileDrop.vue';
import FormSuccess from './FormSuccess.vue';

const props = defineProps({ action: String, jobs: { type: Array, default: () => [] }, privacyUrl: String, maxMb: { type: Number, default: 5 }, accept: { type: String, default: 'pdf,doc,docx' } });
const { form, errors, sending, result, submit } = useForm(props.action, { nom: '', poste: '', tel: '', email: '', message: '', consent: false });
const cv = ref([]);
const onApply = (e) => { form.poste = e.detail; };
onMounted(() => window.addEventListener('vk:apply', onApply));
onBeforeUnmount(() => window.removeEventListener('vk:apply', onApply));
</script>

<template>
    <FormSuccess v-if="result" title="Candidature reçue" text="Merci pour votre intérêt. Nous étudions chaque dossier avec attention." :reference="result.reference" />
    <form v-else class="form-vk" novalidate @submit.prevent="submit({ cv: cv[0] })">
        <div class="row g-4">
            <div class="col-md-6"><label class="form-label" for="a-nom">Nom et prénom <span class="req">*</span></label>
                <input id="a-nom" v-model="form.nom" class="form-control" :class="{ 'is-invalid': errors.nom }" autocomplete="name">
                <p v-if="errors.nom" class="field-error">{{ errors.nom }}</p></div>
            <div class="col-md-6"><label class="form-label" for="a-poste">Poste visé <span class="req">*</span></label>
                <select id="a-poste" v-model="form.poste" class="form-select" :class="{ 'is-invalid': errors.poste }"><option value="">Sélectionner</option><option v-for="j in jobs" :key="j">{{ j }}</option><option>Candidature spontanée</option></select>
                <p v-if="errors.poste" class="field-error">{{ errors.poste }}</p></div>
            <div class="col-md-6"><label class="form-label" for="a-tel">Téléphone <span class="req">*</span></label>
                <input id="a-tel" v-model="form.tel" class="form-control" :class="{ 'is-invalid': errors.tel }" type="tel" autocomplete="tel">
                <p v-if="errors.tel" class="field-error">{{ errors.tel }}</p></div>
            <div class="col-md-6"><label class="form-label" for="a-mail">Email <span class="req">*</span></label>
                <input id="a-mail" v-model="form.email" class="form-control" :class="{ 'is-invalid': errors.email }" type="email" autocomplete="email">
                <p v-if="errors.email" class="field-error">{{ errors.email }}</p></div>
            <div class="col-12"><label class="form-label">CV</label>
                <FileDrop v-model="cv" :accept="accept" :max-mb="maxMb" :max-files="1" :multiple="false" title="Déposez votre CV" icon="fa-file-arrow-up" />
                <p v-if="errors.cv" class="field-error">{{ errors.cv }}</p></div>
            <div class="col-12"><label class="form-label" for="a-msg">Message</label>
                <textarea id="a-msg" v-model="form.message" class="form-control" placeholder="Présentez-vous en quelques lignes"></textarea></div>
        </div>
        <div class="form-check mt-3">
            <input id="a-consent" v-model="form.consent" class="form-check-input" type="checkbox">
            <label class="form-check-label small text-muted-vk" for="a-consent">J'accepte que VOKAMIN utilise ces informations pour traiter ma candidature, conformément à la <a :href="privacyUrl" target="_blank">politique de confidentialité</a>.</label>
            <p v-if="errors.consent" class="field-error">{{ errors.consent }}</p>
        </div>
        <div class="hp-field" aria-hidden="true"><label for="a-website">Site web</label><input id="a-website" v-model="form.website" tabindex="-1" autocomplete="off"></div>
        <button class="btn-vk mt-4" type="submit" :disabled="sending"><span v-if="sending" class="spinner"></span> {{ sending ? 'Envoi…' : 'Envoyer ma candidature' }} <i v-if="!sending" class="fa-solid fa-paper-plane"></i></button>
    </form>
</template>
