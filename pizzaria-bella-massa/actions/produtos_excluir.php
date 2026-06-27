<?php
/**
 * actions/produtos_excluir.php
 * Exclui um produto pelo id. Só admin.
 * Recebe o id por GET (ex: produtos_excluir.php?id=5).
 */
session_start();
require_once __DIR__ . '/../config/database.php';

if (empty($_SESSION['is_admin'])) {
    header('Location: ../login.php');
    exit;
}

$id = (int)($_GET['id'] ?? 0);
if ($id > 0) {
    $stmt = $pdo->prepare("DELETE FROM produtos WHERE id = :id");
    $stmt->execute([':id' => $id]);
}

header('Location: ../admin/produtos.php');
exit;
