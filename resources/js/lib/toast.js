let timer;

/** Petit message de confirmation en bas d'écran. */
export function toast(message, icon = 'fa-circle-check') {
    let el = document.querySelector('.vk-toast');
    if (!el) {
        el = document.createElement('div');
        el.className = 'vk-toast';
        el.setAttribute('role', 'status');
        document.body.appendChild(el);
    }
    el.innerHTML = `<i class="fa-solid ${icon}"></i><span></span>`;
    el.querySelector('span').textContent = message;
    requestAnimationFrame(() => el.classList.add('show'));
    clearTimeout(timer);
    timer = setTimeout(() => el.classList.remove('show'), 4000);
}

window.vkToast = toast;
