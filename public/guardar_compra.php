<?php
session_start();
require 'conexion.php';

if (!isset($_SESSION['usuario'])) {
    header("Location: login.html");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $juego = isset($_POST['juego']) ? $_POST['juego'] : 'Juego no especificado';

    if (!isset($_SESSION['carrito'])) {
        $_SESSION['carrito'] = [];
    }

    $_SESSION['carrito'][] = [
        'juego' => $juego
    ];

    $accion = isset($_POST['accion']) ? $_POST['accion'] : 'agregar';

    if ($accion === 'comprar') {
        header("Location: bienvenido.php?abrir=checkout");
    } else {
        header("Location: bienvenido.php#tienda");
    }
    exit();
}
?>