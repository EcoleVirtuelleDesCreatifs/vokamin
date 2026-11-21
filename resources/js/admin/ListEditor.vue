<script setup>
/* Édition d'une liste de textes ou d'objets (ex. prestations, méthode, avantages). */
const props = defineProps({
    modelValue: { type: Array, default: () => [] },
    fields: { type: Array, default: null }, // ex. [{ key: 'title', label: 'Titre' }]
    addLabel: { type: String, default: 'Ajouter' },
});
const emit = defineEmits(['update:modelValue']);
const update = (list) => emit('update:modelValue', list);
const setItem = (i, v) => { const l = [...props.modelValue]; l[i] = v; update(l); };
const setField = (i, k, v) => { const l = [...props.modelValue]; l[i] = { ...l[i], [k]: v }; update(l); };
const remove = (i) => { const l = [...props.modelValue]; l.splice(i, 1); update(l); };
const move = (i, d) => { const l = [...props.modelValue]; const j = i + d; if (j < 0 || j >= l.length) return; [l[i], l[j]] = [l[j], l[i]]; update(l); };
const add = () => update([...props.modelValue, props.fields ? Object.fromEntries(props.fields.map((f) => [f.key, ''])) : '']);
</script>

<template>
    <div>
        <div v-for="(item, i) in modelValue" :key="i" class="adm-list-row" :class="{ obj: fields }">
            <template v-if="fields">
                <input v-for="f in fields" :key="f.key" class="form-control form-control-sm" :placeholder="f.label" :value="item[f.key]" @input="setField(i, f.key, $event.target.value)">
            </template>
            <input v-else class="form-control form-control-sm" :value="item" @input="setItem(i, $event.target.value)">
            <div class="d-flex gap-1">
                <button class="icon-btn" type="button" aria-label="Monter" @click="move(i, -1)"><i class="fa-solid fa-arrow-up"></i></button>
                <button class="icon-btn" type="button" aria-label="Descendre" @click="move(i, 1)"><i class="fa-solid fa-arrow-down"></i></button>
                <button class="icon-btn" type="button" aria-label="Supprimer" @click="remove(i)"><i class="fa-solid fa-trash"></i></button>
            </div>
        </div>
        <button class="btn-vk sm ghost" type="button" @click="add"><i class="fa-solid fa-plus"></i> {{ addLabel }}</button>
    </div>
</template>
