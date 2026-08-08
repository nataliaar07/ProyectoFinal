<?php
$servidor = "localhost";
$usuario = "root";
$contrasena = ""; 
$base_datos = "login_db"; // <-- Aquí ya apunta a login_db

$conexion = new mysqli($servidor, $usuario, $contrasena, $base_datos);

if ($conexion->connect_error) {
    die("Conexión fallida: " . $conexion->connect_error);
}
?>