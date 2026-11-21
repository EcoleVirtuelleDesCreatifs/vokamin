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

export const post = (path, body) => send(`${base}${path}`, body, 'POST');
export const patch = (path, body) => send(`${base}${path}`, body, 'PATCH');
export const del = (path) => send(`${base}${path}`, null, 'DELETE');

/** Transforme un objet en FormData (tableaux/objets encodés en JSON, booléens en 1/0). */
export function toFormData(obj, files = {}) {
    const fd = new FormData();
    Object.entries(obj).forEach(([k, v]) => {
        if (v === undefined) return;
        if (typeof v === 'boolean') fd.append(k, v ? '1' : '0');
        else if (Array.isArray(v) || (v && typeof v === 'object')) fd.append(k, JSON.stringify(v));
        else fd.append(k, v ?? '');
    });
    Object.entries(files).forEach(([k, v]) => {
        if (Array.isArray(v)) v.forEach((f) => fd.append(`${k}[]`, f));
        else if (v) fd.append(k, v);
    });
    return fd;
}

export const fmtDate = (d) => d ? new Date(d).toLocaleDateString('fr-FR', { day: '2-digit', month: 'short', year: 'numeric' }) : '—';
