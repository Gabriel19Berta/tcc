const MENSAGEM = 'A data final deve ser igual ou posterior à data inicial.';

/**
 * Filtros com data inicial e final: a data final não aceita data anterior
 * à inicial (atributo "min") e fica sinalizada quando o intervalo é inválido,
 * impedindo o envio do formulário pela validação nativa do navegador.
 *
 * Uso: no campo da data final, informe o id do campo da data inicial.
 *     <x-input id="data_fim" type="date" data-data-inicial="data_inicio" ... />
 *
 * Complementa (não substitui) a validação do backend.
 */
export function iniIntervaloDatas() {
    document.querySelectorAll('[data-data-inicial]').forEach(campoFim => {
        const campoInicio = document.getElementById(campoFim.dataset.dataInicial);

        if (!campoInicio) {
            return;
        }

        function validar(sinalizar) {
            const inicio = campoInicio.value;
            const fim = campoFim.value;

            // Datas no formato AAAA-MM-DD: a comparação de texto respeita a ordem cronológica.
            const invalido = inicio !== '' && fim !== '' && fim < inicio;

            campoFim.min = inicio;
            campoFim.setCustomValidity(invalido ? MENSAGEM : '');
            campoFim.classList.toggle('border-danger-500', invalido);
            campoFim.classList.toggle('border-gray-300', !invalido);

            if (invalido && sinalizar) {
                campoFim.reportValidity();
            }
        }

        campoInicio.addEventListener('change', () => validar(true));
        campoFim.addEventListener('change', () => validar(true));

        validar(false);
    });
}
