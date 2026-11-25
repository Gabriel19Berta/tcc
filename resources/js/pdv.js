import $ from 'jquery';
import { aplicarMascaraValor } from './mascaras';

/**
 * Comportamentos da tela do PDV (vendas/pdv.blade.php).
 *
 * A tela continua sendo renderizada pelo Blade e as operações continuam sendo
 * formulários tradicionais; aqui ficam apenas os eventos da interface
 * (abas, preço do produto, cliente, quantidade dos itens e o salvamento
 * automático de desconto/acréscimo).
 *
 * Só executa quando a tela do PDV está presente (elemento #pdv).
 */
export function iniciarPdv() {
    const pdv = document.getElementById('pdv');

    if (!pdv) {
        return;
    }

    iniciarAbas(pdv.dataset.abaInicial || 'produto');
    iniciarPrecoProduto();
    iniciarCliente();
    iniciarQuantidadeItens();
    iniciarValores();
}

/**
 * Abas Produto / Cliente / Pagamento. A aba inicial vem do backend
 * (sessão "aba_ativa" após um redirecionamento).
 */
function iniciarAbas(abaInicial) {
    const abas = ['produto', 'cliente', 'pagamento'];

    function alternarAba(abaSelecionada) {
        abas.forEach(function (aba) {
            const botao = document.querySelector(`[data-aba="${aba}"]`);
            const conteudo = document.getElementById(`aba-${aba}`);

            const selecionada = aba === abaSelecionada;

            // Atualiza o conteúdo visível.
            conteudo.classList.toggle('hidden', !selecionada);

            // Atualiza a aparência do botão.
            botao.classList.toggle('bg-primary', selecionada);
            botao.classList.toggle('text-white', selecionada);
            botao.classList.toggle('rounded-full', selecionada);

            botao.setAttribute('aria-selected', selecionada);
        });
    }

    document.querySelectorAll('[data-aba]').forEach(function (botao) {
        botao.addEventListener('click', function () {
            alternarAba(this.dataset.aba);
        });
    });

    alternarAba(abaInicial);
}

/**
 * Ao escolher um produto, preenche o preço com o preço de venda cadastrado.
 */
function iniciarPrecoProduto() {
    const precoInput = document.getElementById('preco_produto');

    $('#produto_id').on('change', function () {
        const opcaoSelecionada = this.options[this.selectedIndex];

        if (!opcaoSelecionada.value) {
            precoInput.value = '';
            return;
        }

        const preco = Number(opcaoSelecionada.dataset.preco);

        precoInput.value = preco.toLocaleString('pt-BR', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });

        precoInput.dispatchEvent(new Event('input', {
            bubbles: true
        }));
    });
}

/**
 * Cliente selecionado (Select2): salva imediatamente.
 */
function iniciarCliente() {
    if (!document.getElementById('cliente_id')) {
        return;
    }

    $(document).on('change', '#cliente_id', function () {
        const formulario = document.getElementById('form-cliente');

        if (formulario) {
            formulario.requestSubmit();
        }
    });
}

/**
 * Quantidade dos itens: envia ao sair do campo, somente se mudou e é válida.
 */
function iniciarQuantidadeItens() {
    document.querySelectorAll('[data-quantidade-item]').forEach(function (input) {
        input.addEventListener('blur', function () {
            // Não envia se o valor não foi alterado.
            if (input.value === input.dataset.valorOriginal) {
                return;
            }

            // Não envia campos vazios ou inválidos.
            if (!input.checkValidity()) {
                input.reportValidity();
                return;
            }

            input.form.requestSubmit();
        });
    });
}

/**
 * Desconto e acréscimo: salvamento automático (fetch + JSON) ao perder o foco
 * ou trocar o tipo de desconto, atualizando o resumo e o total do rodapé.
 */
