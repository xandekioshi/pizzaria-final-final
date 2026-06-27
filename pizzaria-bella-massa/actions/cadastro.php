<?php
/**
 * actions/cadastro.php
 * Cria um novo usuário (sempre como cliente, is_admin = false),
 * guardando a senha com hash.
 */
session_start();
require_once __DIR__ . '/../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../cadastro.php');
    exit;
}

$nome           = trim($_POST['nome'] ?? '');
$email          = trim($_POST['email'] ?? '');
$senha          = $_POST['senha'] ?? '';
$confirmarSenha = $_POST['confirmar_senha'] ?? '';

// Validações simples.
if ($nome === '' || $email === '' || strlen($senha) < 6) {
    header('Location: ../cadastro.php?erro=campos');
    exit;
}
if ($senha !== $confirmarSenha) {
    header('Location: ../cadastro.php?erro=senha');
    exit;
}

// Verifica se o e-mail já existe.
$stmt = $pdo->prepare("SELECT id FROM usuarios WHERE email = :email");
$stmt->execute([':email' => $email]);
if ($stmt->fetch()) {
    header('Location: ../cadastro.php?erro=email');
    exit;
}

// Insere o novo usuário com a senha "hasheada".
$hash = password_hash($senha, PASSWORD_DEFAULT);
$stmt = $pdo->prepare(
    "INSERT INTO usuarios (nome, email, senha, is_admin)
     VALUES (:nome, :email, :senha, FALSE)"
);
$stmt->execute([
    ':nome'  => $nome,
    ':email' => $email,
    ':senha' => $hash,
]);

// Tudo certo: manda para o login.
header('Location: ../login.php?cadastro=ok');
exit;
