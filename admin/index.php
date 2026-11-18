
<?php
session_start();

if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_tipo'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel Administrativo - GameZone</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

    <header class="cabecalho">
        <h1 class="logo">Game<span>Zone</span></h1>
        <a href="../index.php" class="carrinho">Voltar à loja</a>
    </header>

    <main class="jogos">

        <h2>Painel Administrativo</h2>

        <p class="subtitulo">
            Bem-vindo à administração da GameZone!
        </p>

        <h3>Gerenciamento da loja</h3>
        <br><br>

<a href="cadastrar-produto.php" class="botao-detalhes painel-botao">
    + Cadastrar novo jogo
</a>

<br><br>

<a href="produtos.php" class="botao-detalhes painel-botao">
    Gerenciar produtos
</a>

<br><br>

<a href="pedidos.php" class="botao-detalhes painel-botao">
    Visualizar pedidos
</a>

<br><br>

<a href="mensagens.php" class="botao-detalhes painel-botao">
    Mensagens recebidas
</a>
    </main>

</body>
</html>
