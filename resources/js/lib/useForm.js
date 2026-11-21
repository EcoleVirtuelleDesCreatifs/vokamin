import { reactive, ref } from 'vue';
import { send } from './http';
import { toast } from './toast';

/** État commun des formulaires : envoi, erreurs serveur, succès. */
export function useForm(action, initial) {
    const form = reactive({ ...initial, website: '' });
    const errors = reactive({});
    const sending = ref(false);
    const result = ref(null);

    async function submit(extra = {}) {
        Object.keys(errors).forEach((k) => delete errors[k]);
        sending.value = true;
        const fd = new FormData();
        Object.entries(form).forEach(([k, v]) => {
            if (typeof v === 'boolean') fd.append(k, v ? '1' : '');
            else fd.append(k, v ?? '');
        });
        Object.entries(extra).forEach(([k, v]) => { if (v) fd.append(k, v); });
        const res = await send(action, fd);
        sending.value = false;
        if (res.ok) { result.value = res.data; return true; }
        Object.assign(errors, res.errors);
        toast(res.message && !Object.keys(res.errors).length ? res.message : 'Vérifiez les champs signalés.', 'fa-triangle-exclamation');
        return false;
    }

    return { form, errors, sending, result, submit };
}
