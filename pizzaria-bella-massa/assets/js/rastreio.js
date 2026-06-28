// GRANDE PARTE DO JAVA FOI REVISADO E COMPLEMENTADO PELO CLAUDE OPUS 4.8
document.addEventListener('DOMContentLoaded', () => {
  
  localStorage.removeItem('carrinho');

  const idPedido = document.body.dataset.pedidoId;
  if (!idPedido) return;

  const linhaTempo = document.getElementById('linha-tempo-pedido');
  const textoStatus = document.getElementById('texto-status-atual');

  
  const ordem = ['recebido', 'em_preparo', 'saiu_para_entrega', 'entregue'];
  const textos = {
    recebido: 'Pedido recebido',
    em_preparo: 'Em preparo',
    saiu_para_entrega: 'Saiu para entrega',
    entregue: 'Entregue',
    cancelado: 'Cancelado',
  };

 
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

 
  atualizarLinhaTempo(linhaTempo.dataset.statusAtual);


  async function verificarStatus() {
    try {
      const resposta = await fetch(`actions/status_pedido.php?id=${idPedido}`);
      const dados = await resposta.json();
      if (dados.status) {
        atualizarLinhaTempo(dados.status);
      }
    } catch (erro) {
      
    }
  }

 
  setInterval(verificarStatus, 5000);
});
