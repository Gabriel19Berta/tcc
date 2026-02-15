import Inputmask from 'inputmask';

export function iniMascaras() {
    Inputmask("(99) 99999-9999").mask(document.querySelectorAll(".mask-celular"));
    Inputmask("99999-999").mask(document.querySelectorAll(".mask-cep"));
    Inputmask("(99) 9999-9999").mask(document.querySelectorAll(".mask-telefone"));
    Inputmask("999.999.999-99").mask(document.querySelectorAll(".mask-cpf"));
    Inputmask("99.999.999/9999-99").mask(document.querySelectorAll(".mask-cnpj"));

    const cpfField = document.getElementById("cpf-field");
    const cnpjField = document.getElementById("cnpj-field");

    const tipoRadios = document.querySelectorAll("input[name='tipo']");

    const cpfInput = document.getElementById("cpf");
    const cnpjInput = document.getElementById("cnpj");

    const cpfMask = new Inputmask("999.999.999-99");
    const cnpjMask = new Inputmask("99.999.999/9999-99");

    function toggleDocumento(tipo) {
        if (tipo === "f") {
            cpfField.classList.remove("hidden");
            cnpjField.classList.add("hidden");

            cnpjInput.value = "";
            cnpjMask.remove(cnpjInput);
            cpfMask.mask(cpfInput);
        } else {
            cnpjField.classList.remove("hidden");
            cpfField.classList.add("hidden");

            cpfInput.value = "";
            cpfMask.remove(cpfInput);
            cnpjMask.mask(cnpjInput);
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
}