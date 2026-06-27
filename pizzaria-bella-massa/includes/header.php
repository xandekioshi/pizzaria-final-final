<?php
/**
 * includes/header.php
 * -------------------------------------------------------------
 * Menu superior reaproveitado pelas páginas públicas.
 * IMPORTANTE: a página que inclui este arquivo já deve ter chamado
 * session_start() lá no topo, ANTES de qualquer HTML.
 *
 * Aqui só lemos a $_SESSION para decidir o que mostrar no menu.
 * -------------------------------------------------------------
 */
$usuarioLogado = isset($_SESSION['usuario_id']);
$usuarioNome   = $_SESSION['usuario_nome'] ?? 'Cliente';
$usuarioAdmin  = !empty($_SESSION['is_admin']);
?>
<header id="cabecalho-principal" class="cabecalho">
  <div class="container cabecalho__conteudo">

    <a href="index.php" class="logo">
      <span class="logo__icone" aria-hidden="true">🍕</span>
      <span class="logo__texto">Bella Massa</span>
    </a>

    <nav class="nav-principal" aria-label="Navegação principal">
      <ul class="nav-principal__lista">
        <li><a href="index.php#cardapio">Cardápio</a></li>
        <li><a href="index.php#sobre">Sobre</a></li>
        <li><a href="index.php#contato">Contato</a></li>

        <?php if (!$usuarioLogado): ?>
          <li><a href="login.php">Entrar</a></li>
          <li><a href="cadastro.php">Cadastrar</a></li>
        <?php else: ?>
          <li>Olá, <span id="nome-usuario-logado"><?php echo htmlspecialchars($usuarioNome); ?></span></li>
          <?php if ($usuarioAdmin): ?>
            <li><a href="admin/dashboard.php">Painel Admin</a></li>
          <?php endif; ?>
          <li><a href="actions/logout.php" id="link-logout">Sair</a></li>
        <?php endif; ?>
      </ul>
    </nav>

    <!-- Botão que abre/fecha o carrinho lateral -->
    <button type="button" id="botao-abrir-carrinho" class="botao-carrinho"
            aria-controls="carrinho-lateral" aria-expanded="false">
      <span class="botao-carrinho__icone" aria-hidden="true">🛒</span>
      <span class="botao-carrinho__rotulo">Carrinho</span>
      <span class="botao-carrinho__contador" id="contador-itens-carrinho">0</span>
    </button>

  </div>
</header>
