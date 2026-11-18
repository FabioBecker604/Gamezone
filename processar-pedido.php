
<?php
session_start();

require_once "config/conexao.php";

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: carrinho.php");
    exit;
}

$pagamento = $_POST['pagamento'] ?? '';
$formasPermitidas = ['PIX', 'Boleto', 'Cartão'];

if (!in_array($pagamento, $formasPermitidas, true)) {
    header("Location: finalizar-compra.php");
    exit;
}

$carrinho = $_SESSION['carrinho'] ?? [];

if (empty($carrinho)) {
    header("Location: carrinho.php");
    exit;
}

try {
    $pdo->beginTransaction();

    $total = 0;
    $itens = [];

    foreach ($carrinho as $id => $quantidade) {
        $id = (int) $id;
        $quantidade = (int) $quantidade;

        if ($id <= 0 || $quantidade <= 0) {
            throw new Exception("Item inválido no carrinho.");
        }

        $sql = "SELECT id, nome, preco, estoque,
               em_promocao, preco_promocional
        FROM produtos
        WHERE id = :id
        FOR UPDATE";

        $stmt = $pdo->prepare($sql);
        $stmt->execute(['id' => $id]);

        $produto = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$produto) {
            throw new Exception("Um produto do carrinho não foi encontrado.");
        }

        if ($quantidade > (int) $produto['estoque']) {
            throw new Exception(
                "Estoque insuficiente para: " . $produto['nome']
            );
        }

        if ($produto['em_promocao'] == 1) {
    $precoFinal = (float) $produto['preco_promocional'];
} else {
    $precoFinal = (float) $produto['preco'];
}

$subtotal = $precoFinal * $quantidade;
$total += $subtotal;
        $itens[] = [
    'produto' => $produto,
    'quantidade' => $quantidade,
    'precoFinal' => $precoFinal
];
    }

    $sql = "INSERT INTO pedidos
            (usuario_id, total, forma_pagamento, status)
            VALUES (:usuario_id, :total, :pagamento, 'Pendente')";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        'usuario_id' => $_SESSION['usuario_id'],
        'total' => $total,
        'pagamento' => $pagamento
    ]);

    $pedidoId = $pdo->lastInsertId();

    foreach ($itens as $item) {
        $produto = $item['produto'];
        $quantidade = $item['quantidade'];

        $sql = "INSERT INTO itens_pedido
                (pedido_id, produto_id, quantidade, preco_unitario)
                VALUES (:pedido_id, :produto_id, :quantidade, :preco)";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'pedido_id' => $pedidoId,
            'produto_id' => $produto['id'],
            'quantidade' => $quantidade,
            'preco' => $item['precoFinal']
        ]);

        $sql = "UPDATE produtos
                SET estoque = estoque - :quantidade
                WHERE id = :id AND estoque >= :quantidade";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'quantidade' => $quantidade,
            'id' => $produto['id']
        ]);

        if ($stmt->rowCount() !== 1) {
            throw new Exception("Não foi possível atualizar o estoque.");
        }
    }

    $pdo->commit();

    $_SESSION['carrinho'] = [];

    header("Location: pedido-confirmado.php?id=" . $pedidoId);
    exit;

} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    $mensagem = $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Erro no pedido - GameZone</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <main class="jogos">
        <h2>Não foi possível finalizar o pedido</h2>
        <p><?= htmlspecialchars($mensagem ?? 'Ocorreu um erro.') ?></p>
        <a href="carrinho.php" class="botao-detalhes">
            Voltar ao carrinho
        </a>
    </main>
</body>
</html>
