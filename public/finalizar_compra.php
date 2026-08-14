<?php
session_start();
require 'conexion.php';

if (!isset($_SESSION['usuario'])) {
    header("Location: login.html");
    exit();
}

$nombre = $_POST['nombre'] ?? '';
$carnet = $_POST['carnet'] ?? '';
$usuario = $_SESSION['usuario'];
$precio_unitario = 15.00;
$orden_id = uniqid('pedido_');

$carrito = $_SESSION['carrito'] ?? [];

if (!empty($carrito)) {
    $stmt = $conexion->prepare("INSERT INTO compras (usuario, juego, cantidad, precio_unitario, total, nombre, carnet, orden_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    foreach ($carrito as $item) {
        $juego = $item['juego'];
        $cantidad = 1;
        $total = $cantidad * $precio_unitario;
        $stmt->bind_param("ssiddsss", $usuario, $juego, $cantidad, $precio_unitario, $total, $nombre, $carnet, $orden_id);
        $stmt->execute();
    }
    $stmt->close();
}

$_SESSION['carrito'] = [];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Compra confirmada</title>
    <link rel="stylesheet" href="estilos.css">
</head>
<body class="pagina-confirmacion">

    <div class="confirmacion-caja">
        <div class="confirmacion-icono">✓</div>
        <h1>¡Gracias por tu compra!</h1>
        <p class="confirmacion-texto">Gracias <strong><?= htmlspecialchars($nombre) ?></strong>, tu compra fue registrada con éxito. ¡Te esperamos que sigas comprando!</p>

        <div class="confirmacion-detalle">
            <span>Código de carnet</span>
            <strong><?= htmlspecialchars($carnet) ?></strong>
        </div>

        <div class="calificacion-caja">
            <p class="calificacion-titulo">Califica tu proceso de compra</p>
            <form action="guardar_calificacion.php" method="POST" id="formCalificacion">
                <input type="hidden" name="estrellas" id="inputEstrellas" value="0">
                <div class="estrellas" id="estrellas">
                    <span class="estrella" data-valor="1">★</span>
                    <span class="estrella" data-valor="2">★</span>
                    <span class="estrella" data-valor="3">★</span>
                    <span class="estrella" data-valor="4">★</span>
                    <span class="estrella" data-valor="5">★</span>
                </div>
                <button type="submit" class="btn-comprar calificacion-boton">Enviar calificación</button>
            </form>
        </div>

        <a href="bienvenido.php" class="btn-agregar confirmacion-boton">Volver al catálogo</a>
    </div>

    <script>
        const estrellas = document.querySelectorAll('.estrella');
        const inputEstrellas = document.getElementById('inputEstrellas');

        estrellas.forEach(estrella => {
            estrella.addEventListener('click', () => {
                const valor = estrella.getAttribute('data-valor');
                inputEstrellas.value = valor;

                estrellas.forEach(e => {
                    e.classList.toggle('activa', e.getAttribute('data-valor') <= valor);
                });
            });
        });
    </script>

</body>
</html>