<script setup>
import { ref, reactive, computed, nextTick } from 'vue';
import { send } from '../lib/http';
import { toast } from '../lib/toast';
import FileDrop from './FileDrop.vue';

const props = defineProps({
    action: String,
    services: { type: Array, default: () => [] },
    privacyUrl: String,
    whatsapp: String,
    projectsUrl: String,
    maxFiles: { type: Number, default: 5 },
    maxMb: { type: Number, default: 10 },
    accept: { type: String, default: 'pdf,jpg,jpeg,png,dwg,doc,docx' },
});

const steps = ['Prestation', 'Votre projet', 'Coordonnées', 'Validation'];
const step = ref(0);
const root = ref(null);
const sending = ref(false);
const done = ref(null);
const errors = reactive({});
const files = ref([]);
const form = reactive({
    prestation: '', nature: '', localisation: '', surface: '', delai: 'Dès que possible', budget: 'Non défini',
    description: '', nom: '', entreprise: '', tel: '', whatsapp: '', email: '', rdv: 'Non, être rappelé suffit',
    consent: false, website: '',
});

// Pré-sélection depuis un lien « Demander un devis » d'une page service (#slug)
const preset = window.location.hash.replace('#', '');
if (props.services.some((s) => s.value === preset)) form.prestation = preset;

const natures = ['Villa / maison individuelle', 'Immeuble résidentiel', 'Bureaux / local professionnel', 'Commerce', 'Établissement de santé ou scolaire', 'Programme immobilier', 'Terrain nu', 'Autre'];
const delais = ['Dès que possible', 'Sous 3 mois', 'Sous 6 mois', 'Dans plus de 6 mois', 'Je ne sais pas encore'];
const budgets = ['Non défini', 'Moins de 10 M FCFA', '10 à 50 M FCFA', '50 à 150 M FCFA', 'Plus de 150 M FCFA'];
const rdvs = ['Non, être rappelé suffit', 'Oui, dans vos bureaux à Cocody Angré', 'Oui, sur le site du projet', 'Oui, en visioconférence'];
const phone = /^[+0-9 ().-]{8,20}$/;
const email = /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/;

const prestationLabel = computed(() => props.services.find((s) => s.value === form.prestation)?.label || '—');
const summary = computed(() => [
    ['Prestation', prestationLabel.value], ['Nature du bâtiment', form.nature], ['Localisation', form.localisation],
    ['Surface estimée', form.surface ? `${form.surface} m²` : '—'], ['Délai souhaité', form.delai], ['Budget indicatif', form.budget],
    ['Demandeur', [form.nom, form.entreprise].filter(Boolean).join(' · ')], ['Téléphone', form.tel], ['Email', form.email],
    ['Rendez-vous', form.rdv], ['Pièces jointes', files.value.length ? `${files.value.length} fichier(s)` : 'Aucune'],
]);

function validate(i) {
    Object.keys(errors).forEach((k) => delete errors[k]);
    if (i === 0 && !form.prestation) errors.prestation = 'Sélectionnez une prestation pour continuer.';
    if (i === 1) {
        if (!form.nature) errors.nature = 'Indiquez la nature du bâtiment.';
        if (!form.localisation.trim()) errors.localisation = 'Indiquez la commune ou le quartier.';
        if (form.description.trim().length < 20) errors.description = 'Décrivez votre besoin en quelques phrases (20 caractères minimum).';
        if (form.surface && (!Number.isInteger(+form.surface) || +form.surface < 1)) errors.surface = 'Indiquez une surface en m² (nombre entier).';
    }
    if (i === 2) {
        if (!form.nom.trim()) errors.nom = 'Indiquez votre nom et prénom.';
        if (!phone.test(form.tel.trim())) errors.tel = 'Indiquez un numéro valide (chiffres, espaces et + uniquement).';
        if (form.whatsapp && !phone.test(form.whatsapp.trim())) errors.whatsapp = 'Numéro WhatsApp invalide.';
        if (!email.test(form.email.trim())) errors.email = 'Indiquez un email valide, par exemple nom@domaine.ci.';
    }
    if (i === 3 && !form.consent) errors.consent = 'Votre accord est nécessaire pour envoyer la demande.';
    return Object.keys(errors).length === 0;
}

async function scrollTop() {
    await nextTick();
    const top = root.value.getBoundingClientRect().top + window.scrollY - 100;
    if (window.scrollY > top) window.scrollTo({ top, behavior: 'smooth' });
}
function next() {
    if (!validate(step.value)) { toast('Complétez les champs signalés pour continuer.', 'fa-triangle-exclamation'); return; }
    step.value++;
    scrollTop();
}
function prev() { step.value = Math.max(0, step.value - 1); scrollTop(); }

const stepOf = { prestation: 0, nature: 1, localisation: 1, surface: 1, description: 1, files: 1, nom: 2, tel: 2, whatsapp: 2, email: 2, consent: 3 };

