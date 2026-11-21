<script setup>
import { ref, onMounted, onBeforeUnmount } from 'vue';

const props = defineProps({ slides: { type: Array, default: () => [] } });
const current = ref(0);
const tick = ref(0); // relance l'animation de la barre de progression
const reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
let timer = null;

function go(i) {
    if (!props.slides.length) return;
    current.value = (i + props.slides.length) % props.slides.length;
    tick.value++;
    clearTimeout(timer);
    if (!reduce && props.slides.length > 1) timer = setTimeout(() => go(current.value + 1), 6000);
}

onMounted(() => go(0));
onBeforeUnmount(() => clearTimeout(timer));
</script>

<template>
    <div
        v-for="(s, i) in slides"
        :key="s.image"
        class="hero-slide"
        :class="{ active: i === current }"
    >
        <img :src="s.image" :alt="`${s.title}, ${s.loc}`" :loading="i === 0 ? 'eager' : 'lazy'" :fetchpriority="i === 0 ? 'high' : 'auto'">
    </div>

    <Teleport to="#heroBar" v-if="slides.length">
        <div class="hero-caption">
            <small>{{ slides[current].loc }}</small>
            <strong>{{ slides[current].title }}</strong>
            <a :href="slides[current].href" class="ms-2 small text-white text-decoration-underline">Voir le projet</a>
        </div>
        <div class="hero-dots" role="group" aria-label="Choisir une image">
            <button
                v-for="(s, i) in slides"
                :key="`${i}-${i === current ? tick : 0}`"
                type="button"
                :class="{ active: i === current }"
                :aria-label="`Afficher ${s.title}`"
                @click="go(i)"
            ></button>
        </div>
        <span class="hero-coords">5°23′N · 3°59′W — Cocody Angré</span>
    </Teleport>
</template>
