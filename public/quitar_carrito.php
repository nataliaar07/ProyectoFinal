<?php
session_start();

if (isset($_SESSION['carrito'][$_GET['index']])) {
    unset($_SESSION['carrito'][$_GET['index']]);
    $_SESSION['carrito'] = array_values($_SESSION['carrito']);
}

header("Location: bienvenido.php#tienda");
exit;