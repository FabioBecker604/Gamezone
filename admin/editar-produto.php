
<?php
session_start();

require_once "../config/conexao.php";

if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_tipo'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id) {
    die("Produto não encontrado.");
}

$stmt = $pdo->prepare("SELECT * FROM produtos WHERE id = :id");
$stmt->execute(["id" => $id]);
$produto = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$produto) {
    die("Produto não encontrado.");
}

$mensagem = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $nome = trim($_POST['nome'] ?? "");
    $descricao = trim($_POST['descricao'] ?? "");
    $preco = filter_input(INPUT_POST, 'preco', FILTER_VALIDATE_FLOAT);
    $categoria = filter_input(INPUT_POST, 'categoria_id', FILTER_VALIDATE_INT);
    $imagem = trim($_POST['imagem'] ?? "");
    $estoque = filter_input(INPUT_POST, 'estoque', FILTER_VALIDATE_INT);
    $em_promocao = isset($_POST['em_promocao']) ? 1 : 0;

$preco_promocional = filter_input(
    INPUT_POST,
    'preco_promocional',
    FILTER_VALIDATE_FLOAT
);

    if (
    $nome === "" ||
    $descricao === "" ||
    $preco === false || $preco <= 0 ||
    !$categoria ||
    $imagem === "" ||
    $estoque === false || $estoque === null || $estoque < 0 ||
    ($em_promocao === 1 && (
        $preco_promocional === false ||
        $preco_promocional === null ||
        $preco_promocional <= 0 ||
        $preco_promocional >= $preco
    ))
) {
        $mensagem = "Preencha todos os campos corretamente.";
    } else {

        $sql = "UPDATE produtos SET
        nome = :nome,
        descricao = :descricao,
        preco = :preco,
        categoria_id = :categoria,
        imagem = :imagem,
        estoque = :estoque,
        em_promocao = :em_promocao,
        preco_promocional = :preco_promocional
        WHERE id = :id";

        $stmt = $pdo->prepare($sql);

        $stmt->execute([
            "nome" => $nome,
            "descricao" => $descricao,
            "preco" => $preco,
            "categoria" => $categoria,
            "imagem" => $imagem,
            "estoque" => $estoque,
            "em_promocao" => $em_promocao,
"preco_promocional" => $em_promocao ? $preco_promocional : null,
            "id" => $id
            
        ]);

        header("Location: produtos.php");
        exit;
    }
}

$categorias = $pdo->query("SELECT * FROM categorias ORDER BY nome")
                  ->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar produto - GameZone</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<header class="cabecalho">
    <h1 class="logo">Game<span>Zone</span></h1>
    <a href="produtos.php" class="carrinho">Voltar aos produtos</a>
</header>

<main class="jogos">

    <h2>Editar produto</h2>

    <?php if ($mensagem !== ""): ?>
        <p><?= htmlspecialchars($mensagem) ?></p>
    <?php endif; ?>

    <form method="POST">

        <label>Nome do jogo:</label>
        <input type="text" name="nome"
               value="<?= htmlspecialchars($produto['nome']) ?>" required>

        <br><br>

        <label>Descrição:</label>
        <textarea name="descricao" required><?= htmlspecialchars($produto['descricao']) ?></textarea>

        <br><br>

        <label>Preço:</label>
        <input type="number" name="preco" step="0.01" min="0.01"
               value="<?= htmlspecialchars($produto['preco']) ?>" required>

        <br><br>

        <label>Categoria:</label>
        <select name="categoria_id" required>
            <?php foreach ($categorias as $categoria): ?>
                <option value="<?= (int)$categoria['id'] ?>"
                    <?= $categoria['id'] == $produto['categoria_id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($categoria['nome']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <br><br>

        <label>Nome da imagem:</label>
        <input type="text" name="imagem"
               value="<?= htmlspecialchars($produto['imagem']) ?>" required>

        <br><br>

        <label>Estoque:</label>
        <input type="number" name="estoque" min="0"
               value="<?= (int)$produto['estoque'] ?>" required>

        <br><br>
        <label>Promoção:</label>

<div>
    <input
        type="checkbox"
        name="em_promocao"
        value="1"
        <?= $produto['em_promocao'] == 1 ? 'checked' : '' ?>
    >
    Ativar promoção
</div>

<br>

<label>Preço promocional:</label>

<input
    type="number"
    name="preco_promocional"
    step="0.01"
    min="0.01"
    value="<?= htmlspecialchars($produto['preco_promocional'] ?? '') ?>"
    placeholder="Ex: 99.90"
>

<br><br>

        <button type="submit" class="botao-detalhes">
            Salvar alterações
        </button>

    </form>

</main>

</body>
</html>
