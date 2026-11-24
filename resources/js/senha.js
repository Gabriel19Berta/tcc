/**
 * Exibir/ocultar senha nos campos do componente x-input (type="password").
 */
export function iniAlternarSenha() {
    document.addEventListener('click', (evento) => {
        const botao = evento.target.closest('[data-alternar-senha]');

        if (!botao || botao.disabled) {
            return;
        }

        const campo = botao.closest('[data-campo-senha]')?.querySelector('input');

        if (!campo) {
            return;
        }

        // Trocar o tipo não altera o valor digitado.
        const visivel = campo.type === 'password';
        const rotulo = visivel ? 'Ocultar senha' : 'Exibir senha';

        campo.type = visivel ? 'text' : 'password';

        botao.setAttribute('aria-label', rotulo);
        botao.setAttribute('title', rotulo);
        botao.setAttribute('aria-pressed', String(visivel));

        // Olho aberto: senha visível. Olho riscado: senha oculta.
        const icone = botao.querySelector('i');
        icone?.classList.toggle('fa-eye', visivel);
        icone?.classList.toggle('fa-eye-slash', !visivel);
    });
}
