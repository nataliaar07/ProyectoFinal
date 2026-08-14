<?php
session_start();
require 'conexion.php';

if (!isset($_SESSION['usuario']) || $_SESSION['usuario'] !== 'admin') {
    header("Location: login.html");
    exit;
}

$resultado = $conexion->query("SELECT * FROM compras ORDER BY id DESC");

$pedidos = [];
while ($fila = $resultado->fetch_assoc()) {
    $clave = $fila['orden_id'] ?: 'individual_' . $fila['id'];

    if (!isset($pedidos[$clave])) {
        $pedidos[$clave] = [
            'usuario' => $fila['usuario'],
            'nombre' => $fila['nombre'],
            'carnet' => $fila['carnet'],
            'fecha' => $fila['fecha'] ?? '',
            'juegos' => [],
            'total' => 0
        ];
    }

    $pedidos[$clave]['juegos'][] = $fila['juego'];
    $pedidos[$clave]['total'] += $fila['total'];
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel de administración</title>
    <link rel="stylesheet" href="estilos.css">
</head>
<body class="pagina-catalogo">

    <nav class="navbar">
        <div class="logo">NEXT LEVEL</div>
        <div class="nav-derecha">
            <span style="color:#8f9bb3; font-size:14px;">Panel de administración</span>
            <a href="salir.php" class="btn-salir">Cerrar sesión</a>
        </div>
    </nav>

    <main class="catalogo">
        <h1>Pedidos realizados</h1>

        <div class="admin-pedidos">
            <?php if (empty($pedidos)): ?>
                <p class="carrito-vacio">No hay pedidos todavía.</p>
            <?php else: ?>
                <?php foreach ($pedidos as $pedido): ?>
                    <div class="pedido-card">
                        <div class="pedido-header">
                            <div>
                                <span class="pedido-cliente"><?= htmlspecialchars($pedido['nombre'] ?: $pedido['usuario']) ?></span>
                                <?php if ($pedido['carnet']): ?>
                                    <span class="pedido-carnet">Carnet: <?= htmlspecialchars($pedido['carnet']) ?></span>
                                <?php endif; ?>
                            </div>
                            <span class="pedido-total">$<?= number_format($pedido['total'], 2) ?></span>
                        </div>

                        <div class="pedido-juegos">
                            <?php foreach ($pedido['juegos'] as $juego): ?>
                                <span class="pedido-juego-tag"><?= htmlspecialchars($juego) ?></span>
                            <?php endforeach; ?>
                        </div>

                        <div class="pedido-footer">
                            <span><?= count($pedido['juegos']) ?> juego(s)</span>
                            <span><?= htmlspecialchars($pedido['fecha']) ?></span>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </main>

</body>
</html>