async function submit() {
    if (!validate(3)) return;
    sending.value = true;
    const fd = new FormData();
    Object.entries(form).forEach(([k, v]) => fd.append(k, k === 'consent' ? (v ? '1' : '') : v ?? ''));
    files.value.forEach((f) => fd.append('files[]', f));
    const res = await send(props.action, fd);
    sending.value = false;
    if (res.ok) {
        done.value = res.data;
        await nextTick();
        root.value.scrollIntoView({ behavior: 'smooth', block: 'center' });
        return;
    }
    Object.assign(errors, res.errors);
    const first = Object.keys(res.errors)[0];
    if (first && stepOf[first] !== undefined) step.value = stepOf[first];
    toast(res.message || 'Vérifiez les champs signalés.', 'fa-triangle-exclamation');
}
</script>

<template>
    <div ref="root">
        <div v-if="done" class="wizard"><div class="success-box">
            <div class="ok"><i class="fa-solid fa-check"></i></div>
            <h2 class="display" style="font-size:2rem">Merci {{ done.firstname }}, votre demande est envoyée.</h2>
            <p class="lead-vk mx-auto">Un email de confirmation vous a été adressé. Un chargé d'affaires VOKAMIN vous recontacte rapidement.</p>
            <p>Référence de votre demande : <span class="ref-code">{{ done.reference }}</span></p>
            <div class="d-flex flex-wrap gap-3 justify-content-center mt-4">
                <a class="btn-vk blue" :href="projectsUrl">Voir nos réalisations</a>
                <a class="btn-vk wa" :href="whatsapp" target="_blank" rel="noopener" data-track="whatsapp"><i class="fa-brands fa-whatsapp"></i> Nous écrire sur WhatsApp</a>
            </div>
        </div></div>

        <form v-else class="wizard form-vk" novalidate @submit.prevent="submit">
            <div class="wizard-steps" aria-hidden="true">
                <div v-for="(s, i) in steps" :key="s" :class="{ active: i === step, done: i < step }"><span class="n">{{ i + 1 }}</span><span class="lbl">{{ s }}</span></div>
            </div>
            <div class="wizard-body">
                <Transition name="fade" mode="out-in">
                    <div v-if="step === 0" key="0">
                        <h2 class="display" style="font-size:1.8rem">Quel type de prestation recherchez-vous ?</h2>
                        <p class="text-muted-vk mb-4">Choisissez la prestation principale. Vous pourrez préciser vos autres besoins à l'étape suivante.</p>
                        <div class="choice-grid" role="radiogroup" aria-label="Prestation">
                            <div v-for="s in services" :key="s.value" class="choice">
                                <input :id="`pr-${s.value}`" v-model="form.prestation" type="radio" name="prestation" :value="s.value">
                                <label :for="`pr-${s.value}`"><i class="fa-solid" :class="s.icon"></i>{{ s.label }}</label>
                            </div>
                        </div>
                        <p v-if="errors.prestation" class="field-error">{{ errors.prestation }}</p>
                    </div>

                    <div v-else-if="step === 1" key="1">
                        <h2 class="display mb-4" style="font-size:1.8rem">Parlez-nous de votre projet</h2>
                        <div class="row g-4">
                            <div class="col-md-6"><label class="form-label" for="q-nature">Nature du bâtiment <span class="req">*</span></label>
                                <select id="q-nature" v-model="form.nature" class="form-select" :class="{ 'is-invalid': errors.nature }"><option value="">Sélectionner</option><option v-for="n in natures" :key="n">{{ n }}</option></select>
                                <p v-if="errors.nature" class="field-error">{{ errors.nature }}</p></div>
                            <div class="col-md-6"><label class="form-label" for="q-loc">Localisation <span class="req">*</span></label>
                                <input id="q-loc" v-model="form.localisation" class="form-control" :class="{ 'is-invalid': errors.localisation }" placeholder="Ex. Cocody Riviera, Bingerville…">
                                <p v-if="errors.localisation" class="field-error">{{ errors.localisation }}</p></div>
                            <div class="col-md-4"><label class="form-label" for="q-surface">Surface estimée (m²)</label>
                                <input id="q-surface" v-model="form.surface" class="form-control" :class="{ 'is-invalid': errors.surface }" type="number" min="1" inputmode="numeric" placeholder="Ex. 250">
                                <p v-if="errors.surface" class="field-error">{{ errors.surface }}</p></div>
                            <div class="col-md-4"><label class="form-label" for="q-delai">Délai souhaité</label>
                                <select id="q-delai" v-model="form.delai" class="form-select"><option v-for="d in delais" :key="d">{{ d }}</option></select></div>
                            <div class="col-md-4"><label class="form-label" for="q-budget">Budget indicatif</label>
                                <select id="q-budget" v-model="form.budget" class="form-select"><option v-for="b in budgets" :key="b">{{ b }}</option></select></div>
                            <div class="col-12"><label class="form-label" for="q-desc">Description du besoin <span class="req">*</span></label>
                                <textarea id="q-desc" v-model="form.description" class="form-control" :class="{ 'is-invalid': errors.description }" placeholder="Nombre de pièces, niveaux, état actuel, attentes particulières…"></textarea>
                                <p v-if="errors.description" class="field-error">{{ errors.description }}</p></div>
                            <div class="col-12"><label class="form-label">Pièces jointes <span class="text-muted-vk text-lowercase fw-normal" style="letter-spacing:0">(plans, photos, croquis, cahier des charges)</span></label>
                                <FileDrop v-model="files" :accept="accept" :max-mb="maxMb" :max-files="maxFiles" />
                                <p v-if="errors.files" class="field-error">{{ errors.files }}</p></div>
                        </div>
                    </div>

                    <div v-else-if="step === 2" key="2">
                        <h2 class="display mb-4" style="font-size:1.8rem">Vos coordonnées</h2>
                        <div class="row g-4">
                            <div class="col-md-6"><label class="form-label" for="q-nom">Nom et prénom <span class="req">*</span></label>
                                <input id="q-nom" v-model="form.nom" class="form-control" :class="{ 'is-invalid': errors.nom }" autocomplete="name">
                                <p v-if="errors.nom" class="field-error">{{ errors.nom }}</p></div>
                            <div class="col-md-6"><label class="form-label" for="q-ent">Entreprise</label>
                                <input id="q-ent" v-model="form.entreprise" class="form-control" autocomplete="organization" placeholder="Facultatif"></div>
                            <div class="col-md-6"><label class="form-label" for="q-tel">Téléphone <span class="req">*</span></label>
                                <input id="q-tel" v-model="form.tel" class="form-control" :class="{ 'is-invalid': errors.tel }" type="tel" autocomplete="tel" placeholder="+225 07 00 00 00 00">
                                <p v-if="errors.tel" class="field-error">{{ errors.tel }}</p></div>
                            <div class="col-md-6"><label class="form-label" for="q-wa">WhatsApp</label>
                                <input id="q-wa" v-model="form.whatsapp" class="form-control" :class="{ 'is-invalid': errors.whatsapp }" type="tel" placeholder="Si différent du téléphone">
                                <p v-if="errors.whatsapp" class="field-error">{{ errors.whatsapp }}</p></div>
                            <div class="col-12"><label class="form-label" for="q-mail">Email <span class="req">*</span></label>
                                <input id="q-mail" v-model="form.email" class="form-control" :class="{ 'is-invalid': errors.email }" type="email" autocomplete="email" placeholder="nom@domaine.ci">
                                <p v-if="errors.email" class="field-error">{{ errors.email }}</p></div>
                            <div class="col-12"><label class="form-label" for="q-rdv">Souhaitez-vous un rendez-vous ?</label>
                                <select id="q-rdv" v-model="form.rdv" class="form-select"><option v-for="r in rdvs" :key="r">{{ r }}</option></select></div>
                        </div>
                    </div>

                    <div v-else key="3">
                        <h2 class="display mb-2" style="font-size:1.8rem">Vérifiez votre demande</h2>
                        <p class="text-muted-vk mb-4">Relisez les informations avant l'envoi.</p>
                        <table class="spec-table"><tbody><tr v-for="[k, v] in summary" :key="k"><th>{{ k }}</th><td>{{ v || '—' }}</td></tr></tbody></table>
                        <div class="form-check mt-4">
                            <input id="q-consent" v-model="form.consent" class="form-check-input" type="checkbox">
                            <label class="form-check-label small text-muted-vk" for="q-consent">J'accepte que VOKAMIN utilise ces informations pour répondre à ma demande, conformément à la <a :href="privacyUrl" target="_blank">politique de confidentialité</a>.</label>
                            <p v-if="errors.consent" class="field-error">{{ errors.consent }}</p>
                        </div>
                        <div class="hp-field" aria-hidden="true"><label for="q-website">Site web</label><input id="q-website" v-model="form.website" tabindex="-1" autocomplete="off"></div>
                    </div>
                </Transition>
            </div>
            <div class="wizard-foot">
                <button class="btn-vk ghost sm" type="button" :style="{ visibility: step === 0 ? 'hidden' : 'visible' }" @click="prev"><i class="fa-solid fa-arrow-left"></i> Retour</button>
                <span class="small text-muted-vk align-self-center">Étape {{ step + 1 }} sur {{ steps.length }}</span>
                <button v-if="step < steps.length - 1" class="btn-vk sm" type="button" @click="next">Continuer <i class="fa-solid fa-arrow-right"></i></button>
                <button v-else class="btn-vk sm" type="submit" :disabled="sending">
                    <span v-if="sending" class="spinner"></span>
                    {{ sending ? 'Envoi en cours…' : 'Envoyer ma demande' }} <i v-if="!sending" class="fa-solid fa-paper-plane"></i>
                </button>
            </div>
        </form>
    </div>
</template>
