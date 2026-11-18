<?php
session_start();

require_once "../config/conexao.php";

if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_tipo'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

$sql = "SELECT * FROM mensagens ORDER BY data_envio DESC";
$stmt = $pdo->query($sql);
$mensagens = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mensagens - GameZone</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<header class="cabecalho">
    <h1 class="logo">Game<span>Zone</span></h1>
    <a href="index.php" class="carrinho">Voltar ao painel</a>
</header>

<main class="jogos">

    <h2>Mensagens recebidas</h2>

    <?php if (empty($mensagens)): ?>

        <p>Nenhuma mensagem recebida ainda.</p>

    <?php else: ?>

        <?php foreach ($mensagens as $mensagem): ?>

            <div class="card-jogo">

                <h3><?= htmlspecialchars($mensagem['nome']) ?></h3>

                <p>
                    E-mail:
                    <?= htmlspecialchars($mensagem['email']) ?>
                </p>

                <p>
                    Mensagem:
                    <?= nl2br(htmlspecialchars($mensagem['mensagem'])) ?>
                </p>

                <p>
                    Data:
                    <?= date('d/m/Y H:i', strtotime($mensagem['data_envio'])) ?>
                </p>

            </div>

        <?php endforeach; ?>

    <?php endif; ?>

</main>

</body>
</html>