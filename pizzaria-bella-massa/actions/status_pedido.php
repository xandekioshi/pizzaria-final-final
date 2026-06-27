<?php
/**
 * actions/status_pedido.php
 * Devolve o status atual de um pedido em JSON.
 * É chamado de tempos em tempos pelo rastreio.js (Extra 3).
 * Usa GET porque é só uma consulta.
 */
session_start();
require_once __DIR__ . '/../config/database.php';
header('Content-Type: application/json');

$idPedido = (int)($_GET['id'] ?? 0);

$stmt = $pdo->prepare("SELECT status FROM pedidos WHERE id = :id");
$stmt->execute([':id' => $idPedido]);
$pedido = $stmt->fetch();

if (!$pedido) {
    echo json_encode(['erro' => 'Pedido não encontrado']);
    exit;
}

echo json_encode(['status' => $pedido['status']]);
