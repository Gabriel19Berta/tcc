import './bootstrap';
import 'flowbite';
import Alpine from 'alpinejs';
import { iniMascaras } from './mascaras';
import { consultaCep } from './consultaCep';

window.Alpine = Alpine;

Alpine.start();

document.addEventListener("DOMContentLoaded", () => {
    iniMascaras();
    consultaCep();

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
