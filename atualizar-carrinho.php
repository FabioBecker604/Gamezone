
<?php
session_start();

require_once "config/conexao.php";

$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
$quantidade = filter_input(INPUT_POST, 'quantidade', FILTER_VALIDATE_INT);

if (!$id || $quantidade === false || $quantidade === null) {
    header("Location: carrinho.php");
    exit;
}

$sql = "SELECT estoque FROM produtos WHERE id = :id";
$stmt = $pdo->prepare($sql);
$stmt->execute(['id' => $id]);

$produto = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$produto || $quantidade <= 0) {
    unset($_SESSION['carrinho'][$id]);
} else {
    $quantidade = min($quantidade, (int)$produto['estoque']);
    $_SESSION['carrinho'][$id] = $quantidade;
}

header("Location: carrinho.php");
exit;
