import Inputmask from 'inputmask';

export function iniMascaras() {

    const masks = [
        {id: "celular", mask: "(99) 99999-9999"},
        {id: "telefone", mask: "(99) 9999-9999"},
        {id: "cep", mask: "99999-999"},
        {id: "cpf", mask: "999.999.999-99"},
        {id: "cnpj", mask: "99.999.999/9999-99"},
    ];

    masks.forEach(item => {
        const campo = document.getElementById(item.id)

        if (campo) {
            Inputmask(item.mask).mask(campo);
        }
    });


}