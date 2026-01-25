import './bootstrap';

import Alpine from 'alpinejs';
import Inputmask from 'inputmask';

window.Alpine = Alpine;

Alpine.start();

document.addEventListener('DOMContentLoaded', () => {
    Inputmask("999.999.999-99").mask(document.querySelectorAll(".cpf"));

    Inputmask("99.999.999/9999-99").mask(document.querySelectorAll(".cnpj"));

    Inputmask("(99) 99999-9999").mask(document.querySelectorAll(".celular"));

    Inputmask("99999-999").mask(document.querySelectorAll(".cep"));
});