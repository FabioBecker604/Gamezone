
<?php
session_start();

require_once "../config/conexao.php";

if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_tipo'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

$mensagem = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $nome = trim($_POST['nome'] ?? "");
    $descricao = trim($_POST['descricao'] ?? "");
    $preco = filter_input(INPUT_POST, 'preco', FILTER_VALIDATE_FLOAT);
    $categoria = filter_input(INPUT_POST, 'categoria_id', FILTER_VALIDATE_INT);
    $imagem = trim($_POST['imagem'] ?? "");
    $estoque = filter_input(INPUT_POST, 'estoque', FILTER_VALIDATE_INT);

    if (
        $nome === "" ||
        $descricao === "" ||
        $preco === false || $preco <= 0 ||
        !$categoria ||
        $imagem === "" ||
        $estoque === false || $estoque === null || $estoque < 0
    ) {
        $mensagem = "Preencha todos os campos corretamente.";
    } else {

        $sql = "INSERT INTO produtos
                (categoria_id, nome, descricao, preco, imagem, estoque)
                VALUES
                (:categoria, :nome, :descricao, :preco, :imagem, :estoque)";

        $stmt = $pdo->prepare($sql);

        $stmt->execute([
            "categoria" => $categoria,
            "nome" => $nome,
            "descricao" => $descricao,
            "preco" => $preco,
            "imagem" => $imagem,
            "estoque" => $estoque
        ]);

        $mensagem = "Jogo cadastrado com sucesso!";
    }
}

$categorias = $pdo->query("SELECT * FROM categorias ORDER BY nome")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar jogo - GameZone</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

    <header class="cabecalho">
        <h1 class="logo">Game<span>Zone</span></h1>
        <a href="index.php" class="carrinho">Voltar ao painel</a>
    </header>

    <main class="jogos">

        <h2>Cadastrar novo jogo</h2>

        <?php if ($mensagem !== ""): ?>
            <p><?= htmlspecialchars($mensagem) ?></p>
        <?php endif; ?>

        <form method="POST">

            <label>Nome do jogo:</label>
            <input type="text" name="nome" required>

            <br><br>

            <label>Descrição:</label>
            <textarea name="descricao" required></textarea>

            <br><br>

            <label>Preço:</label>
            <input type="number" name="preco" step="0.01" min="0.01" required>

            <br><br>

            <label>Categoria:</label>
            <select name="categoria_id" required>
                <option value="">Selecione uma categoria</option>

                <?php foreach ($categorias as $categoria): ?>
                    <option value="<?= (int)$categoria['id'] ?>">
                        <?= htmlspecialchars($categoria['nome']) ?>
                    </option>
                <?php endforeach; ?>

            </select>

            <br><br>

            <label>Nome da imagem:</label>
            <input type="text" name="imagem" placeholder="exemplo.jpg" required>

            <br><br>

            <label>Estoque:</label>
            <input type="number" name="estoque" min="0" required>

            <br><br>

            <button type="submit" class="botao-detalhes">
                Cadastrar jogo
            </button>

        </form>

    </main>

</body>
</html>
