/**
 * admin-pedidos.js
 * - Muda o status de um pedido (chama o PHP).
 * - Abre o modal "Ver detalhes" lendo os dados da própria linha.
 * - Filtra a tabela por status.
 */
document.addEventListener('DOMContentLoaded', () => {
  const modal = document.getElementById('modal-detalhes-pedido');
  const overlay = document.getElementById('overlay-admin');

  function formatarReais(valor) {
    return 'R$ ' + parseFloat(valor).toFixed(2).replace('.', ',');
  }

  // ---- Trocar status ----
  document.querySelectorAll('.seletor-status-pedido').forEach((select) => {
    select.addEventListener('change', async () => {
      const dados = new URLSearchParams();
      dados.append('id', select.dataset.id);
      dados.append('status', select.value);

      try {
        const resposta = await fetch('../actions/atualizar_status.php', {
          method: 'POST',
          body: dados,
        });
        const json = await resposta.json();
        if (json.ok) {
          // Atualiza o data-status da linha (usado no filtro).
          select.closest('tr').dataset.status = select.value;
        } else {
          alert('Não foi possível atualizar o status.');
        }
      } catch (erro) {
        alert('Erro ao atualizar o status.');
      }
    });
  });

  // ---- Ver detalhes ----
  document.querySelectorAll('.botao-ver-detalhes-pedido').forEach((botao) => {
    botao.addEventListener('click', () => {
      const linha = botao.closest('tr');

      document.getElementById('detalhe-numero-pedido').textContent =
        '#' + String(linha.dataset.pedidoId).padStart(4, '0');
      document.getElementById('detalhe-cliente-nome').textContent = linha.dataset.cliente;
      document.getElementById('detalhe-endereco').textContent = linha.dataset.endereco;
      document.getElementById('detalhe-valor-frete').textContent = formatarReais(linha.dataset.frete);
      document.getElementById('detalhe-valor-total').textContent = formatarReais(linha.dataset.total);

      // Lista de itens (vem em JSON no data-itens).
      const itens = JSON.parse(linha.dataset.itens);
      const lista = document.getElementById('detalhe-lista-itens');
      lista.innerHTML = '';
      itens.forEach((it) => {
        const li = document.createElement('li');
        li.className = 'item-carrinho';
        li.innerHTML = `
          <span class="item-carrinho__nome">${it.nome}</span>
          <span class="item-carrinho__quantidade-valor">${it.quantidade}x</span>
          <span class="item-carrinho__subtotal">${formatarReais(it.preco_unitario * it.quantidade)}</span>
        `;
        lista.appendChild(li);
      });

      modal.showModal();
    });
  });

  document.getElementById('botao-fechar-modal-detalhes').addEventListener('click', () => modal.close());

  // ---- Filtro por status ----
  document.getElementById('filtro-status').addEventListener('change', (e) => {
    const filtro = e.target.value;
    document.querySelectorAll('#tabela-pedidos tr').forEach((linha) => {
      if (!linha.dataset.status) return; // linha "nenhum pedido"
      linha.style.display = (filtro === 'todos' || linha.dataset.status === filtro) ? '' : 'none';
    });
  });
});
