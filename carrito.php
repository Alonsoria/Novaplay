<?php
session_start();
include("config.php");

// Acciones (vaciar)
if (isset($_GET['action']) && $_GET['action'] === 'clear') {
    unset($_SESSION['carrito']);
    header("Location: carrito.php");
    exit;
}

$carrito = $_SESSION['carrito'] ?? [];
$compraRealizada = false;
$codigosGenerados = [];
$bono = 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['metodo_pago'])) {
    $compraRealizada = true;
    $numJuegos = count($carrito);
    for ($i = 0; $i < $numJuegos; $i++) {
        $codigosGenerados[] = strtoupper(bin2hex(random_bytes(8)));
    }
    $bono = isset($_POST['total_final']) ? floatval($_POST['total_final']) * 0.10 : 0;
    unset($_SESSION['carrito']); 
}

$productosEnCarrito = [];
$total = 0;

$pk = null;
$res1 = $conn->query("SHOW COLUMNS FROM productos LIKE 'id'");
if ($res1 && $res1->num_rows > 0) { $pk = 'id'; } 
else {
    $res2 = $conn->query("SHOW COLUMNS FROM productos LIKE 'id_producto'");
    if ($res2 && $res2->num_rows > 0) { $pk = 'id_producto'; }
}

if (!empty($carrito)) {
    foreach ($carrito as $key => $cantidad) {
        if (strpos($key, 'combo_') === 0) {
            $idCombo = (int)str_replace('combo_', '', $key);
            $sqlCombo = "SELECT * FROM combos WHERE id_combo = $idCombo";
            $resCombo = $conn->query($sqlCombo);
            if ($combo = $resCombo->fetch_assoc()) {
                $combo['cantidad'] = $cantidad;
                $combo['subtotal'] = $combo['precio'] * $cantidad;
                $combo['es_combo'] = true;
                $productosEnCarrito[] = $combo;
                $total += $combo['subtotal'];
            }
        } else {
            $idProd = (int)$key;
            $sqlProd = "SELECT * FROM productos WHERE $pk = $idProd";
            $resProd = $conn->query($sqlProd);
            if ($prod = $resProd->fetch_assoc()) {
                $prod['cantidad'] = $cantidad;
                $prod['subtotal'] = $prod['precio'] * $cantidad;
                $prod['es_combo'] = false;
                $productosEnCarrito[] = $prod;
                $total += $prod['subtotal'];
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Resumen de Compra - Novaplay</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="cart-body">

<header>
    <nav class="navbar">
        <ul class="nav-links">
            <li><a href="productos.php">Juegos</a></li>
            <li><a href="index.php#suscripciones">Suscripciones</a></li>
            <li><a href="productos.php?cat=accesorios">Accesorios</a></li>
        </ul>
        <div class="logo-container">
            <a href="index.php"><img src="./images/novaplay logo 2.png" alt="Logo" class="logo"></a>
        </div>
        <div class="header-right">
            <div class="search-box">
                <input type="text" placeholder="Buscar">
                <i class="fa-solid fa-magnifying-glass"></i>
            </div>
            <a href="login.php" class="user-icon"><i class="fa-solid fa-circle-user"></i> Usuario</a>
        </div>
    </nav>
</header>

<main class="cart-main">
    <?php if ($compraRealizada): ?>
        <div class="success-container">
            <div class="loading" id="loadingAnim">Procesando pago...</div>
            <div id="compraExito" class="compra-exito" style="display:none;">
                <h2>¡Compra Exitosa!</h2>
                <p>Tus códigos de activación:</p>
                <div class="codigos-grid">
                    <?php foreach ($codigosGenerados as $codigo): ?>
                        <span class="codigo-tag"><?php echo $codigo; ?></span>
                    <?php endforeach; ?>
                </div>
                <div class="bono-final">Bono acumulado: $<?php echo number_format($bono,2); ?></div>
                <a href="index.php" class="btn2">Volver al Inicio</a>
            </div>
        </div>
        <script>
            setTimeout(() => {
                document.getElementById('loadingAnim').style.display = 'none';
                document.getElementById('compraExito').style.display = 'block';
            }, 2000);
        </script>

    <?php elseif (empty($productosEnCarrito)): ?>
        <div class="empty-cart">
            <i class="fa-solid fa-cart-shopping"></i>
            <p>Tu carrito está vacío</p>
            <a href="index.php" class="btn2">Ir a la tienda</a>
        </div>

    <?php else: ?>
        <div class="cart-container-layout">
            <div class="cart-items-section">
                <?php foreach ($productosEnCarrito as $p): 
                    $img = (!empty($p['imagen']) && file_exists($p['imagen'])) ? $p['imagen'] : 'images/placeholder.png';
                ?>
                <div class="cart-item-card">
                    <img src="<?php echo $img; ?>" alt="Juego" class="item-img">
                    <div class="item-details">
                        <h3><?php echo htmlspecialchars($p['nombre']); ?></h3>
                        <span class="item-type"><?php echo ($p['es_combo']) ? 'Combo Especial' : 'Juego'; ?></span>
                        <p class="item-unit-price">Precio unitario: $<?php echo number_format($p['precio'], 2); ?> MXN</p>
                        <p class="item-total-price">Total: $<?php echo number_format($p['subtotal'], 2); ?> MXN</p>
                    </div>
                    <div class="item-controls">
                        <button class="ctrl-btn"><i class="fa-solid fa-minus"></i></button>
                        <span class="qty"><?php echo $p['cantidad']; ?></span>
                        <button class="ctrl-btn"><i class="fa-solid fa-plus"></i></button>
                        <button class="delete-btn"><i class="fa-solid fa-trash"></i></button>
                    </div>
                </div>
                <?php endforeach; ?>
                
                <div class="cart-extra-actions">
                    <a href="index.php" class="text-link">← Continuar comprando</a>
                    <a href="carrito.php?action=clear" class="text-link delete">Vaciar carrito</a>
                </div>
            </div>

            <aside class="cart-summary-sidebar">
                <h2>Resumen</h2>
                <div class="summary-list">
                    <?php foreach ($productosEnCarrito as $p): ?>
                        <div class="summary-item">
                            <span><?php echo htmlspecialchars($p['nombre']); ?> x<?php echo $p['cantidad']; ?></span>
                            <span>$<?php echo number_format($p['subtotal'], 0); ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="summary-total">
                    <span>Total:</span>
                    <span class="total-amount">$<?php echo number_format($total, 2); ?> MXN</span>
                </div>

                <div class="summary-bonus">
                    <i class="fa-solid fa-gift"></i>
                    <p>Ganarás <span>$<?php echo number_format($total*0.1, 2); ?></span> (10% de tu compra) que podrás usar en futuras compras</p>
                </div>

                <form method="post" class="payment-methods">
                    <input type="hidden" name="total_final" value="<?php echo $total; ?>">
                    <button type="submit" name="metodo_pago" value="tarjeta" class="pay-btn card-btn">
                        <i class="fa-solid fa-credit-card"></i> Pagar con tarjeta
                    </button>
                    <a href="crear_pago.php?total=<?php echo $total; ?>" class="pay-btn paypal-btn">
                        <i class="fa-brands fa-paypal"></i> Pagar con PayPal
                    </a>
                    <p class="currency-note">Todos los precios son en pesos mexicanos (MXN)</p>
                </form>
            </aside>
        </div>
    <?php endif; ?>
</main>

<footer>
    <div class="footer-content">
        <p>© 2026 Novaplay - E-commerce de Videojuegos</p>
        <div class="footer-links">
            <a href="#">TÉRMINOS Y CONDICIONES</a>
            <a href="#">AVISO DE PRIVACIDAD</a>
            <a href="#">POLÍTICA DE COOKIES</a>
        </div>
        <p class="legal-text">La información proporcionada será tratada conforme a nuestro Aviso de Privacidad.</p>
    </div>
</footer>

</body>
</html>