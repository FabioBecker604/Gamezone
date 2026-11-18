
<?php
session_start();

require_once "config/conexao.php";

// Recebe os filtros enviados pela página
$busca = trim($_GET['busca'] ?? '');
$categoria = filter_input(INPUT_GET, 'categoria', FILTER_VALIDATE_INT);

// Busca as categorias cadastradas
$sqlCategorias = "SELECT * FROM categorias ORDER BY nome";
$stmtCategorias = $pdo->query($sqlCategorias);
$categorias = $stmtCategorias->fetchAll(PDO::FETCH_ASSOC);

// Monta a consulta dos produtos
$sql = "SELECT * FROM produtos WHERE 1=1";
$parametros = [];

if ($busca !== '') {
    $sql .= " AND nome LIKE :busca";
    $parametros['busca'] = "%$busca%";
}

if ($categoria) {
    $sql .= " AND categoria_id = :categoria";
    $parametros['categoria'] = $categoria;
}

$sql .= " ORDER BY nome";

// Busca os jogos em promoção
$sqlPromocoes = "SELECT * FROM produtos
                 WHERE em_promocao = 1
                 ORDER BY nome";

$stmtPromocoes = $pdo->query($sqlPromocoes);
$promocoes = $stmtPromocoes->fetchAll(PDO::FETCH_ASSOC);

$stmt = $pdo->prepare($sql);
$stmt->execute($parametros);
$produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>



<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>GameZone</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- CSS próprio da GameZone -->
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

    <header class="cabecalho" id="inicio">

    <h1 class="logo">Game<span>Zone</span></h1>

    <nav>
        <a href="#inicio">Início</a>
        <a href="#catalogo">Jogos</a>
        <a href="#promocoes">Promoções</a>
        <a href="#contato">Contato</a>
    </nav>

    <div class="acoes-usuario">

        <?php if (isset($_SESSION['usuario_id'])): ?>

            <span>
                Olá, <?= htmlspecialchars($_SESSION['usuario_nome']) ?>!
            </span>

            <a href="sair.php" class="carrinho">Sair</a>

            <?php if ($_SESSION['usuario_tipo'] === 'admin'): ?>
                <a href="admin/index.php" class="carrinho">
                    Painel Admin
                </a>
            <?php endif; ?>

        <?php else: ?>

            <a href="login.php" class="carrinho">Entrar</a>
            <a href="cadastro.php" class="carrinho">Cadastrar</a>

        <?php endif; ?>

        <a href="carrinho.php" class="carrinho">🛒 Carrinho</a>

    </div>

</header>

    <main>
    <section class="destaque">
        <div class="destaque-conteudo">
            <h2>Entre no universo dos games!</h2>
            <p>Encontre seus jogos favoritos com ofertas especiais.</p>
<a href="#catalogo" class="botao">Explorar jogos</a>        </div>
    </section>

<section class="promocoes" id="promocoes">

    <h2>Ofertas especiais</h2>
    <p class="subtitulo">Aproveite nossos jogos em promoção!</p>

    <div class="grade-produtos row g-4">

        <?php foreach ($promocoes as $produto): ?>

            <div class="card-produto col-12 col-sm-6 col-lg-4 col-xl-3">

                <img src="assets/img/<?= htmlspecialchars($produto['imagem']) ?>"
                     alt="<?= htmlspecialchars($produto['nome']) ?>">

                <h3><?= htmlspecialchars($produto['nome']) ?></h3>

                <p class="preco-antigo">
                    De: R$ <?= number_format($produto['preco'], 2, ',', '.') ?>
                </p>

                <p class="preco-promocional">
                    Por: R$ <?= number_format($produto['preco_promocional'], 2, ',', '.') ?>
                </p>

                <a href="detalhes.php?id=<?= (int)$produto['id'] ?>"
                   class="botao-detalhes">
                    Ver detalhes
                </a>

            </div>

        <?php endforeach; ?>

    </div>

</section>
<section class="jogos" id="catalogo">
    <h2>Jogos em destaque</h2>
    <p class="subtitulo">Confira alguns dos nossos jogos!</p>


<form method="GET" action="index.php" class="filtros-jogos">

    <input
        type="text"
        name="busca"
            class="form-control"

        placeholder="Pesquisar jogo pelo nome..."
        value="<?= htmlspecialchars($busca) ?>"
    >

    <select name="categoria" class="form-select">
        <option value="">Todas as categorias</option>

        <?php foreach ($categorias as $cat): ?>
            <option
                value="<?= (int)$cat['id'] ?>"
                <?= $categoria == $cat['id'] ? 'selected' : '' ?>
            >
                <?= htmlspecialchars($cat['nome']) ?>
            </option>
        <?php endforeach; ?>

    </select>

    <button type="submit" class="btn btn-primary botao-detalhes">
    Pesquisar
</button>

    <a href="index.php" class="btn btn-outline-light botao-detalhes">
    Limpar filtros
</a>

</form>


    <div class="lista-jogos row g-4">
<?php if (empty($produtos)): ?>

    <p class="subtitulo">
        Nenhum jogo encontrado. Tente outra pesquisa!
    </p>

<?php else: ?>
        <?php foreach ($produtos as $produto): ?>

            <div class="card-jogo col-12 col-sm-6 col-lg-4 col-xl-3">
                <div class="imagem-jogo">
    <img src="assets/img/<?= htmlspecialchars($produto['imagem']) ?>"
         alt="<?= htmlspecialchars($produto['nome']) ?>">
</div>

                <h3>
                    <?= htmlspecialchars($produto['nome']) ?>
                </h3>

                <?php if ($produto['em_promocao'] == 1): ?>

    <p class="preco-antigo">
        De: R$ <?= number_format((float)$produto['preco'], 2, ',', '.') ?>
    </p>

    <p class="preco-promocional">
        Por: R$ <?= number_format((float)$produto['preco_promocional'], 2, ',', '.') ?>
    </p>

<?php else: ?>

    <p>
        R$ <?= number_format((float)$produto['preco'], 2, ',', '.') ?>
    </p>

<?php endif; ?>

                <a href="detalhes.php?id=<?= $produto['id'] ?>" class="botao-detalhes">
    Ver detalhes
</a>
            </div>

        <?php endforeach; ?>
<?php endif; ?>
    </div>
</section>




<section class="contato" id="contato">

    <h2>Entre em contato</h2>

    <p>Tem alguma dúvida ou sugestão? Envie uma mensagem para a GameZone!</p>

    <?php if (isset($_GET['mensagem']) && $_GET['mensagem'] === 'enviada'): ?>

    <p class="mensagem-sucesso">
        Mensagem enviada com sucesso! Obrigado por entrar em contato!
    </p>

<?php endif; ?>

    <form action="enviar-contato.php" method="POST">

        <input type="text" name="nome" placeholder="Seu nome" required>

        <input type="email" name="email" placeholder="Seu e-mail" required>

        <textarea name="mensagem" placeholder="Digite sua mensagem..." required></textarea>

        <button type="submit" class="botao-detalhes">
            Enviar mensagem
        </button>

    </form>

</section>

</main>
<footer class="rodape">
    <h2>GameZone</h2>
    <p>Seu universo de games em um só lugar!</p>
    <p class="direitos">
        © 2026 GameZone - Todos os direitos reservados.
    </p>
</footer>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>