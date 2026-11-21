<script setup>
import { useForm } from '../lib/useForm';
import FormSuccess from './FormSuccess.vue';

const props = defineProps({ action: String, privacyUrl: String });
const { form, errors, sending, result, submit } = useForm(props.action, { nom: '', tel: '', email: '', sujet: '', message: '', consent: false });
const sujets = ['Renseignement sur un service', 'Prise de rendez-vous', "Suivi d'un chantier", 'Partenariat / fournisseur', 'Presse', 'Autre'];
</script>

<template>
    <FormSuccess v-if="result" title="Message envoyé" text="Nous vous répondons dans les meilleurs délais." :reference="result.reference" />
    <form v-else class="form-vk mt-4" novalidate @submit.prevent="submit()">
        <div class="row g-4">
            <div class="col-md-6"><label class="form-label" for="c-nom">Nom et prénom <span class="req">*</span></label>
                <input id="c-nom" v-model="form.nom" class="form-control" :class="{ 'is-invalid': errors.nom }" required autocomplete="name">
                <p v-if="errors.nom" class="field-error">{{ errors.nom }}</p></div>
            <div class="col-md-6"><label class="form-label" for="c-tel">Téléphone</label>
                <input id="c-tel" v-model="form.tel" class="form-control" :class="{ 'is-invalid': errors.tel }" type="tel" autocomplete="tel">
                <p v-if="errors.tel" class="field-error">{{ errors.tel }}</p></div>
            <div class="col-md-6"><label class="form-label" for="c-mail">Email <span class="req">*</span></label>
                <input id="c-mail" v-model="form.email" class="form-control" :class="{ 'is-invalid': errors.email }" type="email" required autocomplete="email">
                <p v-if="errors.email" class="field-error">{{ errors.email }}</p></div>
            <div class="col-md-6"><label class="form-label" for="c-sujet">Sujet <span class="req">*</span></label>
                <select id="c-sujet" v-model="form.sujet" class="form-select" :class="{ 'is-invalid': errors.sujet }" required><option value="">Sélectionner</option><option v-for="s in sujets" :key="s">{{ s }}</option></select>
                <p v-if="errors.sujet" class="field-error">{{ errors.sujet }}</p></div>
            <div class="col-12"><label class="form-label" for="c-msg">Message <span class="req">*</span></label>
                <textarea id="c-msg" v-model="form.message" class="form-control" :class="{ 'is-invalid': errors.message }" required></textarea>
                <p v-if="errors.message" class="field-error">{{ errors.message }}</p></div>
        </div>
        <div class="form-check mt-3">
            <input id="c-consent" v-model="form.consent" class="form-check-input" type="checkbox">
            <label class="form-check-label small text-muted-vk" for="c-consent">J'accepte que VOKAMIN utilise ces informations pour répondre à ma demande, conformément à la <a :href="privacyUrl" target="_blank">politique de confidentialité</a>.</label>
            <p v-if="errors.consent" class="field-error">{{ errors.consent }}</p>
        </div>
        <div class="hp-field" aria-hidden="true"><label for="c-website">Site web</label><input id="c-website" v-model="form.website" tabindex="-1" autocomplete="off"></div>
        <button class="btn-vk mt-4" type="submit" :disabled="sending"><span v-if="sending" class="spinner"></span> {{ sending ? 'Envoi…' : 'Envoyer le message' }} <i v-if="!sending" class="fa-solid fa-paper-plane"></i></button>
    </form>
</template>
