<?php
session_start();

$nombre = $_POST['nombre'];
$carnet = $_POST['carnet'];

$_SESSION['carrito'] = [];

echo "<h2 style='font-family:sans-serif; padding:40px;'>¡Gracias $nombre! Tu compra fue confirmada con el carnet $carnet.</h2>";
echo "<a href='bienvenido.php' style='font-family:sans-serif;'>Volver al catálogo</a>";