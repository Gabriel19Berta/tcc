import './bootstrap';
import Alpine from 'alpinejs';
import { iniMascaras } from './mascaras'

window.Alpine = Alpine;

Alpine.start();

document.addEventListener("DOMContentLoaded", () => {
    iniMascaras();
});

document.addEventListener('input', function (event) {
    const input = event.target;

    if (!input.matches('.input-form')) return;

    const wrapper = input.closest('div');
    const errorText = wrapper.querySelector('.input-error');

    if (!errorText) return;

    input.classList.remove(
        'border-danger-500',
        'focus:border-danger-500',
        'focus:ring-danger-500'
    );

    input.classList.add(
        'border-gray-300',
        'focus:border-primary',
        'focus:ring-primary'
    );

    errorText.remove();
});
