<?php
/**
 * actions/calcular_frete.php
 * Recebe o endereço (cidade/cep) e devolve o valor do frete em JSON.
 * O cálculo do frete é responsabilidade SÓ do back-end.
 *
 * Regra simples usada aqui (você pode adaptar):
 *   - CEP de Campo Grande/MS (começa com 79)  -> R$ 7,50
 *   - Outros CEPs do estado (informado "MS")   -> R$ 9,90
 *   - Demais casos                             -> R$ 12,00
 */
header('Content-Type: application/json');

// Aceita os dados por POST (vindos do fetch do checkout.js).
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
