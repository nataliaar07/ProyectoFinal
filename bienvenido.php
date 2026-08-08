<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();

// Conexión segura
$servidor = "localhost";
$usuario_bd = "root";
$contrasena_bd = "";
$base_datos = "login_db";

$conexion = @new mysqli($servidor, $usuario_bd, $contrasena_bd, $base_datos);

// Manejo de usuario logueado
$usuario_actual = isset($_SESSION['usuario']) ? $_SESSION['usuario'] : 'natalia';

// Inicializar carrito
if (!isset($_SESSION['carrito'])) {
    $_SESSION['carrito'] = array();
}

// 1. AGREGAR AL CARRITO
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['agregar_carrito'])) {
    $juego_nombre = $_POST['juego_nombre'];
    $juego_precio = floatval($_POST['juego_precio']);

    $_SESSION['carrito'][] = array(
        'nombre' => $juego_nombre,
        'precio' => $juego_precio
    );
    header("Location: bienvenido.php");
    exit();
}

// 2. VACIAR CARRITO
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['vaciar_carrito'])) {
    $_SESSION['carrito'] = array();
    header("Location: bienvenido.php");
    exit();
}

// 3. PROCESAR COMPRA
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['procesar_compra'])) {
    if (!empty($_SESSION['carrito']) && !$conexion->connect_error) {
        foreach ($_SESSION['carrito'] as $item) {
            $juego = $item['nombre'];
            $precio = $item['precio'];
            $sql_compra = "INSERT INTO compras (usuario, juego, cantidad, precio_unitario, total) 
                          VALUES ('$usuario_actual', '$juego', 1, $precio, $precio)";
            @$conexion->query($sql_compra);
        }
        $_SESSION['carrito'] = array();
        $mensaje = "¡Compra realizada con éxito!";
    }
}

// 4. PUBLICAR RESEÑA
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['publicar_resena'])) {
    $comentario = $_POST['comentario'];
    if (!empty($comentario) && !$conexion->connect_error) {
        $sql_resena = "INSERT INTO resenas (usuario, comentario, puntuacion) VALUES ('$usuario_actual', '$comentario', 5)";
        @$conexion->query($sql_resena);
        header("Location: bienvenido.php");
        exit();
    }
}

