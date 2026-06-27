<?php
/**
 * actions/login.php
 * Recebe o POST do formulário de login, confere e-mail/senha no banco
 * e, se estiver tudo certo, cria a $_SESSION.
 */
session_start();
require_once __DIR__ . '/../config/database.php';

// Só aceita POST.
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../login.php');
    exit;
}

$email = trim($_POST['email'] ?? '');
$senha = $_POST['senha'] ?? '';

// Busca o usuário pelo e-mail.
$stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = :email");
$stmt->execute([':email' => $email]);
$usuario = $stmt->fetch();

// Confere a senha com password_verify (senha guardada com hash).
if ($usuario && password_verify($senha, $usuario['senha'])) {
    // No PostgreSQL via PDO o booleano costuma vir como 't' ou 'f' (texto),
    // então comparamos explicitamente para não marcar todo mundo como admin.
    $ehAdmin = in_array($usuario['is_admin'], [true, 't', '1', 1, 'true'], true);

    $_SESSION['usuario_id']   = $usuario['id'];
    $_SESSION['usuario_nome'] = $usuario['nome'];
    $_SESSION['is_admin']     = $ehAdmin;

    // Admin vai para o painel, cliente vai para o cardápio.
    if ($_SESSION['is_admin']) {
        header('Location: ../admin/dashboard.php');
    } else {
        header('Location: ../index.php');
    }
    exit;
}

// Deu errado: volta para o login mostrando o erro.
header('Location: ../login.php?erro=1');
exit;
