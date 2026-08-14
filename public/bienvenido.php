<?php
session_start();
require 'conexion.php';


if (!isset($_SESSION['usuario'])) {
    header("Location: login.html");
    exit;
}

$carrito = $_SESSION['carrito'] ?? [];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Catálogo</title>
    <link rel="stylesheet" href="estilos.css">
</head>
<body class="pagina-catalogo">

    <nav class="navbar">
        <div class="logo">NEXT LEVEL</div>
        <ul class="nav-links">
            <li><a href="#tienda">Tienda</a></li>
            <li><a href="#comunidad">Comunidad</a></li>
        </ul>
        <div class="nav-derecha">
            <input type="text" class="buscador" placeholder="Buscar...">
            <button class="btn-carrito" onclick="abrirCarrito()">
                🛒 <span class="carrito-contador"><?= count($carrito) ?></span>
            </button>
            <a href="salir.php" class="btn-salir">Cerrar sesión</a>
        </div>
    </nav>

    <section class="carrusel">
        <div class="carrusel-track">
            <div class="carrusel-slide"></div>
            <div class="carrusel-slide"></div>
            <div class="carrusel-slide"></div>
            <div class="carrusel-slide"></div>
        </div>
        <div class="carrusel-controles">
            <button onclick="moverCarrusel(-1)" class="carrusel-btn">‹</button>
            <button onclick="moverCarrusel(1)" class="carrusel-btn">›</button>
        </div>
    </section>

    <main class="catalogo" id="tienda">
        <h1>Catálogo de juegos</h1>

        <div class="grid-juegos">
            <?php for ($i = 1; $i <= 50; $i++): ?>
                <div class="juego-card" onclick="mostrarInfo(<?= $i ?>)">
                    <div class="juego-imagen"></div>
                    <div class="juego-info">
                        <h3>Juego <?= $i ?></h3>
                        <span class="juego-precio">$15.00</span>
                    </div>
                </div>
            <?php endfor; ?>
        </div>
    </main>

    <div class="modal-overlay" id="modalJuego">
        <div class="modal-caja">
            <button class="modal-cerrar" onclick="cerrarInfo()">×</button>
            <div class="modal-imagen"></div>
            <h2 id="modalTitulo">Nombre del juego</h2>
            <p class="modal-descripcion">Aquí va la descripción del juego (pendiente).</p>
            <div class="modal-detalles">
                <span>Precio: $15.00</span>
                <span>Categoría: Videojuegos</span>
            </div>

            <form action="guardar_compra.php" method="POST" style="margin-top: 15px;">
                <input type="hidden" name="juego" id="inputJuegoNombre">

                <div class="modal-botones">
                    <button type="submit" name="accion" value="agregar" class="btn-agregar">Agregar al carrito</button>
                    <button type="submit" name="accion" value="comprar" class="btn-comprar">Comprar</button>
                </div>
            </form>
        </div>
    </div>

    <div class="modal-overlay" id="modalCarrito">
        <div class="modal-caja modal-carrito-caja">
            <button class="modal-cerrar" onclick="cerrarCarrito()">×</button>
            <h2>Tu carrito</h2>

            <div class="lista-carrito">
                <?php if (empty($carrito)): ?>
                    <p class="carrito-vacio">No has agregado juegos todavía.</p>
                <?php else: ?>
                    <?php foreach ($carrito as $index => $item): ?>
                        <div class="carrito-item">
                            <span><?= htmlspecialchars($item['juego']) ?></span>
                            <a href="quitar_carrito.php?index=<?= $index ?>" class="btn-quitar">Quitar</a>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <?php if (!empty($carrito)): ?>
                <div class="carrito-footer">
                    <button class="btn-comprar" onclick="abrirCheckout()">Listo para comprar</button>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="modal-overlay" id="modalCheckout">
        <div class="modal-caja">
            <button class="modal-cerrar" onclick="cerrarCheckout()">×</button>
            <h2>Finalizar compra</h2>
            <p class="modal-descripcion">Completa tus datos para confirmar la compra.</p>

            <form action="finalizar_compra.php" method="POST">
                <label class="checkout-label">Nombre completo</label>
                <input type="text" name="nombre" class="checkout-input" required>

                <label class="checkout-label">Código de carnet</label>
                <input type="text" name="carnet" class="checkout-input" required>

                <button type="submit" class="btn-comprar">Confirmar compra</button>
            </form>
        </div>
    </div>

    <section class="comunidad" id="comunidad">
        <h1>Comunidad</h1>

        <div class="nueva-resena">
            <form action="guardar_resena.php" method="POST">
                <textarea name="comentario" class="resena-textarea" placeholder="Escribe tu reseña..." required></textarea>
                <button type="submit" class="btn-publicar">Publicar reseña</button>
            </form>
        </div>

        <div class="lista-resenas">
            <?php
            $resultado = $conexion->query("SELECT usuario, comentario, fecha FROM resenas ORDER BY fecha DESC");
            if ($resultado && $resultado->num_rows > 0):
                while ($fila = $resultado->fetch_assoc()):
            ?>
                    <div class="resena-card">
                        <div class="resena-header">
                            <div class="resena-avatar"></div>
                            <span class="resena-usuario"><?= htmlspecialchars($fila['usuario']) ?></span>
                        </div>
                        <p class="resena-texto"><?= htmlspecialchars($fila['comentario']) ?></p>
                    </div>
            <?php 
                endwhile;
            endif; 
            ?>
        </div>
    </section>

    <script>
        let posicionCarrusel = 0;
        function moverCarrusel(direccion) {
            const track = document.querySelector('.carrusel-track');
            const totalSlides = document.querySelectorAll('.carrusel-slide').length;
            posicionCarrusel += direccion;
            if (posicionCarrusel < 0) posicionCarrusel = totalSlides - 1;
            if (posicionCarrusel >= totalSlides) posicionCarrusel = 0;
            track.style.transform = `translateX(-${posicionCarrusel * 100}%)`;
        }

        function mostrarInfo(id) {
            const nombreJuego = 'Juego ' + id;
            document.getElementById('modalTitulo').innerText = nombreJuego;
            document.getElementById('inputJuegoNombre').value = nombreJuego;
            document.getElementById('modalJuego').classList.add('activo');
        }

        function cerrarInfo() {
            document.getElementById('modalJuego').classList.remove('activo');
        }

        function abrirCarrito() {
            document.getElementById('modalCarrito').classList.add('activo');
        }

        function cerrarCarrito() {
            document.getElementById('modalCarrito').classList.remove('activo');
        }

        function abrirCheckout() {
            cerrarCarrito();
            document.getElementById('modalCheckout').classList.add('activo');
        }

        function cerrarCheckout() {
            document.getElementById('modalCheckout').classList.remove('activo');
        }
    </script>

</body>
</html>