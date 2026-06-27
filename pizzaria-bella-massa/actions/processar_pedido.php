<?php
/**
 * actions/processar_pedido.php
 * Recebe o carrinho e o endereço, valida tudo no servidor e salva o pedido.
 *
 * SEGURANÇA: nunca confiamos no preço que vem do navegador. Buscamos o
 * preço de cada produto direto no banco e recalculamos o total aqui.
 */
session_start();
require_once __DIR__ . '/../config/database.php';

// 1) Só logado e só por POST.
if (!isset($_SESSION['usuario_id']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../login.php');
    exit;
}

// 2) Lê o carrinho enviado em JSON: [{ "id": 1, "quantidade": 2 }, ...]
$carrinho = json_decode($_POST['carrinho_json'] ?? '[]', true);
if (!is_array($carrinho) || count($carrinho) === 0) {
    header('Location: ../checkout.php');
    exit;
}

// 3) Recalcula o subtotal usando os preços do banco.
$subtotal = 0;
$itensValidados = [];
foreach ($carrinho as $item) {
    $idProduto  = (int)($item['id'] ?? 0);
    $quantidade = (int)($item['quantidade'] ?? 0);
    if ($idProduto <= 0 || $quantidade <= 0) {
        continue;
    }

    $stmt = $pdo->prepare("SELECT preco FROM produtos WHERE id = :id");
    $stmt->execute([':id' => $idProduto]);
    $produto = $stmt->fetch();
    if (!$produto) {
        continue; // produto não existe, ignora
    }

    $precoUnitario = (float)$produto['preco'];
    $subtotal += $precoUnitario * $quantidade;

    $itensValidados[] = [
        'id_produto'     => $idProduto,
        'quantidade'     => $quantidade,
        'preco_unitario' => $precoUnitario,
    ];
}

if (count($itensValidados) === 0) {
    header('Location: ../checkout.php');
    exit;
}

// 4) Recalcula o frete no servidor (mesma regra do calcular_frete.php).
$cep    = preg_replace('/\D/', '', $_POST['cep'] ?? '');
$estado = strtoupper(trim($_POST['estado'] ?? ''));
if (substr($cep, 0, 2) === '79') {
    $frete = 7.50;
} elseif ($estado === 'MS') {
    $frete = 9.90;
} else {
    $frete = 12.00;
}

$valorTotal = $subtotal + $frete;

// 5) Salva o pedido e os itens dentro de uma transação.
try {
    $pdo->beginTransaction();

    $stmt = $pdo->prepare(
        "INSERT INTO pedidos
            (id_usuario, valor_frete, valor_total, status,
             cep, rua, numero, complemento, bairro, cidade, estado)
         VALUES
            (:id_usuario, :frete, :total, 'recebido',
             :cep, :rua, :numero, :complemento, :bairro, :cidade, :estado)
         RETURNING id"
    );
    $stmt->execute([
        ':id_usuario'  => $_SESSION['usuario_id'],
        ':frete'       => $frete,
        ':total'       => $valorTotal,
        ':cep'         => $_POST['cep'] ?? '',
        ':rua'         => $_POST['rua'] ?? '',
        ':numero'      => $_POST['numero'] ?? '',
        ':complemento' => $_POST['complemento'] ?? '',
        ':bairro'      => $_POST['bairro'] ?? '',
        ':cidade'      => $_POST['cidade'] ?? '',
        ':estado'      => $estado,
    ]);
    $idPedido = $stmt->fetchColumn();

    // Insere cada item do pedido.
    $stmtItem = $pdo->prepare(
        "INSERT INTO itens_pedido (id_pedido, id_produto, quantidade, preco_unitario)
         VALUES (:id_pedido, :id_produto, :quantidade, :preco_unitario)"
    );
    foreach ($itensValidados as $item) {
        $stmtItem->execute([
            ':id_pedido'      => $idPedido,
            ':id_produto'     => $item['id_produto'],
            ':quantidade'     => $item['quantidade'],
            ':preco_unitario' => $item['preco_unitario'],
        ]);
    }

    $pdo->commit();
} catch (PDOException $e) {
    $pdo->rollBack();
    exit('Erro ao salvar o pedido: ' . $e->getMessage());
}

// 6) Tudo certo: vai para a tela de confirmação.
header('Location: ../pedido-confirmado.php?id=' . $idPedido);
exit;
