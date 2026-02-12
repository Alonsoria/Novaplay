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

// Handle cart actions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'], $_POST['product_id'])) {
    $productId = $_POST['product_id'];
    $action = $_POST['action'];

    if ($action === 'increase') {
        if (isset($_SESSION['carrito'][$productId])) {
            $_SESSION['carrito'][$productId]++;
        }
    } elseif ($action === 'decrease') {
        if (isset($_SESSION['carrito'][$productId])) {
            $_SESSION['carrito'][$productId]--;
            if ($_SESSION['carrito'][$productId] <= 0) {
                unset($_SESSION['carrito'][$productId]);
            }
        }
    } elseif ($action === 'delete') {
        unset($_SESSION['carrito'][$productId]);
    }

    header("Location: carrito.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Carrito - Novaplay</title>
    <link rel="stylesheet" href="style.css">
    <link rel="icon" href="./images/novaplay icono.png">
    <style>
        .empty-cart {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 60vh;
            text-align: center;
        }

        .empty-cart img {
            max-width: 200px;
            margin-bottom: 20px;
        }

        .empty-cart p {
            font-size: 20px;
            color: #ffffff;
            font-weight: bold;
            background: linear-gradient(135deg, #ff00cc, #7a00ff);
            padding: 10px 20px;
            border-radius: 12px;
            box-shadow: 0 0 20px rgba(255, 0, 200, 0.5);
        }

        .cart-list li {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 15px;
            padding: 10px;
            background: #1f1f2e;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(255, 255, 255, 0.1);
        }

        .cart-list img {
            width: 100px;
            height: 100px;
            border-radius: 8px;
        }

        .cart-list .product-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .cart-list .product-actions {
            display: flex;
            align-items: center;
            gap: 10px;
        }

.cart-list .product-actions button {
    background: linear-gradient(135deg, #ff00cc, #7a00ff);
    border: none;
    padding: 5px 10px;
    border-radius: 5px;
    cursor: pointer;
    font-weight: bold;
    color: #fff;
    background-size: 200% 200%;
    animation: gradientShift 3s ease infinite;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.cart-list .product-actions button:hover {
    transform: scale(1.1);
    box-shadow: 0 0 12px rgba(255, 0, 200, 0.6);
}

@keyframes gradientShift {
    0% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
    100% { background-position: 0% 50%; }
}


        .cart-list .delete-btn {
            background: #ff4d4d;
            color: white;
        }

        .cart-list .delete-btn:hover {
            background: #ff6666;
        }

        .pagobtn {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 250px;
            height: 50px;
            gap: 10px;
            padding: 10px 20px;
            font-size: 16px;
            font-weight: bold;
            color: white;
            background-color: #9c27b0;
            border: none;
            border-radius: 20px;
            text-decoration: none;
            transition: background-color 0.3s;
        }

        .pagobtn:hover {
            background-color: #b832d8;
        }

        .pagobtn svg {
            vertical-align: middle;
        }

        .pagobtn img {
            vertical-align: middle;
        }
    </style>
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
        <div class="empty-cart">
            <img class="imagenMario" src="./images/sadMario.png" alt="Carrito vacío">
            <p>Vaya! parece que tus videojuegos están en otro castillo...</p>
        </div>
    <?php else: ?>
<div class="checkout-layout">
    <ul class="cart-list">
        <?php foreach ($productosEnCarrito as $p): 
            $img = (!empty($p['imagen']) && file_exists($p['imagen'])) ? $p['imagen'] : 'images/placeholder.png';
        ?>
            <li>
                <div class="product-info">
                    <img src="<?php echo htmlspecialchars($img); ?>" alt="<?php echo htmlspecialchars($p['nombre']); ?>" class="cart-img">
                    <strong>
                        <?php echo htmlspecialchars($p['nombre']); ?>
                        <?php if (!empty($p['es_combo'])): ?> <span style="color:#ffcc00;">(Combo)</span><?php endif; ?>
                    </strong>
                </div>
                    <div class="product-right">
                        <span class="subtotal">$<?php echo number_format($p['subtotal'], 2); ?></span>
                        <div class="product-actions">
                            <form method="post" action="carrito.php">
                                <input type="hidden" name="action" value="increase">
                                <input type="hidden" name="product_id" value="<?php echo $p[$pk]; ?>">
                                <button type="submit" class="btn-plus"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-plus-icon lucide-plus"><path d="M5 12h14"/><path d="M12 5v14"/></svg></button>
                            </form>
                            <form method="post" action="carrito.php">
                                <input type="hidden" name="action" value="decrease">
                                <input type="hidden" name="product_id" value="<?php echo $p[$pk]; ?>">
                                <button type="submit" class="btn-minus"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-minus-icon lucide-minus"><path d="M5 12h14"/></svg></button>
                            </form>
                            <span class="cantidad">x<?php echo $p['cantidad']; ?></span>
                            <form method="post" action="carrito.php">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="product_id" value="<?php echo $p[$pk]; ?>">
                                <button type="submit" class="btn-delete"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-trash2-icon lucide-trash-2"><path d="M10 11v6"/><path d="M14 11v6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6"/><path d="M3 6h18"/><path d="M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg></button>
                            </form>
                        </div>
                    </div>

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
                    <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-credit-card-icon lucide-credit-card"><rect width="20" height="14" x="2" y="5" rx="2"/><line x1="2" x2="22" y1="10" y2="10"/></svg> Tarjeta de crédito / débito
                </button>

                <a href="crear_pago.php?total=<?php echo $total_final ?? $total; ?>" class="pagobtn paypal">
                    <img src="images//paypal-logo.png" width="32px" height="32px" alt=""> Pagar con PayPal
                </a>

                <button type="submit" name="metodo_pago" value="tienda" class="pagobtn tienda">
                    <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-store-icon lucide-store"><path d="M15 21v-5a1 1 0 0 0-1-1h-4a1 1 0 0 0-1 1v5"/><path d="M17.774 10.31a1.12 1.12 0 0 0-1.549 0 2.5 2.5 0 0 1-3.451 0 1.12 1.12 0 0 0-1.548 0 2.5 2.5 0 0 1-3.452 0 1.12 1.12 0 0 0-1.549 0 2.5 2.5 0 0 1-3.77-3.248l2.889-4.184A2 2 0 0 1 7 2h10a2 2 0 0 1 1.653.873l2.895 4.192a2.5 2.5 0 0 1-3.774 3.244"/><path d="M4 10.95V19a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-8.05"/></svg> Pago en tienda
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
