<?php


$DB_HOST = '127.0.0.1';
$DB_PORT = '5432';
$DB_NOME = 'pizzaria';   
$DB_USER = 'postgres';
$DB_SENHA = 'postgres';  

try {
    $dsn = "pgsql:host=$DB_HOST;port=$DB_PORT;dbname=$DB_NOME";
    $pdo = new PDO($dsn, $DB_USER, $DB_SENHA);

    
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

} catch (PDOException $e) {
   
    exit('Erro ao conectar no banco de dados: ' . $e->getMessage());
}
