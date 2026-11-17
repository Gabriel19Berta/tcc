import Inputmask from 'inputmask';

export function aplicarMascaraValor(campo) {
    Inputmask.remove(campo);

    const prefixo = campo.dataset.maskPrefix ?? "R$ ";

    Inputmask("currency", {
        prefix: prefixo,
        groupSeparator: ".",
        radixPoint: ",",
        digits: 2,
        digitsOptional: false,
        rightAlign: false,
        removeMaskOnSubmit: true,
        numericInput: true
    }).mask(campo);
}

export function iniMascaras() {
    Inputmask("(99) 99999-9999").mask(document.querySelectorAll(".mask-celular"));
    Inputmask("99999-999").mask(document.querySelectorAll(".mask-cep"));
    Inputmask("(99) 9999-9999").mask(document.querySelectorAll(".mask-telefone"));
    Inputmask("999.999.999-99").mask(document.querySelectorAll(".mask-cpf"));
    Inputmask("**.***.***/****-99").mask(document.querySelectorAll(".mask-cnpj"));

    document.querySelectorAll(".mask-valor").forEach(campo => {
        aplicarMascaraValor(campo);
    });

    window.aplicarMascaraValor = aplicarMascaraValor;
}
