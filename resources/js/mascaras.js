import Inputmask from 'inputmask';

export function iniMascaras() {
    Inputmask("(99) 99999-9999").mask(document.querySelectorAll(".mask-celular"));
    Inputmask("99999-999").mask(document.querySelectorAll(".mask-cep"));
    Inputmask("(99) 9999-9999").mask(document.querySelectorAll(".mask-telefone"));
    Inputmask("999.999.999-99").mask(document.querySelectorAll(".mask-cpf"));
    Inputmask("99.999.999/9999-99").mask(document.querySelectorAll(".mask-cnpj"));

    // Máscara para valor (R$)
    Inputmask("currency", {
        prefix: "R$ ",         
        groupSeparator: ".", // Separador de milhar 
        radixPoint: ",",     // Separador decimal
        digits: 2,
        digitsOptional: false,
        autoGroup: true,
        rightAlign: false,
        removeMaskOnSubmit: true
    }).mask(document.querySelectorAll(".mask-valor"))
}