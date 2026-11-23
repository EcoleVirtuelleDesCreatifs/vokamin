<script setup>
import { ref, computed, watch, onBeforeUnmount, nextTick } from 'vue';

const props = defineProps({ images: { type: Array, default: () => [] } });
const index = ref(null);
const closeBtn = ref(null);
const layout = computed(() => `gallery gallery-${Math.min(props.images.length, 4)}`);
const pad = (n) => String(n).padStart(2, '0');
let lastFocus = null;
let touchX = 0;

function open(i) { lastFocus = document.activeElement; index.value = i; }
function close() { index.value = null; lastFocus?.focus(); }
function move(step) { index.value = (index.value + step + props.images.length) % props.images.length; }
function onKey(e) {
    if (index.value === null) return;
    if (e.key === 'Escape') close();
    if (e.key === 'ArrowLeft') move(-1);
    if (e.key === 'ArrowRight') move(1);
}

watch(index, async (v) => {
    document.body.style.overflow = v === null ? '' : 'hidden';
    if (v !== null) { await nextTick(); closeBtn.value?.focus(); }
});
window.addEventListener('keydown', onKey);
onBeforeUnmount(() => window.removeEventListener('keydown', onKey));
</script>

<template>
    <div :class="layout">
        <a v-for="(img, i) in images" :key="img.url" :href="img.url" :aria-label="`Agrandir la photo ${i + 1} : ${img.caption || img.alt || ''}`" @click.prevent="open(i)">
            <img :src="img.url" :alt="img.caption || img.alt || ''" loading="lazy" decoding="async">
        </a>
    </div>

    <Teleport to="body">
        <Transition name="fade">
            <div v-if="index !== null" class="lightbox open" role="dialog" aria-modal="true" aria-label="Visionneuse photo"
                 @click.self="close" @touchstart.passive="touchX = $event.touches[0].clientX"
                 @touchend="Math.abs($event.changedTouches[0].clientX - touchX) > 50 && move($event.changedTouches[0].clientX < touchX ? 1 : -1)">
                <button ref="closeBtn" class="lb-btn lb-close" type="button" aria-label="Fermer" @click="close"><i class="fa-solid fa-xmark"></i></button>
                <button v-if="images.length > 1" class="lb-btn lb-prev" type="button" aria-label="Photo précédente" @click="move(-1)"><i class="fa-solid fa-arrow-left"></i></button>
                <figure>
                    <Transition name="fade" mode="out-in"><img :key="images[index].url" :src="images[index].url" :alt="images[index].caption || images[index].alt || ''"></Transition>
                    <figcaption><span>{{ images[index].caption }}</span><span class="ct">{{ pad(index + 1) }} / {{ pad(images.length) }}</span></figcaption>
                </figure>
                <button v-if="images.length > 1" class="lb-btn lb-next" type="button" aria-label="Photo suivante" @click="move(1)"><i class="fa-solid fa-arrow-right"></i></button>
            </div>
        </Transition>
    </Teleport>
</template>
