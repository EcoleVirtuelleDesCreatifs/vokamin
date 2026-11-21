/* Interactions générales du site (hors composants Vue) */
import { toast } from './lib/toast';

const $ = (s, c = document) => c.querySelector(s);
const $$ = (s, c = document) => Array.from(c.querySelectorAll(s));
const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
const csrf = () => $('meta[name="csrf-token"]')?.getAttribute('content') || '';

export function initSite() {
    /* En-tête : transparent sur le visuel, plein au défilement */
    const header = $('.site-header');
    const toTop = $('.to-top');
    const onScroll = () => {
        header?.classList.toggle('is-solid', window.scrollY > 40);
        toTop?.classList.toggle('show', window.scrollY > 700);
    };
    window.addEventListener('scroll', onScroll, { passive: true });
    onScroll();
    toTop?.addEventListener('click', () => window.scrollTo({ top: 0, behavior: reduceMotion ? 'auto' : 'smooth' }));

    /* Apparitions au défilement et compteurs */
    const countUp = (el) => {
        if (el.dataset.done) return;
        el.dataset.done = 1;
        const target = parseFloat(el.dataset.count);
        const t0 = performance.now();
        const step = (t) => {
            const p = Math.min(1, (t - t0) / 1500);
            el.firstChild.nodeValue = Math.round(target * (1 - Math.pow(1 - p, 3)));
            if (p < 1) requestAnimationFrame(step);
        };
        requestAnimationFrame(step);
    };
    if (!reduceMotion && 'IntersectionObserver' in window) {
        document.documentElement.classList.add('reveal-ready');
        const io = new IntersectionObserver((entries) => entries.forEach((e) => {
            if (!e.isIntersecting) return;
            e.target.classList.add('in');
            if (e.target.dataset.count) countUp(e.target);
            io.unobserve(e.target);
        }), { threshold: 0.12, rootMargin: '0px 0px -6% 0px' });
        $$('.reveal, .img-reveal, .process, [data-count]').forEach((el) => {
            if (el.getBoundingClientRect().top < window.innerHeight * 0.95) {
                requestAnimationFrame(() => el.classList.add('in'));
                if (el.dataset.count) countUp(el);
            } else io.observe(el);
        });
    } else {
        $$('.process').forEach((p) => p.classList.add('in'));
    }

    /* Carrousel de projets : flèches + glisser à la souris */
    $$('[data-track-carousel]').forEach((wrap) => {
        const track = $('.proj-track', wrap);
        if (!track) return;
        const amount = () => (track.querySelector('.proj-card')?.offsetWidth || 400) + 22;
        $('[data-prev]', wrap)?.addEventListener('click', () => track.scrollBy({ left: -amount(), behavior: 'smooth' }));
        $('[data-next]', wrap)?.addEventListener('click', () => track.scrollBy({ left: amount(), behavior: 'smooth' }));
        let down = false, sx = 0, sl = 0, moved = false;
        track.addEventListener('pointerdown', (e) => { if (e.pointerType !== 'mouse') return; down = true; moved = false; sx = e.clientX; sl = track.scrollLeft; });
        window.addEventListener('pointermove', (e) => { if (!down) return; const dx = e.clientX - sx; if (Math.abs(dx) > 5) { moved = true; track.classList.add('dragging'); } track.scrollLeft = sl - dx; });
        window.addEventListener('pointerup', () => { if (!down) return; down = false; setTimeout(() => track.classList.remove('dragging'), 0); });
        track.addEventListener('click', (e) => { if (moved) { e.preventDefault(); moved = false; } }, true);
    });

    /* Boutons de copie */
    $$('[data-copy]').forEach((b) => b.addEventListener('click', (e) => {
        e.preventDefault();
        const txt = b.dataset.copy;
        const done = () => toast(b.dataset.copyLabel || `« ${txt} » copié.`, 'fa-copy');
        if (navigator.clipboard?.writeText) navigator.clipboard.writeText(txt).then(done).catch(() => fallbackCopy(txt, done));
        else fallbackCopy(txt, done);
    }));
    function fallbackCopy(txt, cb) {
        const ta = document.createElement('textarea');
        ta.value = txt; ta.style.position = 'fixed'; ta.style.opacity = '0';
        document.body.appendChild(ta); ta.select();
        try { document.execCommand('copy'); cb(); } catch (e) { toast(txt, 'fa-copy'); }
        ta.remove();
    }

    /* Statistiques : clics WhatsApp et appels */
    document.addEventListener('click', (e) => {
        const a = e.target.closest('[data-track]');
        if (!a) return;
        const body = new FormData();
        body.append('type', a.dataset.track);
        body.append('path', window.location.pathname);
        body.append('_token', csrf());
        try { navigator.sendBeacon ? navigator.sendBeacon('/track', body) : fetch('/track', { method: 'POST', body, keepalive: true }); } catch (err) { /* sans incidence */ }
    });

    /* Sommaire actif des pages légales */
    const tocLinks = $$('.legal-toc a');
    if (tocLinks.length && 'IntersectionObserver' in window) {
        const io2 = new IntersectionObserver((ents) => ents.forEach((en) => {
            if (en.isIntersecting) tocLinks.forEach((a) => a.classList.toggle('active', a.getAttribute('href') === '#' + en.target.id));
        }), { rootMargin: '-30% 0px -60% 0px' });
        tocLinks.forEach((a) => { const t = document.getElementById(a.getAttribute('href').slice(1)); if (t) io2.observe(t); });
    }

    /* Recrutement : bouton « Postuler » qui pré-remplit le poste */
    $$('[data-apply]').forEach((a) => a.addEventListener('click', () => {
        window.dispatchEvent(new CustomEvent('vk:apply', { detail: a.dataset.apply }));
    }));

    /* Plaquette : ouverture de la modale */
    $$('[data-open-brochure]').forEach((b) => b.addEventListener('click', (e) => {
        e.preventDefault();
        window.dispatchEvent(new CustomEvent('vk:brochure'));
    }));

    /* Fermer le menu mobile au clic sur un lien */
    $$('.mobile-nav a').forEach((a) => a.addEventListener('click', () => {
        const oc = window.bootstrap?.Offcanvas.getInstance($('.mobile-nav'));
        oc?.hide();
    }));

    /* Transition douce entre les pages internes */
    const curtain = $('.page-curtain');
    if (curtain && !reduceMotion) {
        document.addEventListener('click', (e) => {
            const a = e.target.closest('a');
            if (!a || e.defaultPrevented || e.metaKey || e.ctrlKey || e.shiftKey || a.target === '_blank' || a.hasAttribute('download')) return;
            const url = new URL(a.href, window.location.href);
            if (url.origin !== window.location.origin || url.pathname.startsWith('/admin') || (url.pathname === window.location.pathname && url.hash)) return;
            if (a.closest('[data-no-transition]')) return;
            e.preventDefault();
            curtain.classList.add('show');
            setTimeout(() => { window.location.href = url.href; }, 350);
        });
        window.addEventListener('pageshow', () => curtain.classList.remove('show'));
    }
}