// CATÁLOGO DE VIDEOJUEGOS
$juegos_catalogo = array(
    array("nombre" => "Valorant", "precio" => 0.00, "img" => "https://images.unsplash.com/photo-1542751371-adc38448a05e?auto=format&fit=crop&w=400&q=80"),
    array("nombre" => "GTA V", "precio" => 29.99, "img" => "https://images.unsplash.com/photo-1511512578047-dfb367046420?auto=format&fit=crop&w=400&q=80"),
    array("nombre" => "Fortnite", "precio" => 0.00, "img" => "https://images.unsplash.com/photo-1589241062272-c0a000072dfa?auto=format&fit=crop&w=400&q=80"),
    array("nombre" => "Cyberpunk 2077", "precio" => 59.99, "img" => "https://images.unsplash.com/photo-1607604276583-eef5d076aa5f?auto=format&fit=crop&w=400&q=80"),
    array("nombre" => "Minecraft", "precio" => 26.95, "img" => "https://images.unsplash.com/photo-1538481199705-c710c4e965fc?auto=format&fit=crop&w=400&q=80"),
    array("nombre" => "Elden Ring", "precio" => 59.99, "img" => "https://images.unsplash.com/photo-1550745165-9bc0b252726f?auto=format&fit=crop&w=400&q=80")
);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Next Level - Catálogo y Carrito</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { background-color: #0c021a; color: #ffffff; font-family: sans-serif; padding: 20px 40px; }
        header { display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid #381b66; padding-bottom: 15px; margin-bottom: 30px; }
        .logo { font-size: 2rem; font-weight: 900; color: #ffffff; }
        .logo span { color: #a83bff; }
        .btn-logout { background: #e63946; color: white; padding: 8px 16px; text-decoration: none; border-radius: 4px; font-weight: bold; }
        
        .main-layout { display: grid; grid-template-columns: 2.5fr 1fr; gap: 30px; margin-bottom: 40px; }
        .section-title { font-size: 1.4rem; margin-bottom: 20px; text-transform: uppercase; }
        
        .games-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 20px; }
        .game-card { background-color: #17092c; border: 1px solid #381b66; border-radius: 8px; padding: 15px; display: flex; flex-direction: column; justify-content: space-between; }
        .game-img { width: 100%; height: 120px; object-fit: cover; border-radius: 6px; margin-bottom: 12px; }
        .game-title { font-weight: bold; font-size: 1.1rem; margin-bottom: 6px; }
        .game-price { color: #ffe600; font-weight: bold; margin-bottom: 12px; }
        .btn-add { width: 100%; padding: 10px; background: linear-gradient(90deg, #4f52f5, #7d2ae8); border: none; border-radius: 4px; color: white; font-weight: bold; cursor: pointer; }
        
        .cart-panel { background-color: #17092c; border: 1px solid #381b66; border-radius: 8px; padding: 20px; height: fit-content; }
        .cart-list { list-style: none; margin-bottom: 15px; max-height: 250px; overflow-y: auto; }
        .cart-item { display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #2b134d; }
        .cart-total { display: flex; justify-content: space-between; font-weight: bold; font-size: 1.1rem; margin-bottom: 15px; border-top: 1px solid #381b66; padding-top: 10px; }
        .btn-buy { width: 100%; padding: 14px; background: #ffe600; border: none; border-radius: 4px; color: #0c021a; font-weight: 900; cursor: pointer; margin-bottom: 8px; }
        .btn-buy:disabled { background: #444; color: #888; cursor: not-allowed; }
        .btn-clear { width: 100%; padding: 8px; background: transparent; border: 1px solid #e63946; color: #e63946; border-radius: 4px; cursor: pointer; }
        
        .community-section { background-color: #17092c; border: 1px solid #381b66; border-radius: 8px; padding: 25px; }
        .review-textarea { width: 100%; height: 90px; background-color: #0c021a; border: 1px solid #381b66; border-radius: 6px; color: white; padding: 12px; resize: none; margin-bottom: 15px; }
        .btn-publish { background: linear-gradient(90deg, #4f52f5, #7d2ae8); color: white; border: none; padding: 10px 20px; border-radius: 4px; font-weight: bold; cursor: pointer; margin-bottom: 25px; }
        .review-card { background-color: #0c021a; border: 1px solid #2b134d; border-radius: 6px; padding: 15px; margin-bottom: 10px; }
        .review-user { font-weight: bold; color: #a83bff; margin-bottom: 5px; }
    </style>
</head>
<body>

    <header>
        <div class="logo">NEXT <span>LEVEL</span></div>
        <div>
            <span>Hola, <strong><?php echo htmlspecialchars($usuario_actual); ?></strong></span>
            <a href="formulario.html" class="btn-logout" style="margin-left: 15px;">Cerrar sesión</a>
        </div>
    </header>

    <?php if (isset($mensaje)): ?>
        <p style="background: #28a745; color: white; padding: 10px; border-radius: 5px; margin-bottom: 20px;"><?php echo $mensaje; ?></p>
    <?php endif; ?>

    <div class="main-layout">
        
        <section>
            <h2 class="section-title">Catálogo de juegos</h2>
            <div class="games-grid">
                <?php foreach ($juegos_catalogo as $juego): ?>
                    <div class="game-card">
                        <img src="<?php echo $juego['img']; ?>" class="game-img" alt="<?php echo $juego['nombre']; ?>">
                        <div class="game-title"><?php echo $juego['nombre']; ?></div>
                        <div class="game-price"><?php echo $juego['precio'] == 0 ? "Gratis" : "$" . number_format($juego['precio'], 2); ?></div>
                        
                        <form action="bienvenido.php" method="POST">
                            <input type="hidden" name="juego_nombre" value="<?php echo $juego['nombre']; ?>">
                            <input type="hidden" name="juego_precio" value="<?php echo $juego['precio']; ?>">
                            <button type="submit" name="agregar_carrito" class="btn-add">Agregar al carrito</button>
                        </form>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>

        <aside class="cart-panel">
            <h2 class="section-title">Tu carrito</h2>
            <ul class="cart-list">
                <?php 
                $total_carrito = 0;
                if (!empty($_SESSION['carrito'])): 
                    foreach ($_SESSION['carrito'] as $item): 
                        $total_carrito += $item['precio'];
                ?>
                        <li class="cart-item">
                            <span><?php echo htmlspecialchars($item['nombre']); ?></span>
                            <span style="color: #ffe600; font-weight: bold;"><?php echo $item['precio'] == 0 ? "Gratis" : "$" . number_format($item['precio'], 2); ?></span>
                        </li>
                    <?php endforeach; ?>
                <?php else: ?>
                    <li class="cart-item"><span>El carrito está vacío</span></li>
                <?php endif; ?>
            </ul>

            <div class="cart-total">
                <span>Total:</span>
                <span style="color: #ffe600;">$<?php echo number_format($total_carrito, 2); ?></span>
            </div>

            <form action="bienvenido.php" method="POST">
                <button type="submit" name="procesar_compra" class="btn-buy" <?php echo empty($_SESSION['carrito']) ? 'disabled' : ''; ?>>
                    LISTO PARA COMPRAR
                </button>
            </form>

            <?php if (!empty($_SESSION['carrito'])): ?>
                <form action="bienvenido.php" method="POST">
                    <button type="submit" name="vaciar_carrito" class="btn-clear">Vaciar carrito</button>
                </form>
            <?php endif; ?>
        </aside>

    </div>

    <section class="community-section">
        <h2 class="section-title">Comunidad</h2>
        
        <form action="bienvenido.php" method="POST">
            <textarea name="comentario" class="review-textarea" placeholder="Escribe tu reseña..." required></textarea>
            <br>
            <button type="submit" name="publicar_resena" class="btn-publish">Publicar reseña</button>
        </form>

        <div>
            <?php
            if (!$conexion->connect_error) {
                $sql_resenas = "SELECT * FROM resenas ORDER BY id DESC";
                $res = @$conexion->query($sql_resenas);
                if ($res && $res->num_rows > 0) {
                    while ($fila = $res->fetch_assoc()) {
                        echo '<div class="review-card">';
                        echo '<div class="review-user">' . htmlspecialchars($fila['usuario']) . '</div>';
                        echo '<div>' . htmlspecialchars($fila['comentario']) . '</div>';
                        echo '</div>';
                    }
                } else {
                    echo '<p style="color: #aaa;">Sé el primero en publicar una reseña.</p>';
                }
            }
            ?>
        </div>
    </section>

</body>
</html>