function iniciarValores() {
    const formValores = document.getElementById('form-valores');

    if (!formValores) {
        return;
    }

    const tipoDesconto = document.getElementById('tipo_desconto');
    const campoDesconto = document.getElementById('desconto');
    const campoAcrescimo = document.getElementById('outros_custos');

    let temporizador = null;
    let salvando = false;
    let alterado = false;
    let salvamentoPendente = false;

    // Alterna o prefixo da máscara conforme o tipo de desconto.
    function atualizarTipoDesconto() {
        const percentual = tipoDesconto.value === 'percentual';

        document.getElementById('label-desconto').textContent =
            percentual ? 'Desconto (%)' : 'Desconto (R$)';

        campoDesconto.dataset.maskPrefix = percentual ? '' : 'R$ ';
        campoDesconto.placeholder = percentual ? '0,00' : 'R$ 0,00';

        aplicarMascaraValor(campoDesconto);
    }

    // Limpa mensagens de erro ao editar o campo.
    function limparErro(campo) {
        const erro = document.getElementById(campo);

        if (erro) {
            erro.textContent = '';
        }
    }

    // Exibe os valores (já formatados pelo backend) no resumo e no rodapé.
    function atualizarResumo(dados) {
        document.getElementById('resumo-produtos').textContent =
            'R$ ' + dados.valor_produtos;

        document.getElementById('resumo-desconto').textContent =
            '- R$ ' + dados.descontos;

        document.getElementById('resumo-acrescimo').textContent =
            'R$ ' + dados.outros_custos;

        document.getElementById('resumo-total').textContent =
            'R$ ' + dados.valor_total;

        const totalRodape = document.getElementById('total-venda');

        if (totalRodape) {
            totalRodape.textContent = 'R$ ' + dados.valor_total;
        }

        const valorRestante = document.getElementById('valor-restante');

        if (valorRestante) {
            valorRestante.textContent = 'R$ ' + dados.valor_restante;
        }
    }

    function exibirErros(erros) {
        const campos = {
            tipo_desconto: 'erro-tipo-desconto',
            desconto: 'erro-desconto',
            outros_custos: 'erro-outros-custos'
        };

        Object.entries(campos).forEach(([campo, elemento]) => {
            const mensagem = erros[campo]?.[0] ?? '';

            document.getElementById(elemento).textContent = mensagem;
        });
    }

    async function salvarValores() {
        if (!alterado) {
            return;
        }

        // Já existe um salvamento em andamento: repete ao terminar.
        if (salvando) {
            salvamentoPendente = true;
            return;
        }

        salvando = true;
        alterado = false;

        const status = document.getElementById('status-salvamento');
        status.textContent = 'Salvando...';

        try {
            const resposta = await fetch(formValores.action, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: new FormData(formValores)
            });

            const dados = await resposta.json();

            if (resposta.status === 422) {
                exibirErros(dados.errors ?? {});
                status.textContent = 'Não foi possível salvar os valores.';
                return;
            }

            if (!resposta.ok) {
                throw new Error('Erro ao salvar os valores.');
            }

            exibirErros({});
            atualizarResumo(dados);

            status.textContent = 'Valores salvos com sucesso.';
        } catch (erro) {
            status.textContent = 'Erro ao salvar. Tente novamente.';
            alterado = true;
        } finally {
            salvando = false;

            if (salvamentoPendente) {
                salvamentoPendente = false;
                agendarSalvamento();
            }
        }
    }

    function agendarSalvamento() {
        clearTimeout(temporizador);

        temporizador = setTimeout(salvarValores, 400);
    }

    tipoDesconto.addEventListener('change', function () {
        atualizarTipoDesconto();
        alterado = true;
        agendarSalvamento();
        limparErro('erro-tipo-desconto');
    });

    campoDesconto.addEventListener('input', function () {
        limparErro('erro-desconto');
        alterado = true;
    });

    campoAcrescimo.addEventListener('input', function () {
        limparErro('erro-outros-custos');
        alterado = true;
    });

    // Salva ao perder o foco dos campos.
    [campoDesconto, campoAcrescimo].forEach(campo => {
        campo.addEventListener('blur', agendarSalvamento);
    });

    // Enquanto o usuário digita, adia o salvamento agendado (salva no próximo blur).
    formValores.addEventListener('input', function () {
        clearTimeout(temporizador);
    });

    atualizarTipoDesconto();
}
