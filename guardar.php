<?php
include("conexion.php"); 

$nombre = $_POST['nombre'];
$correo = $_POST['correo'];
$telefono = $_POST['telefono'];

$sql = "INSERT INTO usuarios (nombre, correo, telefono) VALUES ('$nombre', '$correo', '$telefono')";

if ($conexion->query($sql) === TRUE) {
    echo "datos guardados"; 
} else {
    echo "Error: " . $sql . "<br>" . $conexion->error; 
}

$conexion->close(); 
?>