<?php
$conexion = new mysqli("localhost", "root", "", "login_db");

if ($conexion->connect_error) {
    die("Error en la conexión: " . $conexion->connect_error);
}
?>