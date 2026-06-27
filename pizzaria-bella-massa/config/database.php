<?php
/**
 * config/database.php
 * -------------------------------------------------------------
 * Conexão única com o PostgreSQL usando PDO.
 * Qualquer arquivo PHP que precise do banco faz:
 *
 *     require_once __DIR__ . '/../config/database.php';   // dentro de /actions ou /admin
 *     // ou
 *     require_once __DIR__ . '/config/database.php';      // na raiz
 *
 * e usa a variável $pdo.
 * -------------------------------------------------------------
 */

// >>>>> AJUSTE AQUI conforme o seu PostgreSQL (mesma tela do print) <<<<<
$DB_HOST = '127.0.0.1';
$DB_PORT = '5432';
$DB_NOME = 'pizzaria';   // banco criado com:  CREATE DATABASE pizzaria;
$DB_USER = 'postgres';
$DB_SENHA = 'postgres';  // <<< troque pela senha do seu PostgreSQL

try {
    $dsn = "pgsql:host=$DB_HOST;port=$DB_PORT;dbname=$DB_NOME";
    $pdo = new PDO($dsn, $DB_USER, $DB_SENHA);

    // Faz o PDO lançar exceção quando der erro (mais fácil de depurar).
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // Traz os resultados como array associativo por padrão.
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    // Em produção não mostraríamos o erro na tela, mas para o trabalho ajuda.
    exit('Erro ao conectar no banco de dados: ' . $e->getMessage());
}
