<script setup>
import { ref, computed } from 'vue';

const props = defineProps({
    projects: { type: Array, default: () => [] },
    filters: { type: Object, default: () => ({}) },
});
const active = ref('all');
const list = computed(() => active.value === 'all' ? props.projects : props.projects.filter((p) => p.tags.includes(active.value)));
const count = (tag) => props.projects.filter((p) => p.tags.includes(tag)).length;
</script>

<template>
    <div class="d-flex flex-wrap gap-4 justify-content-between align-items-end mb-5">
        <div>
            <p class="filter-group-label">Domaine</p>
            <div class="filters" role="group" aria-label="Filtrer par domaine">
                <button type="button" :class="{ active: active === 'all' }" @click="active = 'all'">Tous<span class="count">{{ projects.length }}</span></button>
                <button v-for="(label, tag) in filters" :key="tag" type="button" :class="{ active: active === tag }" @click="active = tag">
                    {{ label }}<span class="count">{{ count(tag) }}</span>
                </button>
            </div>
        </div>
        <p class="mb-0 text-muted-vk small fw-bold text-uppercase" style="letter-spacing:.14em" aria-live="polite">
            {{ list.length }} {{ list.length > 1 ? 'projets' : 'projet' }}
        </p>
    </div>

    <TransitionGroup name="portfolio" tag="div" class="portfolio" style="position:relative">
        <a v-for="p in list" :key="p.url" class="proj-card" :href="p.url">
            <div class="ph"><img :src="p.cover" :alt="`${p.name} — ${p.location}`" loading="lazy" decoding="async"></div>
            <div class="meta">
                <div><h3>{{ p.name }}</h3><span class="loc"><i class="fa-solid fa-location-dot"></i>{{ p.location }}</span></div>
                <span class="cat">{{ p.category }}</span>
            </div>
        </a>
    </TransitionGroup>
    <div v-if="!list.length" class="empty-state"><i class="fa-regular fa-folder-open fa-2x mb-3"></i><p>Aucun projet dans ce domaine pour le moment.</p></div>
</template>
