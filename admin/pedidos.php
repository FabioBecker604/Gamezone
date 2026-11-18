
<?php
session_start();

require_once "../config/conexao.php";

if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_tipo'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

$sql = "SELECT pedidos.id, pedidos.total, pedidos.forma_pagamento,
               pedidos.status, pedidos.data_pedido,
               usuarios.nome, usuarios.email
        FROM pedidos
        INNER JOIN usuarios ON pedidos.usuario_id = usuarios.id
        ORDER BY pedidos.data_pedido DESC";

$stmt = $pdo->query($sql);
$pedidos = $stmt->fetchAll(PDO::FETCH_ASSOC);
foreach ($pedidos as &$pedido) {
    $sqlItens = "SELECT produtos.nome, itens_pedido.quantidade,
                        itens_pedido.preco_unitario
                 FROM itens_pedido
                 INNER JOIN produtos
                 ON itens_pedido.produto_id = produtos.id
                 WHERE itens_pedido.pedido_id = :pedido_id";

    $stmtItens = $pdo->prepare($sqlItens);
    $stmtItens->execute([
        'pedido_id' => $pedido['id']
    ]);

    $pedido['itens'] = $stmtItens->fetchAll(PDO::FETCH_ASSOC);
}

unset($pedido);

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pedidos - GameZone</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<header class="cabecalho">
    <h1 class="logo">Game<span>Zone</span></h1>
    <a href="index.php" class="carrinho">Voltar ao painel</a>
</header>

<main class="jogos">
    <h2>Pedidos recebidos</h2>
    <p class="subtitulo">Confira as compras realizadas na GameZone.</p>

    <?php if (empty($pedidos)): ?>
        <p>Nenhum pedido foi realizado ainda.</p>
    <?php else: ?>

        <?php foreach ($pedidos as $pedido): ?>
            <div class="card-jogo">
                <h3>Pedido #<?= (int) $pedido['id'] ?></h3>

                <p>
                    <strong>Cliente:</strong>
                    <?= htmlspecialchars($pedido['nome']) ?>
                </p>

                <p>
                    <strong>E-mail:</strong>
                    <?= htmlspecialchars($pedido['email']) ?>
                </p>

                <p>
                    <strong>Total:</strong>
                    R$ <?= number_format((float) $pedido['total'], 2, ',', '.') ?>
                </p>

                <p>
                    <strong>Pagamento:</strong>
                    <?= htmlspecialchars($pedido['forma_pagamento']) ?>
                </p>

                <p>
                    <strong>Status:</strong>
                    <?= htmlspecialchars($pedido['status']) ?>
                </p>
                <h4>Jogos comprados:</h4>

<?php foreach ($pedido['itens'] as $item): ?>

    <p>
        <?= htmlspecialchars($item['nome']) ?>
        — Quantidade: <?= (int)$item['quantidade'] ?>
        — R$ <?= number_format((float)$item['preco_unitario'], 2, ',', '.') ?>
    </p>

<?php endforeach; ?>

                <p>
                    <strong>Data:</strong>
                    <?= date('d/m/Y H:i', strtotime($pedido['data_pedido'])) ?>
                </p>
            </div>
        <?php endforeach; ?>

    <?php endif; ?>
</main>

</body>
</html>
