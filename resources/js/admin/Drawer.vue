<script setup>
import { onMounted, onBeforeUnmount } from 'vue';

defineProps({ title: String });
const emit = defineEmits(['close']);
const onKey = (e) => { if (e.key === 'Escape') emit('close'); };
onMounted(() => { window.addEventListener('keydown', onKey); document.body.style.overflow = 'hidden'; });
onBeforeUnmount(() => { window.removeEventListener('keydown', onKey); document.body.style.overflow = ''; });
</script>

<template>
    <Teleport to="body">
        <div class="adm-drawer-backdrop" @click="emit('close')"></div>
        <aside class="adm-drawer" role="dialog" aria-modal="true" :aria-label="title">
            <header>
                <h2 class="h5 mb-0 flex-grow-1" style="font-weight:700">{{ title }}</h2>
                <slot name="actions" />
                <button class="btn-close" type="button" aria-label="Fermer" @click="emit('close')"></button>
            </header>
            <div class="body"><slot /></div>
        </aside>
    </Teleport>
</template>
