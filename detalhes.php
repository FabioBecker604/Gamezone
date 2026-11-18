
<?php
require_once "config/conexao.php";

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id) {
    die("Jogo não encontrado.");
}

$sql = "SELECT * FROM produtos WHERE id = :id";
$stmt = $pdo->prepare($sql);
$stmt->execute(['id' => $id]);

$produto = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$produto) {
    die("Jogo não encontrado.");
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($produto['nome']) ?> - GameZone</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

    <header class="cabecalho">
        <h1 class="logo">Game<span>Zone</span></h1>
        <a href="index.php" class="carrinho">Voltar à loja</a>
    </header>

    <main class="detalhes-produto">

        <div class="imagem-detalhe">
            <img
                src="assets/img/<?= htmlspecialchars($produto['imagem']) ?>"
                alt="<?= htmlspecialchars($produto['nome']) ?>"
            >
        </div>

        <div class="informacoes-produto">
            <h2><?= htmlspecialchars($produto['nome']) ?></h2>

            <p>
                <?= htmlspecialchars($produto['descricao']) ?>
            </p>

            <?php if ($produto['em_promocao'] == 1): ?>

    <p class="preco-antigo">
        De: R$ <?= number_format((float)$produto['preco'], 2, ',', '.') ?>
    </p>

    <h3 class="preco-promocional">
        Por: R$ <?= number_format((float)$produto['preco_promocional'], 2, ',', '.') ?>
    </h3>

<?php else: ?>

    <h3>
        R$ <?= number_format((float)$produto['preco'], 2, ',', '.') ?>
    </h3>

<?php endif; ?>

            <p>
                Estoque disponível: <?= (int)$produto['estoque'] ?>
            </p>

            <?php if ($produto['estoque'] > 0): ?>

    <form action="adicionar-carrinho.php" method="POST">
        <input type="hidden" name="id" value="<?= (int)$produto['id'] ?>">

        <button type="submit" class="botao-detalhes">
            🛒 Adicionar ao carrinho
        </button>
    </form>

<?php else: ?>

    <p>Produto indisponível no momento.</p>

<?php endif; ?>

            <a href="index.php" class="botao-detalhes">
                Voltar aos jogos
            </a>
        </div>

    </main>

</body>
</html>
