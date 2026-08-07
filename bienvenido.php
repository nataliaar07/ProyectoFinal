<?php
session_start();
if (!isset($_SESSION['usuario'])) {
header("Location: login.html");
exit();
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Bienvenidos</title>
<link rel="stylesheet" href="estilos.css">
</head>
<body>
<div class="contenedor">
    <h2>Hola!!!!, <?php echo $_SESSION['usuario']; ?>.</h2>
    <a href="salir.php">Cerrar Sesión</a>
</div>
</body>
</html>