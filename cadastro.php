
<?php
require_once "config/conexao.php";

$mensagem = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $nome = trim($_POST["nome"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $senha = $_POST["senha"] ?? "";

    if ($nome === "" || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($senha) < 6) {

        $mensagem = "Preencha os campos corretamente. A senha deve ter pelo menos 6 caracteres.";

    } else {

        $verificar = $pdo->prepare(
            "SELECT id FROM usuarios WHERE email = :email"
        );

        $verificar->execute(["email" => $email]);

        if ($verificar->fetch()) {

            $mensagem = "Este e-mail já está cadastrado.";

        } else {

            $senhaHash = password_hash($senha, PASSWORD_DEFAULT);

            $sql = "INSERT INTO usuarios (nome, email, senha)
                    VALUES (:nome, :email, :senha)";

            $stmt = $pdo->prepare($sql);

            $stmt->execute([
                "nome" => $nome,
                "email" => $email,
                "senha" => $senhaHash
            ]);

            $mensagem = "Cadastro realizado com sucesso!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro - GameZone</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

    <header class="cabecalho">
        <h1 class="logo">Game<span>Zone</span></h1>
        <a href="index.php" class="carrinho">Voltar à loja</a>
    </header>

    <main class="jogos">
        <h2>Criar minha conta</h2>
        <p class="subtitulo">Cadastre-se para aproveitar a GameZone!</p>

        <?php if ($mensagem !== ""): ?>
            <p><?= htmlspecialchars($mensagem) ?></p>
        <?php endif; ?>

        <form method="POST">

            <label>Nome completo:</label>
            <input type="text" name="nome" required>

            <br><br>

            <label>E-mail:</label>
            <input type="email" name="email" required>

            <br><br>

            <label>Senha (mínimo 6 caracteres):</label>
            <input type="password" name="senha" minlength="6" required>

            <br><br>

            <button type="submit" class="botao-detalhes">
                Criar conta
            </button>

        </form>
    </main>

</body>
</html>
