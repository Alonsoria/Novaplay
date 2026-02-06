<?php
// carrito.php
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
    // Simula compra y genera códigos
    $compraRealizada = true;
    $numJuegos = count($carrito);
    for ($i = 0; $i < $numJuegos; $i++) {
        $codigosGenerados[] = strtoupper(bin2hex(random_bytes(8)));
    }
    $bono = isset($_POST['total_final']) ? floatval($_POST['total_final']) * 0.10 : 0;
    unset($_SESSION['carrito']); // Vacía el carrito tras compra
}

$productosEnCarrito = [];
$total = 0;
$total_final = 0;

// Detectar columna PK en productos
$pk = null;
$res1 = $conn->query("SHOW COLUMNS FROM productos LIKE 'id'");
if ($res1 && $res1->num_rows > 0) {
    $pk = 'id';
} else {
    $res2 = $conn->query("SHOW COLUMNS FROM productos LIKE 'id_producto'");
    if ($res2 && $res2->num_rows > 0) {
        $pk = 'id_producto';
    }
}

if (!empty($carrito)) {
    foreach ($carrito as $key => $cantidad) {
        if (strpos($key, 'combo_') === 0) {
            // Es un combo
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
            // Es un producto normal
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
    $total_final = $total;
    $bono = $total_final * 0.10;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Carrito - Novaplay</title>
    <link rel="stylesheet" href="style.css">
    <link rel="icon" href="./images/novaplay icono.png">
</head>
<body>
<header>
    <div class="header-container">
        <nav class="navbar">
            <ul>
                <li><a href="productos.php">Productos</a></li>
                <li><a href="combos.php">Combos</a></li>

                                <!-- LOGO -->
                <li class="logo-item">
                    <a href="index.php">
                        <img src="./images/novaplay logo 2.png" alt="Novaplay Logo" class="logo">
                    </a>
                </li>
                
                <li><a href="about_us.php">Acerca de nosotros</a></li>

                <!-- LOGIN -->
                <li class="login-item">
                    <a href="login.php" class="login-btn">Login</a>
                </li>
            </ul>
        </nav>
    </div>
</header>
<main>
    <div class="cart-actions">
        <a href="index.php" class="btn btn-cart">← Seguir comprando</a>
        <a href="carrito.php?action=clear" class="btn btn-cart btn-clear">Vaciar carrito</a>
    </div>
    <hr>
<?php if ($compraRealizada): ?>
    <div class="loading" id="loadingAnim">Procesando pago...</div>

    <script>
        setTimeout(function(){
            document.getElementById('loadingAnim').style.display = 'none';
            document.getElementById('compraWrapper').style.display = 'flex';
        }, 3000);
    </script>

    <style>
        .compra-wrapper {
            display: none;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 60vh;
            gap: 25px;
        }

        .compra-card {
            background: #151525;
            border-radius: 16px;
            padding: 30px 40px;
            text-align: center;
            box-shadow: 0 0 25px rgba(255, 0, 200, 0.4);
            max-width: 500px;
            width: 100%;
        }

        .compra-exito {
            font-size: 26px;
            font-weight: bold;
            color: #ffffff;
            margin-bottom: 15px;
        }

        .codigos-list {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 12px;
            margin-top: 15px;
        }

        .codigo-item {
            background-color: #2c2c2c;
            color: #ffcc00;
            padding: 10px 16px;
            border-radius: 8px;
            font-size: 18px;
            letter-spacing: 1px;
            box-shadow: 0 0 10px rgba(255, 204, 0, 0.4);
        }

        .bono-info {
            margin-top: 20px;
            padding: 15px 20px;
            background: linear-gradient(135deg, #ff00cc, #7a00ff);
            color: #ffffff;
            font-size: 20px;
            font-weight: bold;
            border-radius: 12px;
            box-shadow: 0 0 20px rgba(255, 0, 200, 0.5);
        }

        .loading {
            text-align: center;
            font-size: 22px;
            color: #ffffff;
            margin-top: 80px;
            animation: pulse 1.5s infinite;
        }

        @keyframes pulse {
            0% { opacity: 1; }
            50% { opacity: 0.5; }
            100% { opacity: 1; }
        }
    </style>

    <div id="compraWrapper" class="compra-wrapper">
        <div class="compra-card">
            <div class="compra-exito">
                ¡Compra realizada correctamente! 🎉
            </div>
<div class="codigos-list">
    <?php foreach ($codigosGenerados as $codigo): ?>
        <div class="codigo-item">
            <span class="codigo-text"><?php echo $codigo; ?></span>
            <button class="copy-btn" onclick="copiarCodigo('<?php echo $codigo; ?>', this)" title="Copiar código">
                📋
            </button>
        </div>
    <?php endforeach; ?>
    <script>
function copiarCodigo(codigo, boton) {
    navigator.clipboard.writeText(codigo).then(() => {
        // Quitar mensaje anterior si existe
        const msgExistente = boton.parentElement.querySelector('.copy-msg');
        if (msgExistente) msgExistente.remove();

        // Crear mensaje
        const msg = document.createElement('span');
        msg.className = 'copy-msg';
        msg.innerText = 'Copiado al portapapeles';

        boton.parentElement.appendChild(msg);

        // Quitar mensaje después de 2 segundos
        setTimeout(() => {
            msg.remove();
        }, 2000);
    });
}
</script>

</div>

            <div class="bono-info">
                Has recibido un bono del 10% de tu compra: <br>
                <strong>$<?php echo number_format($bono, 2); ?></strong>
            </div>
        </div>
    </div>
    <?php elseif (empty($productosEnCarrito)): ?>
        <p>No hay productos en el carrito.</p>
    <?php else: ?>
<div class="checkout-layout">
    <ul class="cart-list">
        <?php foreach ($productosEnCarrito as $p): 
            $img = (!empty($p['imagen']) && file_exists($p['imagen'])) ? $p['imagen'] : 'images/placeholder.png';
        ?>
            <li>
                <img src="<?php echo htmlspecialchars($img); ?>" alt="<?php echo htmlspecialchars($p['nombre']); ?>" class="cart-img">
                <strong style="margin-right: 10px; margin-left: 10px">
                    <?php echo htmlspecialchars($p['nombre']); ?>
                    <?php if (!empty($p['es_combo'])): ?> <span style="color:#ffcc00;">(Combo)</span><?php endif; ?>
                </strong>
                (x<?php echo $p['cantidad']; ?>)
                - $<?php echo number_format($p['subtotal'], 2); ?>
            </li>
        <?php endforeach; ?>
    </ul>

    <div class="metodoPago2">
        <p class="total-text"><strong>Total: $<?php echo number_format($total,2); ?></strong></p>

        <div class="bono-info">
            Por tu compra recibirás un bono del 10%:<br>
            <strong>$<?php echo number_format($bono,2); ?></strong>
        </div>

        <div class="pago-info">¿Cómo deseas pagar?</div>

        <form method="post" id="formPago">
            <input type="hidden" name="total_final" value="<?php echo $total_final ?? $total; ?>">

            <div class="pago-metodos">
                <button type="submit" name="metodo_pago" value="tarjeta" class="pagobtn tarjeta"
                    onclick="window.location.href='pago_tarjeta.php'; return false;">
                    💳 Tarjeta de crédito / débito
                </button>

                <a href="crear_pago.php?total=<?php echo $total_final ?? $total; ?>" class="pagobtn paypal">
                    🅿️ Pagar con PayPal
                </a>

                <button type="submit" name="metodo_pago" value="tienda" class="pagobtn tienda">
                    🏪 Pago en tienda
                </button>
            </div>
        </form>
    </div>
</div>
<?php endif; ?>
        </div>
    </div> 
</main>

<footer>
    <p>&copy; <?php echo date("Y"); ?> Novaplay</p>
</footer>
</body>
</html>
