
<?php
session_start();

require_once "config/conexao.php";

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

$carrinho = $_SESSION['carrinho'] ?? [];

if (empty($carrinho)) {
    header("Location: carrinho.php");
    exit;
}

$total = 0;
$itens = [];

foreach ($carrinho as $id => $quantidade) {
    $sql = "SELECT * FROM produtos WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(["id" => $id]);
    $produto = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$produto) {
        continue;
    }

    $quantidade = (int)$quantidade;

    if ($produto['em_promocao'] == 1) {
    $precoFinal = $produto['preco_promocional'];
} else {
    $precoFinal = $produto['preco'];
}

$subtotal = $precoFinal * $quantidade;
$total += $subtotal;

    $itens[] = [
        "produto" => $produto,
        "quantidade" => $quantidade,
        "subtotal" => $subtotal
    ];
}

if (empty($itens)) {
    header("Location: carrinho.php");
    exit;
}

$mensagem = "";
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Finalizar compra - GameZone</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<header class="cabecalho">
    <h1 class="logo">Game<span>Zone</span></h1>
    <a href="carrinho.php" class="carrinho">Voltar ao carrinho</a>
</header>

<main class="jogos">

    <h2>Finalizar compra</h2>
    <p class="subtitulo">Confira seu pedido antes de confirmar.</p>

    <h3>Resumo do pedido</h3>

    <?php foreach ($itens as $item): ?>

        <p>
            <?= htmlspecialchars($item['produto']['nome']) ?>
            — <?= (int)$item['quantidade'] ?> unidade(s)
            — R$ <?= number_format($item['subtotal'], 2, ',', '.') ?>
        </p>

    <?php endforeach; ?>

    <h2>Total: R$ <?= number_format($total, 2, ',', '.') ?></h2>

    <form action="processar-pedido.php" method="POST">

        <h3>Forma de pagamento</h3>

        <label>
            <input type="radio" name="pagamento" value="PIX" required>
            PIX
        </label>

        <br>

        <label>
            <input type="radio" name="pagamento" value="Boleto">
            Boleto
        </label>

        <br>

        <label>
            <input type="radio" name="pagamento" value="Cartão">
            Cartão
        </label>

        <br><br>

        <button type="submit" class="botao-detalhes">
            Confirmar pedido
        </button>

    </form>

</main>

</body>
</html>
