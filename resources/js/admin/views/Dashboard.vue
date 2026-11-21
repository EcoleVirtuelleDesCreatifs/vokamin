<script setup>
import { ref, computed, onMounted } from 'vue';
import { get, fmtDate } from '../api';

const emit = defineEmits(['pending', 'go']);
const data = ref(null);
const error = ref('');
const labels = { construction: 'Construction', conception: 'Conception & plans', promotion: 'Promotion immobilière', renovation: 'Rénovation & décoration', maintenance: 'Building Maintenance', negoce: 'Négoce de matériaux', design: 'Design & enseignes', autre: 'Autre besoin' };
const statusLbl = { new: ['new', 'Nouvelle'], progress: ['prog', 'En traitement'], done: ['done', 'Traitée'] };

onMounted(async () => {
    try {
        data.value = await get('/stats');
        const p = data.value.pending;
        emit('pending', p.quotes + p.messages + p.applications);
    } catch (e) { error.value = 'Impossible de charger les statistiques.'; }
});

const W = 640, H = 220, pad = { l: 34, r: 12, t: 14, b: 26 };
const chart = computed(() => {
    if (!data.value) return null;
    const vals = data.value.weeks.map((w) => w.value);
    const max = Math.max(10, Math.ceil(Math.max(...vals) * 1.15 / 10) * 10);
    const x = (i) => pad.l + (i * (W - pad.l - pad.r)) / Math.max(1, vals.length - 1);
    const y = (v) => pad.t + (1 - v / max) * (H - pad.t - pad.b);
    const line = vals.map((v, i) => `${i ? 'L' : 'M'}${x(i).toFixed(1)},${y(v).toFixed(1)}`).join(' ');
    return {
        line, area: `${line} L${x(vals.length - 1)},${y(0)} L${x(0)},${y(0)} Z`,
        ticks: [0, max / 2, max].map((v) => ({ v: Math.round(v), y: y(v) })),
        labels: data.value.weeks.map((w, i) => ({ t: w.label, x: x(i), show: i % 2 === 0 })),
        last: { x: x(vals.length - 1), y: y(vals[vals.length - 1]), v: vals[vals.length - 1] },
    };
});
const delta = (k) => {
    if (!k.prev) return k.value ? '+' + k.value : '0';
    const d = Math.round(((k.value - k.prev) / k.prev) * 100);
    return (d >= 0 ? '+' : '') + d + ' %';
};
</script>

<template>
    <p v-if="error" class="text-danger">{{ error }}</p>
    <div v-else-if="!data" class="adm-empty"><span class="spinner"></span> Chargement…</div>
    <template v-else>
        <div v-if="data.pending.quotes || data.pending.messages || data.pending.applications" class="adm-card mb-3 d-flex flex-wrap gap-3 align-items-center" style="border-left:3px solid var(--vk-green)">
            <i class="fa-solid fa-bell" style="color:var(--vk-green)"></i>
            <span><strong>À traiter :</strong> {{ data.pending.quotes }} devis · {{ data.pending.messages }} message(s) · {{ data.pending.applications }} candidature(s)</span>
            <a href="#demandes" class="ms-auto small fw-bold">Ouvrir les demandes</a>
        </div>
        <div class="adm-kpis" style="grid-template-columns:repeat(auto-fit,minmax(170px,1fr))">
            <div v-for="k in data.kpis" :key="k.label" class="adm-kpi"><small>{{ k.label }}</small><strong>{{ k.value.toLocaleString('fr-FR') }}</strong>
                <span :class="k.value >= k.prev ? 'up' : 'down'"><i class="fa-solid" :class="k.value >= k.prev ? 'fa-arrow-trend-up' : 'fa-arrow-trend-down'"></i> {{ delta(k) }} <span class="text-muted-vk fw-normal">vs 30 j préc.</span></span></div>
        </div>
        <div class="row g-3 mt-1">
            <div class="col-xl-8"><div class="adm-card h-100">
                <div class="d-flex justify-content-between mb-2"><h2 class="h6 mb-0">Visiteurs uniques par semaine</h2><span class="small text-muted-vk">12 dernières semaines</span></div>
                <svg :viewBox="`0 0 ${W} ${H}`" width="100%" role="img" aria-label="Visiteurs par semaine">
                    <g v-for="t in chart.ticks" :key="t.v"><line :x1="pad.l" :x2="W - pad.r" :y1="t.y" :y2="t.y" stroke="#e2e5ea"/><text x="2" :y="t.y + 4" font-size="11" fill="#5a6275">{{ t.v }}</text></g>
                    <text v-for="l in chart.labels.filter((l) => l.show)" :key="l.t" :x="l.x - 14" :y="H - 6" font-size="11" fill="#5a6275">{{ l.t }}</text>
                    <path :d="chart.area" fill="rgba(11,47,143,.12)"/>
                    <path :d="chart.line" fill="none" stroke="#0b2f8f" stroke-width="2"/>
                    <circle :cx="chart.last.x" :cy="chart.last.y" r="5" fill="#0a7322"/>
                    <text :x="chart.last.x - 8" :y="chart.last.y - 10" font-size="12" font-weight="700" fill="#08153a" text-anchor="end">{{ chart.last.v }}</text>
                </svg>
            </div></div>
            <div class="col-xl-4"><div class="adm-card h-100"><h2 class="h6 mb-3">Provenance des visiteurs (30 j)</h2>
                <div v-if="data.sources.length" class="adm-bars"><div v-for="s in data.sources" :key="s.label" class="row-b"><span>{{ s.label }}</span><div class="track"><div class="fill" :style="{ width: s.pct + '%' }"></div></div><span>{{ s.pct }} %</span></div></div>
                <p v-else class="text-muted-vk small">Pas encore de données.</p>
            </div></div>
            <div class="col-xl-6"><div class="adm-card h-100"><h2 class="h6 mb-3">Projets les plus vus</h2>
                <ol class="adm-rank"><li v-for="p in data.topProjects" :key="p.id"><img :src="p.cover_url" alt=""><span>{{ p.name }}<br><span class="small text-muted-vk">{{ p.location }}</span></span><b>{{ p.views }} vues</b></li></ol>
            </div></div>
            <div class="col-xl-6"><div class="adm-card h-100">
                <div class="d-flex justify-content-between mb-3"><h2 class="h6 mb-0">Dernières demandes de devis</h2><a href="#demandes" class="small fw-bold">Tout voir</a></div>
                <div class="table-wrap"><table class="table adm-table mb-0"><tbody>
                    <tr v-for="q in data.latest" :key="q.id"><td class="fw-bold" style="color:var(--vk-blue)">{{ q.reference }}</td><td>{{ q.nom }}</td><td>{{ labels[q.prestation] || q.prestation }}</td><td>{{ fmtDate(q.created_at) }}</td><td class="text-end"><span class="st" :class="statusLbl[q.status]?.[0]">{{ statusLbl[q.status]?.[1] }}</span></td></tr>
                    <tr v-if="!data.latest.length"><td class="text-muted-vk">Aucune demande pour le moment.</td></tr>
                </tbody></table></div>
            </div></div>
            <div class="col-12"><div class="adm-card"><h2 class="h6 mb-3">Pages les plus consultées (30 j)</h2>
                <div class="table-wrap"><table class="table adm-table mb-0"><tbody><tr v-for="p in data.pages" :key="p.path"><td>{{ p.path }}</td><td class="text-end fw-bold">{{ p.total }}</td></tr></tbody></table></div>
            </div></div>
        </div>
    </template>
</template>
