<script setup>
/**
 * Éditeur visuel des articles, sans dépendance : intertitres, gras, italique,
 * listes, citations, liens et images insérées dans le texte (envoyées au serveur
 * puis placées à l'endroit du curseur). Le HTML est nettoyé côté serveur.
 */
import { ref, watch, onMounted, onBeforeUnmount, computed } from 'vue';
import { post, toFormData } from './api';
import { toast } from '../lib/toast';

const props = defineProps({ modelValue: { type: String, default: '' }, invalid: Boolean });
const emit = defineEmits(['update:modelValue']);

const area = ref(null);
const fileInput = ref(null);
const block = ref('p');
const active = ref({});
const uploading = ref(0);
const selectedImg = ref(null);
const imgAlt = ref('');
const words = ref(0);
let savedRange = null;

const blocks = [
    { v: 'p', label: 'Paragraphe' },
    { v: 'h2', label: 'Intertitre' },
    { v: 'h3', label: 'Sous-titre' },
    { v: 'blockquote', label: 'Citation' },
];

function sync() {
    const html = area.value.innerHTML;
    words.value = (area.value.innerText.trim().match(/\S+/g) || []).length;
    if (html !== props.modelValue) emit('update:modelValue', html);
}

function setContent(html) {
    area.value.innerHTML = html && html.trim() ? html : '<p><br></p>';
    words.value = (area.value.innerText.trim().match(/\S+/g) || []).length;
}

watch(() => props.modelValue, (v) => { if (area.value && v !== area.value.innerHTML) setContent(v); });

function inEditor(node) { return node && area.value && area.value.contains(node); }

function saveSelection() {
    const sel = window.getSelection();
    if (sel.rangeCount && inEditor(sel.anchorNode)) savedRange = sel.getRangeAt(0).cloneRange();
    refreshState();
}

function restoreSelection() {
    area.value.focus();
    if (!savedRange) return;
    const sel = window.getSelection();
    sel.removeAllRanges();
    sel.addRange(savedRange);
}

function refreshState() {
    const sel = window.getSelection();
    if (!sel.rangeCount || !inEditor(sel.anchorNode)) return;
    active.value = {
        bold: document.queryCommandState('bold'),
        italic: document.queryCommandState('italic'),
        underline: document.queryCommandState('underline'),
        ul: document.queryCommandState('insertUnorderedList'),
        ol: document.queryCommandState('insertOrderedList'),
    };
    let n = sel.anchorNode;
    while (n && n !== area.value) {
        if (n.nodeType === 1 && ['P', 'H2', 'H3', 'BLOCKQUOTE'].includes(n.tagName)) { block.value = n.tagName.toLowerCase(); return; }
        n = n.parentNode;
    }
    block.value = 'p';
}

function exec(cmd, value = null) {
    restoreSelection();
    document.execCommand(cmd, false, value);
    saveSelection();
    sync();
}

function setBlock(tag) {
    exec('formatBlock', `<${tag}>`);
}

function link() {
    saveSelection();
    const current = savedRange && savedRange.commonAncestorContainer.parentElement?.closest('a');
    const url = window.prompt('Adresse du lien (https://…, mailto:…, tel:…). Laissez vide pour retirer le lien.', current?.getAttribute('href') || 'https://');
    if (url === null) return;
    if (!url.trim() || url.trim() === 'https://') { exec('unlink'); return; }
    if (savedRange && savedRange.collapsed && !current) {
        exec('insertHTML', `<a href="${escapeAttr(url.trim())}">${escapeHtml(url.trim())}</a>`);
    } else {
        exec('createLink', url.trim());
    }
}

