<script setup>
import { ref } from 'vue';

defineProps({ label: { type: String, default: 'Supprimer' } });
const emit = defineEmits(['confirm']);
const asking = ref(false);
</script>

<template>
    <span v-if="!asking"><button class="btn-vk sm ghost" type="button" style="--t:#b3261e;border-color:#b3261e" @click="asking = true"><i class="fa-solid fa-trash"></i> {{ label }}</button></span>
    <span v-else class="d-inline-flex gap-2 align-items-center small">Confirmer ?
        <button class="btn-vk sm" type="button" style="--c:#b3261e;--c-h:#8c1d17" @click="emit('confirm'); asking = false">Oui, supprimer</button>
        <button class="btn-vk sm ghost" type="button" @click="asking = false">Annuler</button>
    </span>
</template>
