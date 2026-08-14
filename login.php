<?php
include("conexion.php");

$usuario = $_POST['usuario'];
$password = $_POST['password'];

// IMPORTANTE: Asegúrate de que 'usuarios' sea el nombre exacto de la tabla en phpMyAdmin
$sql = "SELECT * FROM usuarios WHERE usuario = '$usuario' AND password = '$password'";
$resultado = $conexion->query($sql);

if ($resultado && $resultado->num_rows > 0) {
    // Si la contraseña coincide, entra a bienvenido.php (o tu página principal)
    header("Location: bienvenido.php");
    exit();
} else {
    echo "<script>
            alert('Usuario o contraseña incorrectos');
            window.location.href = 'index.html';
          </script>";
}

$conexion->close();
?>