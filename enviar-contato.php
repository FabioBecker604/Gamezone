<?php
session_start();

require_once "config/conexao.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: index.php#contato");
    exit;
}

$nome = trim($_POST['nome'] ?? '');
$email = trim($_POST['email'] ?? '');
$mensagem = trim($_POST['mensagem'] ?? '');

if (empty($nome) || empty($email) || empty($mensagem)) {
    die("Preencha todos os campos.");
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    die("Digite um e-mail válido.");
}

$sql = "INSERT INTO mensagens (nome, email, mensagem)
        VALUES (:nome, :email, :mensagem)";

$stmt = $pdo->prepare($sql);

$stmt->execute([
    'nome' => $nome,
    'email' => $email,
    'mensagem' => $mensagem
]);

header("Location: index.php?mensagem=enviada#contato");
exit;