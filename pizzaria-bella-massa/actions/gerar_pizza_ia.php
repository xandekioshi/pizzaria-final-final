<?php
/**
 * actions/gerar_pizza_ia.php  (Extra 4)
 * Recebe 3 ingredientes e devolve um nome + descrição criativos em JSON.
 * Depois salva essa pizza na tabela "produtos" para que ela possa ser
 * adicionada ao carrinho como qualquer outro produto.
 *
 * Como funciona:
 *   - Se você preencher a constante GEMINI_API_KEY, ele chama a API do
 *     Gemini via cURL (atende ao requisito do extra).
 *   - Se deixar em branco, ele gera um nome/descrição localmente, então
 *     o projeto continua funcionando sem precisar de chave de API.
 */
session_start();
require_once __DIR__ . '/../config/database.php';
header('Content-Type: application/json');

// >>> Cole aqui a sua chave do Google Gemini para usar IA de verdade. <<<
// Deixe '' (vazio) para usar o gerador local.
const GEMINI_API_KEY = '';

// Recebe os ingredientes (esperamos exatamente 3).
$ingredientes = $_POST['ingredientes'] ?? [];
if (!is_array($ingredientes) || count($ingredientes) !== 3) {
    echo json_encode(['erro' => 'Selecione exatamente 3 ingredientes.']);
    exit;
}
$ingredientes = array_map('trim', $ingredientes);
$listaTexto = implode(', ', $ingredientes);

$nome = '';
$descricao = '';

// ---------- Opção 1: IA de verdade (Gemini via cURL) ----------
if (GEMINI_API_KEY !== '') {
    $prompt = "Crie um nome curto e criativo (máx 4 palavras) e uma descrição "
            . "apetitosa de uma linha para uma pizza com os ingredientes: $listaTexto. "
            . "Responda no formato: Nome | Descrição";

    $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key=" . GEMINI_API_KEY;
    $corpo = json_encode([
        'contents' => [['parts' => [['text' => $prompt]]]],
    ]);

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $corpo);
    $resposta = curl_exec($ch);
    curl_close($ch);

    $dados = json_decode($resposta, true);
    $texto = $dados['candidates'][0]['content']['parts'][0]['text'] ?? '';
    if ($texto !== '' && strpos($texto, '|') !== false) {
        [$nome, $descricao] = array_map('trim', explode('|', $texto, 2));
    }
}

// ---------- Opção 2: gerador local (se não usou IA ou ela falhou) ----------
if ($nome === '') {
    $nome = 'Pizza ' . $ingredientes[0] . ' ' . $ingredientes[1];
    $descricao = 'Combinação especial de ' . $listaTexto . ' na nossa massa artesanal.';
}

// Preço fixo para pizzas personalizadas.
$preco = 49.90;

// Salva no banco para poder ir ao carrinho.
$stmt = $pdo->prepare(
    "INSERT INTO produtos (nome, descricao, preco, tipo)
     VALUES (:nome, :descricao, :preco, 'pizza')
     RETURNING id"
);
$stmt->execute([':nome' => $nome, ':descricao' => $descricao, ':preco' => $preco]);
$id = $stmt->fetchColumn();

echo json_encode([
    'id'        => $id,
    'nome'      => $nome,
    'descricao' => $descricao,
    'preco'     => $preco,
]);
