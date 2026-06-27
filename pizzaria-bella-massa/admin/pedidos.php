<?php
session_start();
require_once __DIR__ . '/../config/database.php';

if (!isset($_SESSION['usuario_id']) || empty($_SESSION['is_admin'])) {
    header('Location: ../login.php');
    exit;
}

// Todos os pedidos com o nome do cliente.
$pedidos = $pdo->query(
    "SELECT p.*, u.nome AS cliente
     FROM pedidos p
     JOIN usuarios u ON u.id = p.id_usuario
     ORDER BY p.data_pedido DESC"
)->fetchAll();

// Para cada pedido, busca os itens (para o resumo e o modal de detalhes).
$stmtItens = $pdo->prepare(
    "SELECT i.quantidade, i.preco_unitario, pr.nome
     FROM itens_pedido i
     JOIN produtos pr ON pr.id = i.id_produto
     WHERE i.id_pedido = :id"
);

$statusOpcoes = [
    'recebido'          => 'Recebido',
    'em_preparo'        => 'Em preparo',
    'saiu_para_entrega' => 'Saiu para entrega',
    'entregue'          => 'Entregue',
    'cancelado'         => 'Cancelado',
];
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Painel Admin - Pedidos</title>
  <link rel="stylesheet" href="../assets/css/style.css">
  <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body class="pagina-admin">

  <div class="admin-layout">

    <aside class="admin-menu" aria-label="Menu administrativo">
      <div class="admin-menu__logo">
        <span>Bella Massa</span>
        <small>Painel Admin</small>
      </div>
      <nav>
        <ul class="admin-menu__lista">
          <li><a href="dashboard.php">Dashboard</a></li>
          <li><a href="produtos.php">Produtos</a></li>
          <li><a href="pedidos.php" class="is-ativo">Pedidos</a></li>
          <li><a href="../index.php">Ver site</a></li>
          <li><a href="../actions/logout.php" id="link-logout-admin">Sair</a></li>
        </ul>
      </nav>
    </aside>

    <main class="admin-conteudo">
      <header class="admin-cabecalho">
        <h1>Pedidos</h1>
      </header>

      <div class="admin-filtros">
        <label for="filtro-status">Filtrar por status:</label>
        <select id="filtro-status">
          <option value="todos">Todos</option>
          <?php foreach ($statusOpcoes as $valor => $texto): ?>
            <option value="<?php echo $valor; ?>"><?php echo $texto; ?></option>
          <?php endforeach; ?>
        </select>
      </div>

      <section class="admin-tabela-secao">
        <table class="admin-tabela">
          <thead>
            <tr>
              <th>Pedido</th><th>Cliente</th><th>Data</th><th>Itens</th>
              <th>Frete</th><th>Total</th><th>Status</th><th>Ações</th>
            </tr>
          </thead>
          <tbody id="tabela-pedidos">
            <?php if (count($pedidos) === 0): ?>
              <tr><td colspan="8">Nenhum pedido ainda.</td></tr>
            <?php else: ?>
              <?php foreach ($pedidos as $p):
                  // Busca os itens deste pedido.
                  $stmtItens->execute([':id' => $p['id']]);
                  $itens = $stmtItens->fetchAll();

                  // Resumo curto dos itens para a coluna "Itens".
                  $resumo = [];
                  foreach ($itens as $it) {
                      $resumo[] = $it['quantidade'] . 'x ' . $it['nome'];
                  }
                  $resumoTexto = implode(', ', $resumo);

                  // Endereço em uma linha.
                  $endereco = $p['rua'] . ', ' . $p['numero']
                            . ($p['complemento'] ? ' (' . $p['complemento'] . ')' : '')
                            . ' - ' . $p['bairro'] . ', ' . $p['cidade'] . '/' . $p['estado'];

                  // Itens em JSON para o modal de detalhes (lido pelo JS).
                  $itensJson = htmlspecialchars(json_encode($itens), ENT_QUOTES);
              ?>
                <tr data-pedido-id="<?php echo $p['id']; ?>"
                    data-status="<?php echo $p['status']; ?>"
                    data-cliente="<?php echo htmlspecialchars($p['cliente']); ?>"
                    data-endereco="<?php echo htmlspecialchars($endereco); ?>"
                    data-frete="<?php echo $p['valor_frete']; ?>"
                    data-total="<?php echo $p['valor_total']; ?>"
                    data-itens="<?php echo $itensJson; ?>">
                  <td>#<?php echo str_pad($p['id'], 4, '0', STR_PAD_LEFT); ?></td>
                  <td><?php echo htmlspecialchars($p['cliente']); ?></td>
                  <td><?php echo date('d/m/Y H:i', strtotime($p['data_pedido'])); ?></td>
                  <td><?php echo htmlspecialchars($resumoTexto); ?></td>
                  <td>R$ <?php echo number_format($p['valor_frete'], 2, ',', '.'); ?></td>
                  <td>R$ <?php echo number_format($p['valor_total'], 2, ',', '.'); ?></td>
                  <td>
                    <select class="seletor-status-pedido" data-id="<?php echo $p['id']; ?>">
                      <?php foreach ($statusOpcoes as $valor => $texto): ?>
                        <option value="<?php echo $valor; ?>" <?php echo $p['status'] === $valor ? 'selected' : ''; ?>>
                          <?php echo $texto; ?>
                        </option>
                      <?php endforeach; ?>
                    </select>
                  </td>
                  <td class="admin-tabela__acoes">
                    <button type="button" class="botao botao--secundario botao-ver-detalhes-pedido" data-id="<?php echo $p['id']; ?>">
                      Ver detalhes
                    </button>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </section>

      <!-- MODAL: DETALHES DO PEDIDO -->
      <dialog id="modal-detalhes-pedido" class="modal-produto">
        <header class="modal-produto__cabecalho">
          <h2>Detalhes do pedido <span id="detalhe-numero-pedido">#0000</span></h2>
          <button type="button" id="botao-fechar-modal-detalhes" aria-label="Fechar">&times;</button>
        </header>

        <p><strong>Cliente:</strong> <span id="detalhe-cliente-nome"></span></p>
        <p><strong>Endereço:</strong> <span id="detalhe-endereco"></span></p>

        <ul id="detalhe-lista-itens" class="lista-itens-carrinho"></ul>

        <div class="checkout__totais">
          <div class="checkout__linha-total">
            <span>Frete</span>
            <span id="detalhe-valor-frete">R$ 0,00</span>
          </div>
          <div class="checkout__linha-total checkout__linha-total--destaque">
            <span>Total</span>
            <span id="detalhe-valor-total">R$ 0,00</span>
          </div>
        </div>
      </dialog>

    </main>
  </div>

  <div id="overlay-admin" class="overlay" hidden></div>

  <script src="../assets/js/admin-pedidos.js" defer></script>
</body>
</html>
