<?php
session_start();
require 'conexion.php';

if (!isset($_SESSION['usuario'])) {
    header("Location: login.html");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = $_SESSION['usuario'];
    $juego = isset($_POST['juego']) ? $_POST['juego'] : 'Juego no especificado';
    $cantidad = isset($_POST['cantidad']) ? (int)$_POST['cantidad'] : 1;
    $precio_unitario = 15.00; // Precio fijo de ejemplo
    $total = $cantidad * $precio_unitario;
    $accion = isset($_POST['accion']) ? $_POST['accion'] : 'comprar';

    if ($accion === 'agregar') {
        if (!isset($_SESSION['carrito'])) {
            $_SESSION['carrito'] = [];
        }

        $_SESSION['carrito'][] = [
            'juego' => $juego,
            'cantidad' => $cantidad,
            'precio_unitario' => $precio_unitario,
            'total' => $total
        ];

        header("Location: bienvenido.php#tienda");
        exit();

    } else {
        $stmt = $conexion->prepare("INSERT INTO compras (usuario, juego, cantidad, precio_unitario, total) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("ssidd", $usuario, $juego, $cantidad, $precio_unitario, $total);

        if ($stmt->execute()) {
            echo "<script>
                    alert('¡Compra de $juego realizada con éxito!');
                    window.location.href = 'bienvenido.php';
                  </script>";
        } else {
            echo "Error al procesar la compra: " . $conexion->error;
        }

        $stmt->close();
    }
}
?>