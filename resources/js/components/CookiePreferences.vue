<script setup>
import { ref } from 'vue';
import { getConsent, setConsent } from '../lib/consent';
import { toast } from '../lib/toast';

const stats = ref(getConsent()?.value === 'accept');
function save() {
    setConsent(stats.value ? 'accept' : 'refuse');
    toast('Vos préférences de cookies sont enregistrées.');
}
</script>

<template>
    <form class="p-4 bg-paper mt-3" @submit.prevent="save">
        <div class="form-check form-switch mb-3"><input id="ck-ess" class="form-check-input" type="checkbox" checked disabled><label class="form-check-label" for="ck-ess"><strong>Cookies essentiels</strong> — toujours actifs, nécessaires au fonctionnement du site.</label></div>
        <div class="form-check form-switch mb-4"><input id="ck-stats" v-model="stats" class="form-check-input" type="checkbox"><label class="form-check-label" for="ck-stats"><strong>Mesure d'audience</strong> — nous aide à comprendre les pages consultées.</label></div>
        <button class="btn-vk sm" type="submit">Enregistrer mes choix</button>
    </form>
</template>
