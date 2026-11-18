
<?php
session_start();

$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

if ($id && isset($_SESSION['carrinho'][$id])) {
    unset($_SESSION['carrinho'][$id]);
}

header("Location: carrinho.php");
exit;
