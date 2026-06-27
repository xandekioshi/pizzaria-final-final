<?php
session_start();
require_once __DIR__ . '/config/database.php';

// Busca os produtos no banco e separa por tipo.
$pizzas  = $pdo->query("SELECT * FROM produtos WHERE tipo = 'pizza'  ORDER BY id")->fetchAll();
$bebidas = $pdo->query("SELECT * FROM produtos WHERE tipo = 'bebida' ORDER BY id")->fetchAll();

// Dados do usuário para os "ganchos" do <body> (lidos pelo JS).
$logado = isset($_SESSION['usuario_id']) ? 'true' : 'false';
$admin  = !empty($_SESSION['is_admin']) ? 'true' : 'false';
$nome   = $_SESSION['usuario_nome'] ?? '';

/**
 * Pequena função para desenhar um card de produto.
 * Como a tabela "produtos" não guarda imagem, usamos um emoji
 * de acordo com o tipo (pizza ou bebida).
 */
function cardProduto($p) {
    $emoji = $p['tipo'] === 'pizza' ? '🍕' : '🥤';
    $preco = number_format($p['preco'], 2, ',', '.');
    ?>
    <article class="produto-card" data-produto-id="<?php echo $p['id']; ?>">
      <div class="produto-card__imagem" aria-hidden="true"><?php echo $emoji; ?></div>
      <div class="produto-card__corpo">
        <h4 class="produto-card__nome"><?php echo htmlspecialchars($p['nome']); ?></h4>
        <p class="produto-card__descricao"><?php echo htmlspecialchars($p['descricao']); ?></p>
        <div class="produto-card__rodape">
          <span class="produto-card__preco" data-preco="<?php echo $p['preco']; ?>">R$ <?php echo $preco; ?></span>
          <button type="button" class="botao botao--secundario botao-adicionar-carrinho"
                  data-id="<?php echo $p['id']; ?>"
                  data-nome="<?php echo htmlspecialchars($p['nome']); ?>"
                  data-preco="<?php echo $p['preco']; ?>"
                  data-tipo="<?php echo $p['tipo']; ?>">
            Adicionar
          </button>
        </div>
      </div>
    </article>
    <?php
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Pizzaria Bella Massa - Cardápio</title>
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body data-usuario-logado="<?php echo $logado; ?>"
      data-usuario-admin="<?php echo $admin; ?>"
      data-usuario-nome="<?php echo htmlspecialchars($nome); ?>">

  <?php include __DIR__ . '/includes/header.php'; ?>

  <main id="conteudo-principal">

    <!-- HERO -->
    <section class="hero" aria-label="Apresentação">
      <div class="container hero__conteudo">
        <h1 class="hero__titulo">Pizza boa é a que chega quentinha.</h1>
        <p class="hero__subtitulo">Monte seu pedido, escolha o sabor e acompanhe a entrega em tempo real.</p>
        <a href="#cardapio" class="botao botao--primario">Ver cardápio</a>
      </div>
    </section>

    <!-- CARDÁPIO -->
    <section id="cardapio" class="cardapio">
      <div class="container">
        <h2 class="titulo-secao">Cardápio</h2>

        <div class="cardapio__abas" role="tablist" aria-label="Categorias do cardápio">
          <button type="button" class="cardapio__aba is-ativa" data-categoria="pizzas" role="tab" aria-selected="true">Pizzas</button>
          <button type="button" class="cardapio__aba" data-categoria="bebidas" role="tab" aria-selected="false">Bebidas</button>
        </div>

        <!-- PIZZAS (vindas do banco) -->
        <div class="categoria-produtos" id="categoria-pizzas" data-categoria="pizzas">
          <h3 class="categoria-produtos__titulo">Pizzas</h3>
          <div class="produtos-grid" id="lista-pizzas">
            <?php foreach ($pizzas as $p) cardProduto($p); ?>
          </div>
        </div>

        <!-- BEBIDAS (vindas do banco) -->
        <div class="categoria-produtos" id="categoria-bebidas" data-categoria="bebidas" hidden>
          <h3 class="categoria-produtos__titulo">Bebidas</h3>
          <div class="produtos-grid" id="lista-bebidas">
            <?php foreach ($bebidas as $b) cardProduto($b); ?>
          </div>
        </div>

      </div>
    </section>

    <!-- EXTRA 4: GERADOR DE SABOR COM IA -->
    <section id="gerador-ia" class="gerador-ia">
      <div class="container">
        <h2 class="titulo-secao">Crie sua pizza com Inteligência Artificial</h2>
        <p>Escolha 3 ingredientes e deixe a IA inventar um nome e uma descrição para a sua pizza personalizada.</p>
        <button type="button" id="botao-abrir-modal-ia" class="botao botao--primario">Criar pizza com IA</button>
      </div>
    </section>

    <dialog id="modal-ia" class="modal-ia">
      <form method="dialog" id="form-gerador-ia" class="modal-ia__form">
        <header class="modal-ia__cabecalho">
          <h3>Monte sua pizza personalizada</h3>
          <button type="button" id="botao-fechar-modal-ia" aria-label="Fechar">&times;</button>
        </header>

        <p>Selecione exatamente 3 ingredientes:</p>
        <fieldset class="lista-ingredientes">
          <?php
          $ingredientes = ['Mussarela','Calabresa','Bacon','Catupiry','Cebola','Tomate Seco',
                           'Rúcula','Champignon','Pepperoni','Abacaxi','Milho','Azeitona'];
          foreach ($ingredientes as $ing) {
              echo '<label><input type="checkbox" name="ingrediente" value="'.$ing.'"> '.$ing.'</label>';
          }
          ?>
        </fieldset>

        <p id="mensagem-erro-ia" class="mensagem-erro" hidden></p>

        <button type="button" id="botao-gerar-pizza-ia" class="botao botao--primario">Gerar nome e descrição</button>

        <div id="resultado-pizza-ia" class="resultado-pizza-ia" hidden>
          <h4 id="nome-pizza-ia"></h4>
          <p id="descricao-pizza-ia"></p>
          <p>Preço: <span id="preco-pizza-ia" data-preco="">R$ 0,00</span></p>
          <button type="button" id="botao-adicionar-pizza-ia" class="botao botao--secundario"
                  data-id="" data-nome="" data-preco="" data-tipo="pizza">
            Adicionar ao carrinho
          </button>
        </div>
      </form>
    </dialog>

    <!-- SOBRE -->
    <section id="sobre" class="sobre">
      <div class="container">
        <h2 class="titulo-secao">Sobre a Bella Massa</h2>
        <p>Pizzaria de bairro feita com ingredientes frescos e massa de fermentação lenta.</p>
      </div>
    </section>

    <!-- CONTATO -->
    <section id="contato" class="contato">
      <div class="container">
        <h2 class="titulo-secao">Contato</h2>
        <p>Telefone/WhatsApp: (67) 90000-0000</p>
        <p>Endereço: Rua das Pizzas, 123 - Campo Grande/MS</p>
      </div>
    </section>

  </main>

  <!-- CARRINHO LATERAL (controlado 100% pelo JavaScript) -->
  <aside id="carrinho-lateral" class="carrinho-lateral" aria-label="Carrinho de compras" hidden>
    <div class="carrinho-lateral__topo">
      <h2>Seu pedido</h2>
      <button type="button" id="botao-fechar-carrinho" aria-label="Fechar carrinho">&times;</button>
    </div>

    <ul id="lista-itens-carrinho" class="lista-itens-carrinho"></ul>
    <p id="mensagem-carrinho-vazio" class="mensagem-carrinho-vazio">Seu carrinho está vazio.</p>

    <div class="carrinho-lateral__resumo">
      <div class="carrinho-lateral__linha">
        <span>Subtotal</span>
        <span id="subtotal-carrinho">R$ 0,00</span>
      </div>
      <div class="carrinho-lateral__linha">
        <span>Total</span>
        <span id="total-carrinho" data-total="0">R$ 0,00</span>
      </div>
    </div>

    <div class="carrinho-lateral__acoes">
      <button type="button" id="botao-limpar-carrinho" class="botao botao--secundario">Limpar carrinho</button>
      <a href="checkout.php" id="link-finalizar-pedido" class="botao botao--primario">Finalizar pedido</a>
    </div>
  </aside>

  <div id="overlay" class="overlay" hidden></div>

  <?php include __DIR__ . '/includes/footer.php'; ?>

  <script src="assets/js/carrinho.js" defer></script>
  <script src="assets/js/menu.js" defer></script>
  <script src="assets/js/gerador-ia.js" defer></script>
</body>
</html>
