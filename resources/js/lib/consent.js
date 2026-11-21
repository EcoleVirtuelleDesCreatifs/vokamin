const KEY = 'vk_cookies';

export function getConsent() {
    try { return JSON.parse(localStorage.getItem(KEY) || 'null'); } catch (e) { return null; }
}

export function setConsent(value) {
    try { localStorage.setItem(KEY, JSON.stringify({ value, date: Date.now() })); } catch (e) { /* stockage indisponible */ }
    if (value === 'accept') loadAnalytics();
}

/** Charge Google Analytics uniquement après accord, si un identifiant est configuré. */
export function loadAnalytics() {
    const id = window.VK_GA_ID;
    if (!id || window.__vkGa) return;
    window.__vkGa = true;
    const s = document.createElement('script');
    s.async = true;
    s.src = `https://www.googletagmanager.com/gtag/js?id=${encodeURIComponent(id)}`;
    document.head.appendChild(s);
    window.dataLayer = window.dataLayer || [];
    window.gtag = function () { window.dataLayer.push(arguments); };
    window.gtag('js', new Date());
    window.gtag('config', id, { anonymize_ip: true });
}
