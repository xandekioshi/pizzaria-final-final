<?php
/**
 * actions/atualizar_status.php
 * O admin muda o status de um pedido. Faz UPDATE na tabela "pedidos".
 * Só admin pode usar, e só por POST.
 */
session_start();
require_once __DIR__ . '/../config/database.php';
header('Content-Type: application/json');

if (empty($_SESSION['is_admin']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['ok' => false, 'erro' => 'Não autorizado']);
    exit;
}

$idPedido = (int)($_POST['id'] ?? 0);
$status   = $_POST['status'] ?? '';

// Só aceita status conhecidos.
$statusValidos = ['recebido', 'em_preparo', 'saiu_para_entrega', 'entregue', 'cancelado'];
if (!in_array($status, $statusValidos, true)) {
    echo json_encode(['ok' => false, 'erro' => 'Status inválido']);
    exit;
}

$stmt = $pdo->prepare("UPDATE pedidos SET status = :status WHERE id = :id");
$stmt->execute([':status' => $status, ':id' => $idPedido]);

echo json_encode(['ok' => true]);
