<?php
session_start();
include("conexion.php");
$usuario = $_POST['usuario'];
$contrasena = sha1($_POST['contrasena']); 
$sql = "SELECT * FROM usuarios WHERE usuario='$usuario' AND
contrasena='$contrasena'";
$resultado = $conexion->query($sql);
if ($resultado->num_rows == 1) {
$_SESSION['usuario'] = $usuario;
header("Location: bienvenido.php");
} else {
echo "Usuario o contraseña incorrectos.";
}
?>