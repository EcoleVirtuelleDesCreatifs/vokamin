<script setup>
import { useForm } from '../lib/useForm';

const props = defineProps({ action: String });
const { form, errors, sending, result, submit } = useForm(props.action, { email: '' });
</script>

<template>
    <p v-if="result" class="mb-0 fw-bold" style="color:var(--vk-green)"><i class="fa-solid fa-circle-check me-2"></i>Merci ! Votre inscription est enregistrée.</p>
    <form v-else class="form-vk d-flex gap-3 align-items-end flex-wrap" novalidate @submit.prevent="submit()">
        <div class="flex-grow-1"><label class="form-label" for="nl-mail">Email</label>
            <input id="nl-mail" v-model="form.email" class="form-control" :class="{ 'is-invalid': errors.email }" type="email" placeholder="nom@domaine.ci" autocomplete="email">
            <p v-if="errors.email" class="field-error">{{ errors.email }}</p></div>
        <div class="hp-field" aria-hidden="true"><label for="nl-website">Site web</label><input id="nl-website" v-model="form.website" tabindex="-1" autocomplete="off"></div>
        <button class="btn-vk" type="submit" :disabled="sending">S'inscrire</button>
    </form>
</template>
