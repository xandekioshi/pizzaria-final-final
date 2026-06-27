/**
 * admin-produtos.js
 * - Abre o modal vazio para "Novo produto".
 * - Abre o modal já preenchido para "Editar".
 * - Pede confirmação antes de excluir.
 */
document.addEventListener('DOMContentLoaded', () => {
  const modal = document.getElementById('modal-produto');
  const overlay = document.getElementById('overlay-admin');
  const titulo = document.getElementById('titulo-modal-produto');

  // Campos do formulário.
  const campoId = document.getElementById('produto-id');
  const campoNome = document.getElementById('produto-nome');
  const campoDescricao = document.getElementById('produto-descricao');
  const campoTipo = document.getElementById('produto-tipo');
  const campoPreco = document.getElementById('produto-preco');

  function abrirModal() {
    overlay.hidden = false;
    modal.showModal();
  }
  function fecharModal() {
    overlay.hidden = true;
    modal.close();
  }

  // Novo produto: limpa o formulário.
  document.getElementById('botao-novo-produto').addEventListener('click', () => {
    titulo.textContent = 'Novo produto';
    campoId.value = '';
    campoNome.value = '';
    campoDescricao.value = '';
    campoTipo.value = 'pizza';
    campoPreco.value = '';
    abrirModal();
  });

  // Editar: preenche o formulário com os dados da linha.
  document.querySelectorAll('.botao-editar-produto').forEach((botao) => {
    botao.addEventListener('click', () => {
      const linha = botao.closest('tr');
      titulo.textContent = 'Editar produto';
      campoId.value = linha.dataset.produtoId;
      campoNome.value = linha.dataset.nome;
      campoDescricao.value = linha.dataset.descricao;
      campoTipo.value = linha.dataset.tipo;
      campoPreco.value = linha.dataset.preco;
      abrirModal();
    });
  });

  // Excluir: confirma e redireciona para o script PHP.
  document.querySelectorAll('.botao-excluir-produto').forEach((botao) => {
    botao.addEventListener('click', () => {
      if (confirm('Tem certeza que deseja excluir este produto?')) {
        window.location.href = '../actions/produtos_excluir.php?id=' + botao.dataset.id;
      }
    });
  });

  document.getElementById('botao-fechar-modal-produto').addEventListener('click', fecharModal);
});
