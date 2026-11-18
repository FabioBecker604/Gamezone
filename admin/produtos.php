
<?php
session_start();

require_once "../config/conexao.php";

if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_tipo'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

$sql = "SELECT produtos.*, categorias.nome AS categoria
        FROM produtos
        LEFT JOIN categorias ON produtos.categoria_id = categorias.id
        ORDER BY produtos.id DESC";

$stmt = $pdo->query($sql);
$produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar produtos - GameZone</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<header class="cabecalho">
    <h1 class="logo">Game<span>Zone</span></h1>
    <a href="index.php" class="carrinho">Voltar ao painel</a>
</header>

<main class="jogos">

    <h2>Gerenciar produtos</h2>
    <p class="subtitulo">Confira os jogos cadastrados na GameZone.</p>

    <?php if (empty($produtos)): ?>
        <p>Nenhum produto cadastrado.</p>
    <?php else: ?>

        <div class="lista-jogos">

            <?php foreach ($produtos as $produto): ?>

                <div class="card-jogo">

                    <div class="imagem-jogo">
                        <img
                            src="../assets/img/<?= htmlspecialchars($produto['imagem']) ?>"
                            alt="<?= htmlspecialchars($produto['nome']) ?>"
                        >
                    </div>

                    <h3><?= htmlspecialchars($produto['nome']) ?></h3>

                    <p>Categoria: <?= htmlspecialchars($produto['categoria'] ?? 'Sem categoria') ?></p>

                    <p>Preço: R$ <?= number_format((float)$produto['preco'], 2, ',', '.') ?></p>

                    <p>Estoque: <?= (int)$produto['estoque'] ?></p>
                    <a href="editar-produto.php?id=<?= (int)$produto['id'] ?>"
   class="botao-detalhes">
    Editar produto
</a>
<form action="excluir-produto.php" method="POST"
      onsubmit="return confirm('Tem certeza que deseja excluir este jogo?');">

    <input type="hidden" name="id" value="<?= (int)$produto['id'] ?>">

    <button type="submit" class="botao-detalhes">
        Excluir produto
    </button>

</form>

                </div>

            <?php endforeach; ?>

        </div>

    <?php endif; ?>

</main>

</body>
</html>
