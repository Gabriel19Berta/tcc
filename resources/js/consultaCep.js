export function consultaCep() {

    const cepInput = document.getElementById("cep");

    function hideLoader() {
        document.getElementById("loader").classList.add("hidden");
    }

    if (cepInput) {
        cepInput.addEventListener("blur", function () {
            pesquisacep(this.value);
        });
    }

    function limpa_formulário_cep() {
        document.getElementById('logradouro').value = ("");
        document.getElementById('bairro').value = ("");
        document.getElementById('cidade').value = ("");
        document.getElementById('uf').value = ("");

    }

    window.meu_callback = function meu_callback(conteudo) {
        if (!("erro" in conteudo)) {
            hideLoader();

            document.getElementById('logradouro').value = (conteudo.logradouro);
            document.getElementById('bairro').value = (conteudo.bairro);
            document.getElementById('cidade').value = (conteudo.localidade);
            document.getElementById('uf').value = (conteudo.uf);
        }
        else {
            hideLoader();
            alert("CEP não encontrado.");
        }
    }

    function pesquisacep(valor) {
        var cep = valor.replace(/\D/g, '');

        if (cep != "") {
            var validacep = /^[0-9]{8}$/;

            if (validacep.test(cep)) {

                // Adiciona loading
                document.getElementById("loader").classList.remove("hidden");

                //Cria um elemento javascript.
                var script = document.createElement('script');

                //Sincroniza com o callback.
                script.src = 'https://viacep.com.br/ws/' + cep + '/json/?callback=meu_callback';

                //Insere script no documento e carrega o conteúdo.
                document.body.appendChild(script);

            }
            else {
                hideLoader();
                alert("Formato de CEP inválido.");
            }
        }
        else {
            hideLoader();
            limpa_formulário_cep();
        }
    };
}
