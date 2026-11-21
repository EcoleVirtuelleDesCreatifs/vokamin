const csrf = () => document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

/**
 * Envoie un formulaire au serveur Laravel.
 * Renvoie { ok, data, errors } ; errors = { champ: "message" } en cas d'erreur de validation (422).
 */
export async function send(url, body, method = 'POST') {
    const headers = { 'X-CSRF-TOKEN': csrf(), 'X-Requested-With': 'XMLHttpRequest', Accept: 'application/json' };
    let payload = body;
    if (body && !(body instanceof FormData)) {
        headers['Content-Type'] = 'application/json';
        payload = JSON.stringify(body);
    }
    try {
        const res = await fetch(url, { method, headers, body: method === 'GET' ? undefined : payload, credentials: 'same-origin' });
        const isJson = (res.headers.get('content-type') || '').includes('application/json');
        const data = isJson ? await res.json() : null;
        if (res.ok) return { ok: true, data, errors: {} };
        if (res.status === 422 && data?.errors) {
            const errors = {};
            Object.entries(data.errors).forEach(([k, v]) => { errors[k.replace(/\.\d+$/, '')] = Array.isArray(v) ? v[0] : v; });
            return { ok: false, data, errors, message: data.message };
        }
        if (res.status === 419) return { ok: false, errors: {}, message: 'Votre session a expiré. Rechargez la page puis réessayez.' };
        if (res.status === 429) return { ok: false, errors: {}, message: 'Trop de tentatives. Patientez une minute puis réessayez.' };
        if (res.status === 401) { window.location.reload(); return { ok: false, errors: {} }; }
        return { ok: false, errors: {}, message: data?.message || 'Une erreur est survenue. Réessayez dans un instant.' };
    } catch (e) {
        return { ok: false, errors: {}, message: 'Connexion impossible. Vérifiez votre réseau puis réessayez.' };
    }
}
