<?php
session_start();
require_once __DIR__ . '/../config/database.php';

// Proteção: só admin entra aqui.
if (!isset($_SESSION['usuario_id']) || empty($_SESSION['is_admin'])) {
    header('Location: ../login.php');
    exit;
}

// ---- Cartões de resumo (consultas agregadas) ----
$pedidosHoje = $pdo->query(
    "SELECT COUNT(*) FROM pedidos WHERE DATE(data_pedido) = CURRENT_DATE"
)->fetchColumn();

$faturamentoHoje = $pdo->query(
    "SELECT COALESCE(SUM(valor_total), 0) FROM pedidos
     WHERE DATE(data_pedido) = CURRENT_DATE AND status <> 'cancelado'"
)->fetchColumn();

$pedidosPendentes = $pdo->query(
    "SELECT COUNT(*) FROM pedidos
     WHERE status IN ('recebido', 'em_preparo', 'saiu_para_entrega')"
)->fetchColumn();

$faturamentoMes = $pdo->query(
    "SELECT COALESCE(SUM(valor_total), 0) FROM pedidos
     WHERE date_trunc('month', data_pedido) = date_trunc('month', CURRENT_DATE)
       AND status <> 'cancelado'"
)->fetchColumn();

// ---- Últimos 5 pedidos ----
$ultimos = $pdo->query(
    "SELECT p.id, u.nome AS cliente, p.data_pedido, p.valor_total, p.status
     FROM pedidos p
     JOIN usuarios u ON u.id = p.id_usuario
     ORDER BY p.data_pedido DESC
     LIMIT 5"
)->fetchAll();

// Rótulo + cor do status (usado também em pedidos.php).
function etiquetaStatus($status) {
    $mapa = [
        'recebido'          => ['Recebido', 'recebido'],
        'em_preparo'        => ['Em preparo', 'em-preparo'],
        'saiu_para_entrega' => ['Saiu para entrega', 'entrega'],
        'entregue'          => ['Entregue', 'entregue'],
        'cancelado'         => ['Cancelado', 'cancelado'],
    ];
    [$texto, $classe] = $mapa[$status] ?? [$status, 'recebido'];
    return '<span class="etiqueta-status etiqueta-status--' . $classe . '">' . $texto . '</span>';
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Painel Admin - Dashboard</title>
  <link rel="stylesheet" href="../assets/css/style.css">
  <link rel="stylesheet" href="../assets/css/admin.css">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
          <li><a href="dashboard.php" class="is-ativo">Dashboard</a></li>
          <li><a href="produtos.php">Produtos</a></li>
          <li><a href="pedidos.php">Pedidos</a></li>
          <li><a href="../index.php">Ver site</a></li>
          <li><a href="../actions/logout.php" id="link-logout-admin">Sair</a></li>
        </ul>
      </nav>
    </aside>

    <main class="admin-conteudo">
      <header class="admin-cabecalho">
        <h1>Dashboard</h1>
        <span>Olá, <span id="nome-admin-logado"><?php echo htmlspecialchars($_SESSION['usuario_nome']); ?></span></span>
      </header>

      <!-- CARTÕES DE RESUMO -->
      <section class="admin-cartoes" aria-label="Resumo geral">
        <article class="admin-cartao">
          <span class="admin-cartao__rotulo">Pedidos hoje</span>
          <span class="admin-cartao__valor"><?php echo $pedidosHoje; ?></span>
        </article>
        <article class="admin-cartao">
          <span class="admin-cartao__rotulo">Faturamento hoje</span>
          <span class="admin-cartao__valor">R$ <?php echo number_format($faturamentoHoje, 2, ',', '.'); ?></span>
        </article>
        <article class="admin-cartao">
          <span class="admin-cartao__rotulo">Pedidos pendentes</span>
          <span class="admin-cartao__valor"><?php echo $pedidosPendentes; ?></span>
        </article>
        <article class="admin-cartao">
          <span class="admin-cartao__rotulo">Faturamento no mês</span>
          <span class="admin-cartao__valor">R$ <?php echo number_format($faturamentoMes, 2, ',', '.'); ?></span>
        </article>
      </section>

      <!-- EXTRA 2: GRÁFICO DE VENDAS -->
      <section class="admin-grafico" aria-labelledby="titulo-grafico">
        <div class="admin-grafico__cabecalho">
          <h2 id="titulo-grafico">Pizzas mais vendidas</h2>
          <select id="seletor-tipo-grafico">
            <option value="mais_vendidas">Mais vendidas (quantidade)</option>
            <option value="faturamento">Faturamento por produto</option>
          </select>
        </div>
        <canvas id="grafico-vendas" height="120"></canvas>
      </section>

      <!-- ÚLTIMOS PEDIDOS -->
      <section class="admin-tabela-secao">
        <div class="admin-tabela-secao__cabecalho">
          <h2>Últimos pedidos</h2>
          <a href="pedidos.php" class="botao botao--secundario">Ver todos</a>
        </div>

        <table class="admin-tabela">
          <thead>
            <tr>
              <th>Pedido</th><th>Cliente</th><th>Data</th><th>Total</th><th>Status</th>
            </tr>
          </thead>
          <tbody id="tabela-ultimos-pedidos">
            <?php if (count($ultimos) === 0): ?>
              <tr><td colspan="5">Nenhum pedido ainda.</td></tr>
            <?php else: ?>
              <?php foreach ($ultimos as $p): ?>
                <tr>
                  <td>#<?php echo str_pad($p['id'], 4, '0', STR_PAD_LEFT); ?></td>
                  <td><?php echo htmlspecialchars($p['cliente']); ?></td>
                  <td><?php echo date('d/m/Y H:i', strtotime($p['data_pedido'])); ?></td>
                  <td>R$ <?php echo number_format($p['valor_total'], 2, ',', '.'); ?></td>
                  <td><?php echo etiquetaStatus($p['status']); ?></td>
                </tr>
              <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </section>

    </main>
  </div>

  <script src="../assets/js/admin-dashboard.js" defer></script>
</body>
</html>
