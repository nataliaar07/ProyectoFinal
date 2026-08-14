<?php
session_start();
include("conexion.php");

$usuario = $_POST['usuario'];
$contrasena = md5($_POST['contrasena']); 

$sql = "SELECT * FROM usuarios WHERE usuario='$usuario' AND password='$contrasena'";
$resultado = $conexion->query($sql);

if ($resultado && $resultado->num_rows == 1) {
    $_SESSION['usuario'] = $usuario;
    header("Location: bienvenido.php");
    exit();
} else {
    echo "Usuario o contraseña incorrectos.";
}
?>