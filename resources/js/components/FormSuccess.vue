<script setup>
defineProps({ title: String, reference: String, text: String });
</script>

<template>
    <div class="success-box">
        <div class="ok"><i class="fa-solid fa-check"></i></div>
        <h3 class="display" style="font-size:1.8rem">{{ title }}</h3>
        <p class="text-muted-vk mb-1">{{ text }}</p>
        <p v-if="reference">Référence : <span class="ref-code">{{ reference }}</span></p>
        <slot />
    </div>
</template>
