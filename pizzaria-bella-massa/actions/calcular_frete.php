<?php

header('Content-Type: application/json');


$cep    = preg_replace('/\D/', '', $_POST['cep'] ?? '');   // só números
$estado = strtoupper(trim($_POST['estado'] ?? ''));

if (strlen($cep) < 5) {
    echo json_encode(['erro' => 'CEP inválido']);
    exit;
}

$inicioCep = substr($cep, 0, 2);

if ($inicioCep === '79') {
    $frete = 7.50;
} elseif ($estado === 'MS') {
    $frete = 9.90;
} else {
    $frete = 12.00;
}

echo json_encode(['frete' => $frete]);
