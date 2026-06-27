/**
 * rastreio.js  (Extra 3)
 * Pergunta ao PHP, de tempos em tempos, qual é o status do pedido e
 * atualiza a linha do tempo sem recarregar a página.
 */
document.addEventListener('DOMContentLoaded', () => {
  // O pedido foi concluído: pode limpar o carrinho guardado.
  localStorage.removeItem('carrinho');

  const idPedido = document.body.dataset.pedidoId;
  if (!idPedido) return;

  const linhaTempo = document.getElementById('linha-tempo-pedido');
  const textoStatus = document.getElementById('texto-status-atual');

  // Ordem das etapas e o texto de cada status.
  const ordem = ['recebido', 'em_preparo', 'saiu_para_entrega', 'entregue'];
  const textos = {
    recebido: 'Pedido recebido',
    em_preparo: 'Em preparo',
    saiu_para_entrega: 'Saiu para entrega',
    entregue: 'Entregue',
    cancelado: 'Cancelado',
  };

  // Pinta a linha do tempo de acordo com o status atual.
  function atualizarLinhaTempo(statusAtual) {
    linhaTempo.dataset.statusAtual = statusAtual;
    textoStatus.textContent = textos[statusAtual] || statusAtual;

    const indiceAtual = ordem.indexOf(statusAtual);
    linhaTempo.querySelectorAll('.etapa').forEach((etapa) => {
      const indiceEtapa = ordem.indexOf(etapa.dataset.status);
      etapa.classList.remove('etapa--ativa', 'etapa--concluida');

      if (indiceEtapa < indiceAtual) {
        etapa.classList.add('etapa--concluida');
      } else if (indiceEtapa === indiceAtual) {
        etapa.classList.add('etapa--ativa');
      }
    });
  }

  // Marca o estado inicial (veio do PHP).
  atualizarLinhaTempo(linhaTempo.dataset.statusAtual);

  // Consulta o status no servidor.
  async function verificarStatus() {
    try {
      const resposta = await fetch(`actions/status_pedido.php?id=${idPedido}`);
      const dados = await resposta.json();
      if (dados.status) {
        atualizarLinhaTempo(dados.status);
      }
    } catch (erro) {
      // Se der erro, simplesmente tenta de novo na próxima vez.
    }
  }

  // Repete a cada 5 segundos.
  setInterval(verificarStatus, 5000);
});
