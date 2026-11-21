import { send } from '../lib/http';

let base = '/admin/api';
export const setBase = (url) => { base = url.replace(/\/$/, ''); };

export async function get(path, params = {}) {
    const qs = new URLSearchParams(Object.entries(params).filter(([, v]) => v !== '' && v != null)).toString();
    const res = await fetch(`${base}${path}${qs ? '?' + qs : ''}`, { headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' }, credentials: 'same-origin' });
    if (res.status === 401 || res.status === 419) { window.location.reload(); return null; }
    if (!res.ok) throw new Error('Chargement impossible');
    return res.json();
}

export const post = async (path, body) => send(`${base}${path}`, await body, 'POST');
export const patch = (path, body) => send(`${base}${path}`, body, 'PATCH');
export const del = (path) => send(`${base}${path}`, null, 'DELETE');

const MAX_SIDE = 1600;
const SHRINK_TYPES = ['image/jpeg', 'image/png', 'image/webp'];

/**
 * Réduit une photo dans le navigateur avant l'envoi (1600 px max, ~80 % de qualité).
 * Une photo de téléphone de 5 à 10 Mo passe ainsi sous les limites d'envoi de
 * l'hébergeur. En cas d'échec, le fichier d'origine est envoyé tel quel.
 */
export async function shrinkImage(file) {
    if (!(file instanceof File) || !SHRINK_TYPES.includes(file.type) || typeof createImageBitmap !== 'function') return file;
    try {
        const bmp = await createImageBitmap(file);
        const ratio = Math.min(1, MAX_SIDE / Math.max(bmp.width, bmp.height));
        if (ratio === 1 && file.size <= 1.5 * 1024 * 1024) { bmp.close?.(); return file; }
        const canvas = document.createElement('canvas');
        canvas.width = Math.round(bmp.width * ratio);
        canvas.height = Math.round(bmp.height * ratio);
        canvas.getContext('2d').drawImage(bmp, 0, 0, canvas.width, canvas.height);
        bmp.close?.();
        // PNG/WebP (logos, transparence) → WebP ; photos → JPEG.
        const type = file.type === 'image/jpeg' ? 'image/jpeg' : 'image/webp';
        const blob = await new Promise((resolve) => canvas.toBlob(resolve, type, 0.82));
        if (!blob || blob.size >= file.size) return file;
        const ext = blob.type === 'image/jpeg' ? 'jpg' : blob.type === 'image/webp' ? 'webp' : 'png';
        const name = file.name.replace(/\.[^.]+$/, '') + '.' + ext;
        return new File([blob], name, { type: blob.type, lastModified: Date.now() });
    } catch (e) {
        return file;
    }
}

/** Transforme un objet en FormData (tableaux/objets encodés en JSON, booléens en 1/0). Les images sont réduites avant l'envoi. */
export async function toFormData(obj, files = {}) {
    const fd = new FormData();
    Object.entries(obj).forEach(([k, v]) => {
        if (v === undefined) return;
        if (typeof v === 'boolean') fd.append(k, v ? '1' : '0');
        else if (Array.isArray(v) || (v && typeof v === 'object')) fd.append(k, JSON.stringify(v));
        else fd.append(k, v ?? '');
    });
    for (const [k, v] of Object.entries(files)) {
        if (Array.isArray(v)) { for (const f of v) fd.append(`${k}[]`, await shrinkImage(f)); }
        else if (v) fd.append(k, await shrinkImage(v));
    }
    return fd;
}

/** Message à afficher après un échec d'enregistrement. */
export const failMessage = (res) => (Object.keys(res.errors || {}).length ? 'Vérifiez les champs signalés.' : res.message || 'Enregistrement impossible. Réessayez.');

export const fmtDate = (d) => d ? new Date(d).toLocaleDateString('fr-FR', { day: '2-digit', month: 'short', year: 'numeric' }) : '—';
