/**
 * carrinho.js
 * Controla o carrinho de compras: adicionar, remover, mudar quantidade,
 * calcular total e guardar no localStorage (para a página de checkout ler).
 */

const CHAVE_CARRINHO = 'carrinho';

// Lê o carrinho do localStorage (ou um array vazio).
function lerCarrinho() {
  return JSON.parse(localStorage.getItem(CHAVE_CARRINHO)) || [];
}

// Salva o carrinho no localStorage.
function salvarCarrinho(carrinho) {
  localStorage.setItem(CHAVE_CARRINHO, JSON.stringify(carrinho));
}

// Adiciona um produto (ou aumenta a quantidade se já existir).
function adicionarItem(produto) {
  const carrinho = lerCarrinho();
  const existente = carrinho.find((item) => item.id === produto.id);

  if (existente) {
    existente.quantidade += 1;
  } else {
    carrinho.push({ ...produto, quantidade: 1 });
  }

  salvarCarrinho(carrinho);
  renderizarCarrinho();
  abrirCarrinho();
}

// Muda a quantidade de um item; se chegar a 0, remove.
function mudarQuantidade(id, delta) {
  let carrinho = lerCarrinho();
  const item = carrinho.find((i) => i.id === id);
  if (!item) return;

  item.quantidade += delta;
  if (item.quantidade <= 0) {
    carrinho = carrinho.filter((i) => i.id !== id);
  }

  salvarCarrinho(carrinho);
  renderizarCarrinho();
}

// Remove um item do carrinho.
function removerItem(id) {
  const carrinho = lerCarrinho().filter((i) => i.id !== id);
  salvarCarrinho(carrinho);
  renderizarCarrinho();
}

// Limpa o carrinho inteiro.
function limparCarrinho() {
  salvarCarrinho([]);
  renderizarCarrinho();
}

// Desenha o carrinho na tela.
function renderizarCarrinho() {
  const carrinho = lerCarrinho();
  const lista = document.getElementById('lista-itens-carrinho');
  const mensagemVazio = document.getElementById('mensagem-carrinho-vazio');
  const contador = document.getElementById('contador-itens-carrinho');

  lista.innerHTML = '';
  let subtotal = 0;
  let totalItens = 0;

  carrinho.forEach((item) => {
    subtotal += item.preco * item.quantidade;
    totalItens += item.quantidade;

    const li = document.createElement('li');
    li.className = 'item-carrinho';
    li.innerHTML = `
      <span class="item-carrinho__nome">${item.nome}</span>
      <div class="item-carrinho__quantidade">
        <button type="button" class="botao-diminuir-quantidade" data-id="${item.id}">-</button>
        <span class="item-carrinho__quantidade-valor">${item.quantidade}</span>
        <button type="button" class="botao-aumentar-quantidade" data-id="${item.id}">+</button>
      </div>
      <span class="item-carrinho__subtotal">R$ ${(item.preco * item.quantidade).toFixed(2).replace('.', ',')}</span>
      <button type="button" class="botao-remover-item" data-id="${item.id}" aria-label="Remover item">&times;</button>
    `;
    lista.appendChild(li);
  });

  // Mostra/esconde a mensagem de carrinho vazio.
  mensagemVazio.style.display = carrinho.length === 0 ? 'block' : 'none';

  // Atualiza contador e totais.
  contador.textContent = totalItens;
  document.getElementById('subtotal-carrinho').textContent =
    'R$ ' + subtotal.toFixed(2).replace('.', ',');

  const total = document.getElementById('total-carrinho');
  total.textContent = 'R$ ' + subtotal.toFixed(2).replace('.', ',');
  total.dataset.total = subtotal.toFixed(2);
}

// Abre e fecha o carrinho lateral.
function abrirCarrinho() {
  document.getElementById('carrinho-lateral').hidden = false;
  document.getElementById('overlay').hidden = false;
}
function fecharCarrinho() {
  document.getElementById('carrinho-lateral').hidden = true;
  document.getElementById('overlay').hidden = true;
}

// ---------- Liga os eventos quando a página carrega ----------
document.addEventListener('DOMContentLoaded', () => {
  renderizarCarrinho();

  // Botões "Adicionar" dos produtos.
  document.querySelectorAll('.botao-adicionar-carrinho').forEach((botao) => {
    botao.addEventListener('click', () => {
      adicionarItem({
        id: parseInt(botao.dataset.id, 10),
        nome: botao.dataset.nome,
        preco: parseFloat(botao.dataset.preco),
        tipo: botao.dataset.tipo,
      });
    });
  });

  // Cliques dentro da lista do carrinho (delegação de evento).
  document.getElementById('lista-itens-carrinho').addEventListener('click', (e) => {
    const id = parseInt(e.target.dataset.id, 10);
    if (e.target.classList.contains('botao-aumentar-quantidade')) mudarQuantidade(id, 1);
    if (e.target.classList.contains('botao-diminuir-quantidade')) mudarQuantidade(id, -1);
    if (e.target.classList.contains('botao-remover-item')) removerItem(id);
  });

  // Abrir/fechar carrinho.
  document.getElementById('botao-abrir-carrinho').addEventListener('click', abrirCarrinho);
  document.getElementById('botao-fechar-carrinho').addEventListener('click', fecharCarrinho);
  document.getElementById('overlay').addEventListener('click', fecharCarrinho);

  // Limpar carrinho.
  document.getElementById('botao-limpar-carrinho').addEventListener('click', limparCarrinho);

  // Finalizar pedido: precisa estar logado.
  document.getElementById('link-finalizar-pedido').addEventListener('click', (e) => {
    const logado = document.body.dataset.usuarioLogado === 'true';
    if (lerCarrinho().length === 0) {
      e.preventDefault();
      alert('Seu carrinho está vazio.');
      return;
    }
    if (!logado) {
      e.preventDefault();
      alert('Você precisa entrar na sua conta para finalizar o pedido.');
      window.location.href = 'login.php';
    }
  });
});
