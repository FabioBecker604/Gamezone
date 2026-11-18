
<?php
session_start();

require_once "config/conexao.php";

$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

if (!$id) {
    header("Location: index.php");
    exit;
}

$sql = "SELECT id, nome, estoque FROM produtos WHERE id = :id";
$stmt = $pdo->prepare($sql);
$stmt->execute(['id' => $id]);

$produto = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$produto) {
    die("Produto não encontrado.");
}

if ($produto['estoque'] <= 0) {
    die("Produto sem estoque disponível.");
}

if (!isset($_SESSION['carrinho'])) {
    $_SESSION['carrinho'] = [];
}

$quantidadeAtual = $_SESSION['carrinho'][$id] ?? 0;

if ($quantidadeAtual < $produto['estoque']) {
    $_SESSION['carrinho'][$id] = $quantidadeAtual + 1;
}

header("Location: detalhes.php?id=" . $id . "&adicionado=1");
exit;
