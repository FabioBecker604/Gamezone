
<?php
session_start();

require_once "config/conexao.php";

$mensagem = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email = trim($_POST["email"] ?? "");
    $senha = $_POST["senha"] ?? "";

    $sql = "SELECT * FROM usuarios WHERE email = :email";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(["email" => $email]);

    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($usuario && password_verify($senha, $usuario["senha"])) {

        session_regenerate_id(true);

        $_SESSION["usuario_id"] = $usuario["id"];
        $_SESSION["usuario_nome"] = $usuario["nome"];
        $_SESSION["usuario_tipo"] = $usuario["tipo"];

        header("Location: index.php");
        exit;

    } else {

        $mensagem = "E-mail ou senha incorretos.";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - GameZone</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

    <header class="cabecalho">
        <h1 class="logo">Game<span>Zone</span></h1>
        <a href="index.php" class="carrinho">Voltar à loja</a>
    </header>

    <main class="jogos">

        <h2>Entrar na minha conta</h2>

        <p class="subtitulo">
            Acesse sua conta GameZone!
        </p>

        <?php if ($mensagem !== ""): ?>
            <p><?= htmlspecialchars($mensagem) ?></p>
        <?php endif; ?>

        <form method="POST">

            <label>E-mail:</label>
            <input type="email" name="email" required>

            <br><br>

            <label>Senha:</label>
            <input type="password" name="senha" required>

            <br><br>

            <button type="submit" class="botao-detalhes">
                Entrar
            </button>

        </form>

        <br>

        <p>
            Ainda não tem conta?
            <a href="cadastro.php">Cadastre-se</a>
        </p>

    </main>

</body>
</html>
