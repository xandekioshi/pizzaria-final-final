<?php
session_start();
require_once __DIR__ . '/config/database.php';

// Precisa estar logado e ter um id de pedido na URL.
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}
$idPedido = (int)($_GET['id'] ?? 0);

// Busca o pedido. Um cliente só pode ver o próprio pedido (admin pode ver qualquer um).
$sql = "SELECT * FROM pedidos WHERE id = :id";
$stmt = $pdo->prepare($sql);
$stmt->execute([':id' => $idPedido]);
$pedido = $stmt->fetch();

if (!$pedido || ($pedido['id_usuario'] != $_SESSION['usuario_id'] && empty($_SESSION['is_admin']))) {
    exit('Pedido não encontrado.');
}

// Itens do pedido (com o nome do produto).
$sqlItens = "SELECT i.quantidade, i.preco_unitario, p.nome
             FROM itens_pedido i
             JOIN produtos p ON p.id = i.id_produto
             WHERE i.id_pedido = :id";
$stmtItens = $pdo->prepare($sqlItens);
$stmtItens->execute([':id' => $idPedido]);
$itens = $stmtItens->fetchAll();

// ---- EXTRA 1: monta o link do WhatsApp com o resumo do pedido ----
$whatsappRestaurante = '5567900000000'; // <<< troque pelo número do restaurante (55 + DDD + número)
$texto  = "Olá! Quero confirmar o pedido #" . str_pad($idPedido, 4, '0', STR_PAD_LEFT) . "%0A";
foreach ($itens as $it) {
    $texto .= $it['quantidade'] . "x " . $it['nome'] . "%0A";
}
$texto .= "Frete: R$ " . number_format($pedido['valor_frete'], 2, ',', '.') . "%0A";
$texto .= "Total: R$ " . number_format($pedido['valor_total'], 2, ',', '.') . "%0A";
$texto .= "Endereço: " . $pedido['rua'] . ", " . $pedido['numero'] . " - " . $pedido['bairro'];
$linkWhatsapp = "https://wa.me/$whatsappRestaurante?text=$texto";

// Texto amigável de cada status.
$statusTexto = [
    'recebido'           => 'Pedido recebido',
    'em_preparo'         => 'Em preparo',
    'saiu_para_entrega'  => 'Saiu para entrega',
    'entregue'           => 'Entregue',
    'cancelado'          => 'Cancelado',
];
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Pedido confirmado - Pizzaria Bella Massa</title>
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body data-pedido-id="<?php echo $idPedido; ?>">

  <header class="cabecalho cabecalho--simples">
    <div class="container cabecalho__conteudo">
      <a href="index.php" class="logo">
        <span class="logo__icone" aria-hidden="true">🍕</span>
        <span class="logo__texto">Bella Massa</span>
      </a>
    </div>
  </header>

  <main class="conteudo-confirmacao">
    <div class="container">
      <section class="cartao-confirmacao">
        <h1>Pedido recebido com sucesso!</h1>
        <p>Número do pedido: <strong id="numero-pedido">#<?php echo str_pad($idPedido, 4, '0', STR_PAD_LEFT); ?></strong></p>

        <!-- EXTRA 3: linha do tempo do rastreio (atualizada pelo rastreio.js) -->
        <ol id="linha-tempo-pedido" class="linha-tempo-pedido" data-status-atual="<?php echo $pedido['status']; ?>">
          <li class="etapa" data-status="recebido">
            <span class="etapa__icone" aria-hidden="true">1</span>
            <span class="etapa__texto">Pedido recebido</span>
          </li>
          <li class="etapa" data-status="em_preparo">
            <span class="etapa__icone" aria-hidden="true">2</span>
            <span class="etapa__texto">Em preparo</span>
          </li>
          <li class="etapa" data-status="saiu_para_entrega">
            <span class="etapa__icone" aria-hidden="true">3</span>
            <span class="etapa__texto">Saiu para entrega</span>
          </li>
          <li class="etapa" data-status="entregue">
            <span class="etapa__icone" aria-hidden="true">4</span>
            <span class="etapa__texto">Entregue</span>
          </li>
        </ol>

        <p class="status-pedido-texto">
          Status atual: <strong id="texto-status-atual"><?php echo $statusTexto[$pedido['status']] ?? $pedido['status']; ?></strong>
        </p>

        <!-- RESUMO -->
        <h2 class="titulo-secao">Resumo</h2>
        <ul id="lista-itens-pedido-confirmado" class="lista-itens-carrinho">
          <?php foreach ($itens as $it):
              $subtotal = $it['quantidade'] * $it['preco_unitario']; ?>
            <li class="item-carrinho">
              <span class="item-carrinho__nome"><?php echo htmlspecialchars($it['nome']); ?></span>
              <span class="item-carrinho__quantidade-valor"><?php echo $it['quantidade']; ?></span>
              <span class="item-carrinho__subtotal">R$ <?php echo number_format($subtotal, 2, ',', '.'); ?></span>
            </li>
          <?php endforeach; ?>
        </ul>

        <div class="checkout__totais">
          <div class="checkout__linha-total">
            <span>Frete</span>
            <span id="confirmacao-valor-frete">R$ <?php echo number_format($pedido['valor_frete'], 2, ',', '.'); ?></span>
          </div>
          <div class="checkout__linha-total checkout__linha-total--destaque">
            <span>Total</span>
            <span id="confirmacao-valor-total">R$ <?php echo number_format($pedido['valor_total'], 2, ',', '.'); ?></span>
          </div>
        </div>

        <!-- EXTRA 1: botão do WhatsApp (link montado pelo PHP) -->
        <a id="link-whatsapp" href="<?php echo $linkWhatsapp; ?>" target="_blank" rel="noopener" class="botao botao--whatsapp">
          Confirmar pedido pelo WhatsApp
        </a>

        <a href="index.php" class="botao botao--secundario">Voltar ao cardápio</a>
      </section>
    </div>
  </main>

  <?php include __DIR__ . '/includes/footer.php'; ?>

  <script src="assets/js/rastreio.js" defer></script>
</body>
</html>
