/**
 * viacep.js
 * Integração com a API pública do ViaCEP.
 * Ao sair do campo CEP (ou clicar em "Buscar CEP"), busca o endereço
 * e preenche Rua, Bairro, Cidade e Estado automaticamente.
 */
document.addEventListener('DOMContentLoaded', () => {
  const campoCep = document.getElementById('cep');
  const botaoBuscar = document.getElementById('botao-buscar-cep');
  const mensagemErro = document.getElementById('mensagem-erro-cep');

  async function buscarCep() {
    // Tira tudo que não for número.
    const cep = campoCep.value.replace(/\D/g, '');
    mensagemErro.hidden = true;

    if (cep.length !== 8) {
      mensagemErro.textContent = 'Digite um CEP com 8 dígitos.';
      mensagemErro.hidden = false;
      return;
    }

    try {
      const resposta = await fetch(`https://viacep.com.br/ws/${cep}/json/`);
      const dados = await resposta.json();

      // O ViaCEP devolve { erro: true } quando o CEP não existe.
      if (dados.erro) {
        mensagemErro.textContent = 'CEP não encontrado.';
        mensagemErro.hidden = false;
        return;
      }

      // Preenche os campos.
      document.getElementById('rua').value = dados.logradouro || '';
      document.getElementById('bairro').value = dados.bairro || '';
      document.getElementById('cidade').value = dados.localidade || '';
      document.getElementById('estado').value = dados.uf || '';

      // Foca no número, que o usuário precisa digitar.
      document.getElementById('numero').focus();
    } catch (erro) {
      mensagemErro.textContent = 'Erro ao buscar o CEP. Tente novamente.';
      mensagemErro.hidden = false;
    }
  }

  campoCep.addEventListener('blur', buscarCep);
  botaoBuscar.addEventListener('click', buscarCep);
});
