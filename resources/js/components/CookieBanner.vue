<script setup>
import { ref, onMounted } from 'vue';
import { getConsent, setConsent } from '../lib/consent';
import { toast } from '../lib/toast';

defineProps({ policyUrl: String });
const visible = ref(false);
onMounted(() => { if (!getConsent()) setTimeout(() => { visible.value = true; }, 1200); });
function choose(value) {
    setConsent(value);
    visible.value = false;
    toast(value === 'accept' ? 'Préférences enregistrées : mesure d\'audience activée.' : 'Préférences enregistrées : seuls les cookies essentiels sont utilisés.');
}
</script>

<template>
    <div class="cookie-banner" :class="{ show: visible }" role="dialog" aria-live="polite" aria-label="Cookies">
        <h5>Vos préférences de cookies</h5>
        <p>Nous utilisons des cookies essentiels au fonctionnement du site et, avec votre accord, des cookies de mesure d'audience. <a :href="policyUrl">En savoir plus</a></p>
        <div class="d-flex flex-wrap gap-2">
            <button class="btn-vk sm" type="button" @click="choose('accept')">Tout accepter</button>
            <button class="btn-vk sm ghost" type="button" @click="choose('refuse')">Essentiels uniquement</button>
        </div>
    </div>
</template>
