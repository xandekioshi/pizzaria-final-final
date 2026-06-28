// GRANDE PARTE DO JAVA FOI REVISADO E COMPLEMENTADO PELO CLAUDE OPUS 4.8
document.addEventListener('DOMContentLoaded', () => {
  const campoCep = document.getElementById('cep');
  const botaoBuscar = document.getElementById('botao-buscar-cep');
  const mensagemErro = document.getElementById('mensagem-erro-cep');

  async function buscarCep() {
  
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

     
      if (dados.erro) {
        mensagemErro.textContent = 'CEP não encontrado.';
        mensagemErro.hidden = false;
        return;
      }

     
      document.getElementById('rua').value = dados.logradouro || '';
      document.getElementById('bairro').value = dados.bairro || '';
      document.getElementById('cidade').value = dados.localidade || '';
      document.getElementById('estado').value = dados.uf || '';

    
      document.getElementById('numero').focus();
    } catch (erro) {
      mensagemErro.textContent = 'Erro ao buscar o CEP. Tente novamente.';
      mensagemErro.hidden = false;
    }
  }

  campoCep.addEventListener('blur', buscarCep);
  botaoBuscar.addEventListener('click', buscarCep);
});
