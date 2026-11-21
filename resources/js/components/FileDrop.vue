<script setup>
import { ref } from 'vue';
import { toast } from '../lib/toast';

const props = defineProps({
    modelValue: { type: Array, default: () => [] },
    accept: { type: String, default: 'pdf,jpg,jpeg,png' },
    maxMb: { type: Number, default: 10 },
    maxFiles: { type: Number, default: 5 },
    multiple: { type: Boolean, default: true },
    title: { type: String, default: 'Glissez vos fichiers ici' },
    icon: { type: String, default: 'fa-cloud-arrow-up' },
});
const emit = defineEmits(['update:modelValue']);
const input = ref(null);
const drag = ref(false);
const exts = props.accept.split(',').map((e) => e.trim().toLowerCase());

function add(fileList) {
    const files = [...props.modelValue];
    for (const f of Array.from(fileList)) {
        const ext = f.name.split('.').pop().toLowerCase();
        if (!exts.includes(ext)) { toast(`« ${f.name} » : format non accepté (${exts.join(', ')}).`, 'fa-triangle-exclamation'); continue; }
        if (f.size > props.maxMb * 1048576) { toast(`« ${f.name} » dépasse ${props.maxMb} Mo. Compressez-le puis réessayez.`, 'fa-triangle-exclamation'); continue; }
        if (files.length >= props.maxFiles) { toast(`${props.maxFiles} fichier(s) maximum.`, 'fa-triangle-exclamation'); break; }
        files.push(f);
    }
    emit('update:modelValue', props.multiple ? files : files.slice(-1));
}
function remove(i) {
    const files = [...props.modelValue];
    files.splice(i, 1);
    emit('update:modelValue', files);
}
function onDrop(e) { drag.value = false; add(e.dataTransfer.files); }
function onChange(e) { add(e.target.files); e.target.value = ''; }
</script>

<template>
    <div class="dropzone" :class="{ drag }" tabindex="0" role="button" aria-label="Ajouter des fichiers"
         @click="input.click()" @keydown.enter.prevent="input.click()" @keydown.space.prevent="input.click()"
         @dragenter.prevent="drag = true" @dragover.prevent="drag = true" @dragleave.prevent="drag = false" @drop.prevent="onDrop">
        <i class="fa-solid d-block" :class="icon"></i>
        <strong>{{ title }}</strong> ou <u>parcourez</u><br>
        <small class="text-muted-vk">{{ exts.join(', ').toUpperCase() }} · {{ maxMb }} Mo max<span v-if="multiple"> · {{ maxFiles }} fichiers max</span></small>
    </div>
    <input ref="input" type="file" hidden :multiple="multiple" :accept="exts.map((e) => '.' + e).join(',')" @change="onChange">
    <ul v-if="modelValue.length" class="file-list">
        <li v-for="(f, i) in modelValue" :key="f.name + i">
            <span><i class="fa-solid fa-paperclip me-2"></i>{{ f.name }}</span>
            <small class="text-muted-vk ms-auto">{{ (f.size / 1048576).toFixed(1) }} Mo</small>
            <button type="button" aria-label="Retirer le fichier" @click="remove(i)"><i class="fa-solid fa-xmark"></i></button>
        </li>
    </ul>
</template>
