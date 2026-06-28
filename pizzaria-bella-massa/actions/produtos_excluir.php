<?php

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
