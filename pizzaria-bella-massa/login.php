<?php
session_start();
// Se já estiver logado, não faz sentido ver o login de novo.
if (isset($_SESSION['usuario_id'])) {
    header('Location: index.php');
    exit;
}
$erro = isset($_GET['erro']);          // login.php?erro=1 quando e-mail/senha errados
$cadastrado = isset($_GET['cadastro']); // login.php?cadastro=ok depois de criar conta
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Entrar - Pizzaria Bella Massa</title>
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="pagina-autenticacao">

  <header class="cabecalho cabecalho--simples">
    <div class="container cabecalho__conteudo">
      <a href="index.php" class="logo">
        <span class="logo__icone" aria-hidden="true">🍕</span>
        <span class="logo__texto">Bella Massa</span>
      </a>
    </div>
  </header>

  <main class="conteudo-autenticacao">
    <div class="container">
      <div class="cartao-autenticacao">
        <h1 class="cartao-autenticacao__titulo">Entrar</h1>

        <?php if ($cadastrado): ?>
          <p class="mensagem-sucesso">Conta criada com sucesso! Faça login para continuar.</p>
        <?php endif; ?>

        <form id="form-login" action="actions/login.php" method="POST" class="formulario" novalidate>

          <p id="mensagem-erro-login" class="mensagem-erro" <?php echo $erro ? '' : 'hidden'; ?>>
            E-mail ou senha inválidos.
          </p>

          <div class="campo-formulario">
            <label for="email-login">E-mail</label>
            <input type="email" id="email-login" name="email" autocomplete="email" required>
          </div>

          <div class="campo-formulario">
            <label for="senha-login">Senha</label>
            <input type="password" id="senha-login" name="senha" autocomplete="current-password" required>
          </div>

          <button type="submit" class="botao botao--primario botao--bloco">Entrar</button>
        </form>

        <p class="cartao-autenticacao__rodape">
          Ainda não tem conta? <a href="cadastro.php">Cadastre-se</a>
        </p>
      </div>
    </div>
  </main>

  <?php include __DIR__ . '/includes/footer.php'; ?>

  <script src="assets/js/autenticacao.js" defer></script>
</body>
</html>
