<script setup>
import { ref } from 'vue';

defineProps({ before: String, after: String, labelBefore: String, labelAfter: String });
const value = ref(50);
</script>

<template>
    <div class="ba reveal in">
        <img :src="before" :alt="labelBefore" loading="lazy">
        <img class="ba-after" :src="after" :alt="labelAfter" :style="{ clipPath: `inset(0 0 0 ${value}%)` }" loading="lazy">
        <span class="ba-handle" :style="{ left: `${value}%` }"></span>
        <span class="lbl l">{{ labelBefore }}</span>
        <span class="lbl r">{{ labelAfter }}</span>
        <input v-model.number="value" type="range" min="0" max="100" :aria-label="`Comparer ${labelBefore} et ${labelAfter}`">
    </div>
</template>
