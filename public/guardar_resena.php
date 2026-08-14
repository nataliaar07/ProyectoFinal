<?php
session_start();
include("conexion.php");

if (!isset($_SESSION['usuario'])) {
    header("Location: login.html");
    exit();
}

$usuario = $_SESSION['usuario'];
$comentario = isset($_POST['comentario']) ? $_POST['comentario'] : '';

$puntuacion = isset($_POST['puntuacion']) ? (int)$_POST['puntuacion'] : 5;

$sql = "INSERT INTO resenas (usuario, comentario, puntuacion) VALUES ('$usuario', '$comentario', '$puntuacion')";

if ($conexion->query($sql) === TRUE) {
    header("Location: bienvenido.php");
    exit();
} else {
    echo "Error al guardar la reseña: " . $conexion->error;
}
?>
