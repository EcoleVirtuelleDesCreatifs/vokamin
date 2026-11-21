<script setup>
import { ref, onMounted, onBeforeUnmount, nextTick } from 'vue';
import { useForm } from '../lib/useForm';

const props = defineProps({ action: String });
const open = ref(false);
const first = ref(null);
const { form, errors, sending, result, submit } = useForm(props.action, { nom: '', email: '', entreprise: '' });
const show = async () => { open.value = true; document.body.style.overflow = 'hidden'; await nextTick(); first.value?.focus(); };
const close = () => { open.value = false; document.body.style.overflow = ''; };
const onKey = (e) => { if (e.key === 'Escape' && open.value) close(); };
onMounted(() => { window.addEventListener('vk:brochure', show); window.addEventListener('keydown', onKey); });
onBeforeUnmount(() => { window.removeEventListener('vk:brochure', show); window.removeEventListener('keydown', onKey); });
</script>

<template>
    <Teleport to="body">
        <Transition name="fade">
            <div v-if="open" class="modal d-block" tabindex="-1" role="dialog" aria-modal="true" aria-labelledby="brochureTitle" style="background:rgba(8,21,58,.55)" @click.self="close">
                <div class="modal-dialog modal-dialog-centered"><div class="modal-content">
                    <div class="modal-header"><h2 id="brochureTitle" class="modal-title">Recevoir la plaquette</h2><button type="button" class="btn-close" aria-label="Fermer" @click="close"></button></div>
                    <div class="modal-body">
                        <div v-if="result" class="text-center py-3">
                            <div class="success-box p-0"><div class="ok"><i class="fa-solid fa-check"></i></div></div>
                            <h3 class="display" style="font-size:1.5rem">Demande enregistrée</h3>
                            <p class="text-muted-vk small">La plaquette vous est envoyée par email.</p>
                            <a v-if="result.url" class="btn-vk sm" :href="result.url" target="_blank" rel="noopener"><i class="fa-solid fa-file-pdf"></i> Ouvrir la plaquette</a>
                        </div>
                        <form v-else class="form-vk" novalidate @submit.prevent="submit()">
                            <p class="text-muted-vk small">Indiquez votre email : la plaquette institutionnelle de VOKAMIN vous est envoyée.</p>
                            <div class="mb-3"><label class="form-label" for="b-nom">Nom <span class="req">*</span></label>
                                <input id="b-nom" ref="first" v-model="form.nom" class="form-control" :class="{ 'is-invalid': errors.nom }" autocomplete="name">
                                <p v-if="errors.nom" class="field-error">{{ errors.nom }}</p></div>
                            <div class="mb-3"><label class="form-label" for="b-mail">Email <span class="req">*</span></label>
                                <input id="b-mail" v-model="form.email" class="form-control" :class="{ 'is-invalid': errors.email }" type="email" autocomplete="email">
                                <p v-if="errors.email" class="field-error">{{ errors.email }}</p></div>
                            <div class="mb-3"><label class="form-label" for="b-ent">Entreprise / institution</label>
                                <input id="b-ent" v-model="form.entreprise" class="form-control" autocomplete="organization"></div>
                            <div class="hp-field" aria-hidden="true"><label for="b-website">Site web</label><input id="b-website" v-model="form.website" tabindex="-1" autocomplete="off"></div>
                            <button class="btn-vk w-100 mt-2" type="submit" :disabled="sending">Recevoir la plaquette <i class="fa-solid fa-paper-plane"></i></button>
                        </form>
                    </div>
                </div></div>
            </div>
        </Transition>
    </Teleport>
</template>
