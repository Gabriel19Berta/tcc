import './bootstrap';
import 'flowbite';

import $ from 'jquery';
window.$ = window.jQuery = $;

import select2 from 'select2';
/* Inicializa o plugin Select2 no jQuery */
select2();

import Alpine from 'alpinejs';
import { iniMascaras } from './mascaras';
import { consultaCep } from './consultaCep';

window.Alpine = Alpine;

Alpine.start();

document.addEventListener("DOMContentLoaded", () => {
    iniMascaras();
    consultaCep();

    $(document).ready(function () {
        $('.select2').select2({
            placeholder: 'Selecione uma opção',
            allowClear: true,
            language: {
                noResults: function () {
                    return "Nenhum resultado encontrado";
                },
                searching: function () {
                    return "Pesquisando...";
                },
                inputTooShort: function (args) {
                    return `Digite ${args.minimum - args.input.length} ou mais caracteres`;
                }
            },
        });
    });

    /* ADICIONE/REMOVE loading */
    function showLoader() {
        document.getElementById("loader").classList.remove("hidden");
    }

    function hideLoader() {
        document.getElementById("loader").classList.add("hidden");
    }

    document.querySelectorAll("a").forEach(link => {
        link.addEventListener("click", function () {
            const href = link.getAttribute("href");

            if (!href || href.startsWith("#") || link.target === "_blank") return;

            showLoader();
        });
    });

    document.querySelectorAll("form").forEach(form => {
        form.addEventListener("submit", function (e) {
            if (!form.checkValidity()) return;

            showLoader();
        });
    });

    const originalFetch = window.fetch;

    window.fetch = async function (...args) {
        showLoader();

        try {
            const response = await originalFetch(...args);
            return response;
        } finally {
            hideLoader();
        }
    };

    window.addEventListener("load", () => {
        hideLoader();
    });
    

    // Controla a exibição dos campos com base no tipo de pessoa (Física ou Jurídica)
    const cpfField = document.getElementById("cpf-field");
    const cnpjField = document.getElementById("cnpj-field");
    const rgField = document.getElementById("rg-field");
    const ieField = document.getElementById("ie-field");

    const tipoRadios = document.querySelectorAll("input[name='tipo']");
    const cpfInput = document.getElementById("cpf");
    const cnpjInput = document.getElementById("cnpj");
    const rgInput = document.getElementById("rg");
    const ieInput = document.getElementById("ie");
    const dtNasc = document.getElementById("data-nascimento")

    function toggleDocumento(tipoPessoa) {
        if (tipoPessoa === "f") {
            cpfField.classList.remove("hidden");
            rgField.classList.remove("hidden");
            dtNasc.classList.remove("hidden");
            cnpjField.classList.add("hidden");
            ieField.classList.add("hidden");

            cnpjInput.value = "";
            ieInput.value = "";
        } else {
            cnpjField.classList.remove("hidden");
            ieField.classList.remove("hidden");
            cpfField.classList.add("hidden");
            rgField.classList.add("hidden");
            dtNasc.classList.add("hidden");

            dtNasc.value = "";
            cpfInput.value = "";
            rgInput.value = "";
        }
    }

    // Inicial (valor padrão ou old)
    const checked = document.querySelector("input[name='tipo']:checked");
    if (checked) {
        toggleDocumento(checked.value);
    }

    // Listener
    tipoRadios.forEach(radio => {
        radio.addEventListener("change", e => {
            toggleDocumento(e.target.value);
        });
    });

    // Alert ao excluir registro
    document.querySelectorAll('.form-delete').forEach(function (form) {
        form.addEventListener('submit', function (e) {
            if(!confirm('Tem certeza que deseja excluir?')) {
                e.preventDefault();
            }
        });
    });

    // Alert ao alterar status
    document.querySelectorAll('.form-toggle-status').forEach(function (form) {
        form.addEventListener('submit', function (e) {
            const status = form.querySelector('button').textContent.trim() === 'Ativo';

            const mensagem = status ? 'Tem certeza que deseja inativar?': 'Tem certeza que deseja ativar?';

            if(!confirm(mensagem)) {
                e.preventDefault();
            }
        })
    })
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
