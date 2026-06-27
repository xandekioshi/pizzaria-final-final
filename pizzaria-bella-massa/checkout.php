<?php
session_start();
// Esta página só pode ser acessada por quem está logado.
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Finalizar pedido - Pizzaria Bella Massa</title>
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body data-usuario-logado="true" data-usuario-id="<?php echo $_SESSION['usuario_id']; ?>">

  <header class="cabecalho cabecalho--simples">
    <div class="container cabecalho__conteudo">
      <a href="index.php" class="logo">
        <span class="logo__icone" aria-hidden="true">🍕</span>
        <span class="logo__texto">Bella Massa</span>
      </a>
      <a href="index.php" class="botao botao--secundario">Voltar ao cardápio</a>
    </div>
  </header>

  <main class="conteudo-checkout">
    <div class="container checkout__grade">

      <!-- COLUNA: ENDEREÇO (ViaCEP) -->
      <section class="checkout__endereco" aria-labelledby="titulo-endereco">
        <h1 id="titulo-endereco" class="titulo-secao">Endereço de entrega</h1>

        <form id="form-endereco" class="formulario" novalidate>

          <p id="mensagem-erro-cep" class="mensagem-erro" hidden>CEP não encontrado.</p>

          <div class="campo-formulario campo-formulario--cep">
            <label for="cep">CEP</label>
            <div class="campo-com-botao">
              <input type="text" id="cep" name="cep" inputmode="numeric" maxlength="9" placeholder="00000-000" required>
              <button type="button" id="botao-buscar-cep" class="botao botao--secundario">Buscar CEP</button>
            </div>
          </div>

          <div class="campo-formulario">
            <label for="rua">Rua</label>
            <input type="text" id="rua" name="rua" readonly required>
          </div>

          <div class="campo-formulario-linha">
            <div class="campo-formulario">
              <label for="numero">Número</label>
              <input type="text" id="numero" name="numero" required>
            </div>
            <div class="campo-formulario">
              <label for="complemento">Complemento</label>
              <input type="text" id="complemento" name="complemento">
            </div>
          </div>

          <div class="campo-formulario">
            <label for="bairro">Bairro</label>
            <input type="text" id="bairro" name="bairro" readonly required>
          </div>

          <div class="campo-formulario-linha">
            <div class="campo-formulario">
              <label for="cidade">Cidade</label>
              <input type="text" id="cidade" name="cidade" readonly required>
            </div>
            <div class="campo-formulario">
              <label for="estado">Estado</label>
              <input type="text" id="estado" name="estado" readonly maxlength="2" required>
            </div>
          </div>

          <button type="button" id="botao-calcular-frete" class="botao botao--secundario botao--bloco">
            Calcular frete
          </button>
        </form>
      </section>

      <!-- COLUNA: RESUMO DO PEDIDO -->
      <section class="checkout__resumo" aria-labelledby="titulo-resumo">
        <h2 id="titulo-resumo" class="titulo-secao">Resumo do pedido</h2>

        <!-- Preenchido pelo JS lendo o carrinho do localStorage -->
        <ul id="resumo-itens-pedido" class="lista-itens-carrinho"></ul>

        <div class="checkout__totais">
          <div class="checkout__linha-total">
            <span>Subtotal</span>
            <span id="resumo-subtotal" data-valor="0">R$ 0,00</span>
          </div>
          <div class="checkout__linha-total">
            <span>Frete</span>
            <span id="valor-frete" data-valor="0">A calcular</span>
          </div>
          <div class="checkout__linha-total checkout__linha-total--destaque">
            <span>Total</span>
            <span id="resumo-total-geral" data-valor="0">R$ 0,00</span>
          </div>
        </div>

        <!-- Envia o pedido para o back-end processar e salvar -->
        <form id="form-finalizar-pedido" action="actions/processar_pedido.php" method="POST">
          <!-- preenchidos pelo JS antes do envio -->
          <input type="hidden" id="carrinho-json" name="carrinho_json" value="">
          <input type="hidden" id="frete-valor-oculto" name="valor_frete" value="0">
          <input type="hidden" id="total-valor-oculto" name="valor_total" value="0">

          <!-- campos do endereço também vão no POST (copiados pelo JS) -->
          <input type="hidden" id="end-cep"         name="cep"         value="">
          <input type="hidden" id="end-rua"         name="rua"         value="">
          <input type="hidden" id="end-numero"      name="numero"      value="">
          <input type="hidden" id="end-complemento" name="complemento" value="">
          <input type="hidden" id="end-bairro"      name="bairro"      value="">
          <input type="hidden" id="end-cidade"      name="cidade"      value="">
          <input type="hidden" id="end-estado"      name="estado"      value="">

          <p id="mensagem-erro-pedido" class="mensagem-erro" hidden></p>

          <button type="submit" id="botao-confirmar-pedido" class="botao botao--primario botao--bloco">
            Confirmar pedido
          </button>
        </form>
      </section>

    </div>
  </main>

  <?php include __DIR__ . '/includes/footer.php'; ?>

  <script src="assets/js/viacep.js" defer></script>
  <script src="assets/js/checkout.js" defer></script>
</body>
</html>
