
<?php
session_start();

require_once "config/conexao.php";

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id) {
    header("Location: index.php");
    exit;
}

$sql = "SELECT id, total, forma_pagamento, status
        FROM pedidos
        WHERE id = :id AND usuario_id = :usuario_id";

$stmt = $pdo->prepare($sql);
$stmt->execute([
    'id' => $id,
    'usuario_id' => $_SESSION['usuario_id']
]);

$pedido = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$pedido) {
    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pedido confirmado - GameZone</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<header class="cabecalho">
    <h1 class="logo">Game<span>Zone</span></h1>
    <a href="index.php" class="carrinho">Voltar à loja</a>
</header>

<main class="jogos">
    <h2>Pedido registrado com sucesso!</h2>

    <p class="subtitulo">
        Obrigado por comprar na GameZone!
    </p>

    <h3>Resumo da compra</h3>

    <p>
        Número do pedido:
        #<?= (int) $pedido['id'] ?>
    </p>

    <p>
        Total:
        R$ <?= number_format((float) $pedido['total'], 2, ',', '.') ?>
    </p>

    <p>
        Forma de pagamento:
        <?= htmlspecialchars($pedido['forma_pagamento']) ?>
    </p>

    <p>
        Status:
        <?= htmlspecialchars($pedido['status']) ?>
    </p>

    <br>

    <a href="index.php" class="botao-detalhes">
        Continuar comprando
    </a>
</main>

</body>
</html>
