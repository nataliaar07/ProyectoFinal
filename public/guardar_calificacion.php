<?php
session_start();
require 'conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = $_SESSION['usuario'] ?? 'invitado';
    $estrellas = isset($_POST['estrellas']) ? (int)$_POST['estrellas'] : 0;

    if ($estrellas >= 1 && $estrellas <= 5) {
        $stmt = $conexion->prepare("INSERT INTO calificaciones (usuario, estrellas) VALUES (?, ?)");
        $stmt->bind_param("si", $usuario, $estrellas);
        $stmt->execute();
        $stmt->close();
    }

    header("Location: bienvenido.php");
    exit;
}
?>