const escapeHtml = (s) => s.replace(/[&<>]/g, (c) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;' }[c]));
const escapeAttr = (s) => escapeHtml(s).replace(/"/g, '&quot;');

/* ---------- Images dans le texte ---------- */
function pickImages() { saveSelection(); fileInput.value.click(); }

async function onFiles(e) {
    await insertImages(Array.from(e.target.files || []));
    e.target.value = '';
}

async function insertImages(files) {
    const images = files.filter((f) => /^image\/(jpe?g|png|webp)$/i.test(f.type));
    if (!images.length) { if (files.length) toast('Formats acceptés : JPG, PNG ou WebP.', 'fa-triangle-exclamation'); return; }
    for (const file of images) {
        uploading.value++;
        const res = await post('/editor-images', toFormData({}, { image: file }));
        uploading.value--;
        if (!res.ok) { toast(res.errors?.image || res.message || 'Envoi de l\'image impossible.', 'fa-triangle-exclamation'); continue; }
        const alt = file.name.replace(/\.[^.]+$/, '').replace(/[-_]+/g, ' ').trim();
        exec('insertHTML', `<figure><img src="${escapeAttr(res.data.url)}" alt="${escapeAttr(alt)}"><figcaption>Légende de la photo</figcaption></figure><p><br></p>`);
    }
}

function onClick(e) {
    if (e.target.tagName === 'IMG' && inEditor(e.target)) {
        selectedImg.value = e.target;
        imgAlt.value = e.target.getAttribute('alt') || '';
    } else {
        selectedImg.value = null;
    }
    saveSelection();
}

function updateAlt() {
    if (!selectedImg.value) return;
    selectedImg.value.setAttribute('alt', imgAlt.value.trim());
    sync();
}

function removeImage() {
    const img = selectedImg.value;
    if (!img) return;
    (img.closest('figure') || img).remove();
    selectedImg.value = null;
    sync();
}

function moveImage(dir) {
    const fig = selectedImg.value?.closest('figure');
    if (!fig) return;
    const target = dir < 0 ? fig.previousElementSibling : fig.nextElementSibling;
    if (!target) return;
    if (dir < 0) target.before(fig); else target.after(fig);
    sync();
}

/* ---------- Collage et glisser-déposer ---------- */
const KEEP = { P: [], H2: [], H3: [], STRONG: [], B: [], EM: [], I: [], U: [], UL: [], OL: [], LI: [], BLOCKQUOTE: [], A: ['href'], BR: [] };

function cleanPasted(html) {
    const doc = new DOMParser().parseFromString(html, 'text/html');
    const walk = (node) => {
        [...node.childNodes].forEach((child) => {
            if (child.nodeType === 8) { child.remove(); return; }
            if (child.nodeType !== 1) return;
            const tag = child.tagName === 'H1' ? 'H2' : ['H4', 'H5', 'H6'].includes(child.tagName) ? 'H3' : child.tagName;
            if (['SCRIPT', 'STYLE', 'META', 'LINK', 'TITLE', 'IMG', 'SVG'].includes(tag)) { child.remove(); return; }
            walk(child);
            if (!KEEP[tag]) {
                const isBlock = ['DIV', 'SECTION', 'ARTICLE', 'TABLE', 'TR'].includes(tag);
                if (isBlock) { const p = doc.createElement('p'); p.append(...child.childNodes); child.replaceWith(p); } else child.replaceWith(...child.childNodes);
                return;
            }
            let el = child;
            if (tag !== child.tagName) { el = doc.createElement(tag); el.append(...child.childNodes); child.replaceWith(el); }
            [...el.attributes].forEach((a) => { if (!KEEP[tag].includes(a.name)) el.removeAttribute(a.name); });
        });
    };
    walk(doc.body);
    return doc.body.innerHTML;
}

function onPaste(e) {
    const files = Array.from(e.clipboardData?.files || []);
    if (files.length) { e.preventDefault(); saveSelection(); insertImages(files); return; }
    const html = e.clipboardData?.getData('text/html');
    const text = e.clipboardData?.getData('text/plain') || '';
    e.preventDefault();
    if (html) {
        document.execCommand('insertHTML', false, cleanPasted(html));
    } else {
        const paras = text.split(/\r?\n\s*\r?\n/).map((p) => p.trim()).filter(Boolean);
        document.execCommand('insertHTML', false, paras.length > 1 ? paras.map((p) => `<p>${escapeHtml(p).replace(/\r?\n/g, '<br>')}</p>`).join('') : escapeHtml(text).replace(/\r?\n/g, '<br>'));
    }
    sync();
}

function onDrop(e) {
    const files = Array.from(e.dataTransfer?.files || []);
    if (!files.length) return;
    e.preventDefault();
    const range = document.caretRangeFromPoint?.(e.clientX, e.clientY);
    if (range) { savedRange = range; }
    insertImages(files);
}

function onKeydown(e) {
    // Entrée dans une citation ou un intertitre vide : revenir au paragraphe
    if (e.key === 'Enter' && !e.shiftKey) setTimeout(() => { refreshState(); }, 0);
    if ((e.metaKey || e.ctrlKey) && e.key.toLowerCase() === 'k') { e.preventDefault(); link(); }
}

const onSelection = () => saveSelection();
onMounted(() => {
    document.execCommand('defaultParagraphSeparator', false, 'p');
    setContent(props.modelValue);
    document.addEventListener('selectionchange', onSelection);
});
onBeforeUnmount(() => document.removeEventListener('selectionchange', onSelection));

const readMinutes = computed(() => Math.max(1, Math.ceil(words.value / 200)));
</script>

<template>
    <div class="rte" :class="{ 'is-invalid': invalid }">
        <div class="rte-toolbar" role="toolbar" aria-label="Mise en forme">
            <select class="rte-block" :value="block" aria-label="Type de bloc" @mousedown="saveSelection" @change="setBlock($event.target.value)">
                <option v-for="b in blocks" :key="b.v" :value="b.v">{{ b.label }}</option>
            </select>
            <span class="rte-sep"></span>
            <button type="button" title="Gras (Ctrl/Cmd + B)" :class="{ on: active.bold }" @mousedown.prevent="exec('bold')"><i class="fa-solid fa-bold"></i></button>
            <button type="button" title="Italique (Ctrl/Cmd + I)" :class="{ on: active.italic }" @mousedown.prevent="exec('italic')"><i class="fa-solid fa-italic"></i></button>
            <button type="button" title="Souligné" :class="{ on: active.underline }" @mousedown.prevent="exec('underline')"><i class="fa-solid fa-underline"></i></button>
            <span class="rte-sep"></span>
            <button type="button" title="Liste à puces" :class="{ on: active.ul }" @mousedown.prevent="exec('insertUnorderedList')"><i class="fa-solid fa-list-ul"></i></button>
            <button type="button" title="Liste numérotée" :class="{ on: active.ol }" @mousedown.prevent="exec('insertOrderedList')"><i class="fa-solid fa-list-ol"></i></button>
            <button type="button" title="Citation" :class="{ on: block === 'blockquote' }" @mousedown.prevent="setBlock(block === 'blockquote' ? 'p' : 'blockquote')"><i class="fa-solid fa-quote-left"></i></button>
            <span class="rte-sep"></span>
            <button type="button" title="Lien (Ctrl/Cmd + K)" @mousedown.prevent="link"><i class="fa-solid fa-link"></i></button>
            <button type="button" class="rte-img-btn" title="Insérer des images à l'endroit du curseur" @mousedown.prevent="pickImages"><i class="fa-regular fa-image"></i> <span>Images</span></button>
            <span class="rte-sep"></span>
            <button type="button" title="Effacer la mise en forme" @mousedown.prevent="exec('removeFormat')"><i class="fa-solid fa-text-slash"></i></button>
            <button type="button" title="Annuler" @mousedown.prevent="exec('undo')"><i class="fa-solid fa-rotate-left"></i></button>
            <button type="button" title="Rétablir" @mousedown.prevent="exec('redo')"><i class="fa-solid fa-rotate-right"></i></button>
            <span v-if="uploading" class="rte-status"><span class="spinner"></span> Envoi de {{ uploading }} image(s)…</span>
            <input ref="fileInput" type="file" hidden multiple accept=".jpg,.jpeg,.png,.webp" @change="onFiles">
        </div>

        <div v-if="selectedImg" class="rte-imgbar">
            <i class="fa-regular fa-image"></i>
            <label class="small fw-bold" for="rte-alt">Description de l'image</label>
            <input id="rte-alt" v-model="imgAlt" placeholder="Ex. : Signature de la convention au siège de VOKAMIN" @input="updateAlt">
            <button type="button" title="Monter" @click="moveImage(-1)"><i class="fa-solid fa-arrow-up"></i></button>
            <button type="button" title="Descendre" @click="moveImage(1)"><i class="fa-solid fa-arrow-down"></i></button>
            <button type="button" class="danger" title="Retirer l'image" @click="removeImage"><i class="fa-solid fa-trash"></i></button>
        </div>

        <div
            ref="area"
            class="rte-content article-body"
            contenteditable="true"
            role="textbox"
            aria-multiline="true"
            aria-label="Contenu de l'article"
            spellcheck="true"
            lang="fr"
            @input="sync"
            @keyup="saveSelection"
            @mouseup="saveSelection"
            @click="onClick"
            @keydown="onKeydown"
            @paste="onPaste"
            @dragover.prevent
            @drop="onDrop"
        ></div>
        <div class="rte-foot"><span>{{ words }} mots · {{ readMinutes }} min de lecture</span><span>Astuce : glissez-déposez ou collez des photos directement dans le texte.</span></div>
    </div>
</template>
