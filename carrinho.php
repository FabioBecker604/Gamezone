
<?php
session_start();

require_once "config/conexao.php";

$carrinho = $_SESSION['carrinho'] ?? [];
$total = 0;
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meu Carrinho - GameZone</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

    <header class="cabecalho">
        <h1 class="logo">Game<span>Zone</span></h1>
        <a href="index.php" class="carrinho">Voltar à loja</a>
    </header>

    <main class="jogos">
        <h2>Meu carrinho</h2>
        <p class="subtitulo">Confira seus jogos escolhidos!</p>

        <?php if (empty($carrinho)): ?>

            <p>Seu carrinho está vazio.</p>

        <?php else: ?>

            <div class="lista-jogos">

                <?php foreach ($carrinho as $id => $quantidade): ?>

                    <?php
                    $sql = "SELECT * FROM produtos WHERE id = :id";
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute(['id' => $id]);
                    $produto = $stmt->fetch(PDO::FETCH_ASSOC);

                    if (!$produto) {
                        continue;
                    }

                    if ($produto['em_promocao'] == 1) {
    $precoFinal = $produto['preco_promocional'];
} else {
    $precoFinal = $produto['preco'];
}

$subtotal = $precoFinal * $quantidade;
$total += $subtotal;
                    ?>

                    <div class="card-jogo">

                        <div class="imagem-jogo">
                            <img
                                src="assets/img/<?= htmlspecialchars($produto['imagem']) ?>"
                                alt="<?= htmlspecialchars($produto['nome']) ?>"
                            >
                        </div>

                        <h3><?= htmlspecialchars($produto['nome']) ?></h3>


<form action="atualizar-carrinho.php" method="POST">

    <input
        type="hidden"
        name="id"
        value="<?= (int)$produto['id'] ?>"
    >

    <label>Quantidade:</label>

    <input
        type="number"
        name="quantidade"
        value="<?= (int)$quantidade ?>"
        min="1"
        max="<?= (int)$produto['estoque'] ?>"
        required
    >

    <button type="submit" class="botao-detalhes">
        Atualizar quantidade
    </button>

</form>

                        <p>
                            Preço: R$
                            <?= number_format((float)$produto['preco'], 2, ',', '.') ?>
                        </p>

                        <p>
                            Subtotal: R$
                            <?= number_format((float)$subtotal, 2, ',', '.') ?>
                        </p>
                        <form action="remover-carrinho.php" method="POST">
    <input type="hidden" name="id" value="<?= (int)$produto['id'] ?>">

    <button type="submit" class="botao-detalhes">
        Remover do carrinho
    </button>
</form>

                    </div>

                <?php endforeach; ?>

            </div>

            <h2>
                Total: R$ <?= number_format((float)$total, 2, ',', '.') ?>
            </h2>

            <br>

<a href="finalizar-compra.php" class="botao-detalhes">
    Finalizar compra
</a>

        <?php endif; ?>

        <br>

        <a href="index.php" class="botao-detalhes">
            Continuar comprando
        </a>

    </main>

</body>
</html>
