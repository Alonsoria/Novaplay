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

if (empty($carrito)) {
    $productosEnCarrito = [];
    $total = 0;
    $total_final = 0;
} else {
    // detectar columna PK en productos
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

    $ids = array_map('intval', array_keys($carrito));
    $idsList = implode(",", $ids);
    $sql = "SELECT * FROM productos WHERE $pk IN ($idsList)";
    $result = $conn->query($sql);

    $productosEnCarrito = [];
    $total = 0;
    while ($row = $result->fetch_assoc()) {
        $idRow = $row[$pk] ?? ($row['id'] ?? ($row['id_producto'] ?? null));
        $cantidad = $carrito[$idRow] ?? 0;
        $subtotal = $row['precio'] * $cantidad;
        $row['cantidad'] = $cantidad;
        $row['subtotal'] = $subtotal;
        $productosEnCarrito[] = $row;
        $total += $subtotal;
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
</head>
<body>
<header>
    <h1>🛒 Carrito de compras</h1>
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
            document.getElementById('compraExito').style.display = 'block';
            document.getElementById('codigosList').style.display = 'block';
        }, 3000);
    </script>
    <div id="compraExito" class="compra-exito" style="display:none;">
        ¡Compra realizada correctamente!<br>
        Tus códigos de juego:
    </div>
    <div id="codigosList" class="codigos-list" style="display:none;">
        <?php foreach ($codigosGenerados as $codigo): ?>
            <div class="codigo-item"><?php echo $codigo; ?></div>
        <?php endforeach; ?>
        <div class="bono-info">
            Has recibido un bono del 10% de tu compra: <strong>$<?php echo number_format($bono,2); ?></strong>
        </div>
    </div>
<?php elseif (empty($productosEnCarrito)): ?>
    <p>No hay productos en el carrito.</p>
<?php else: ?>
    <ul class="cart-list">
        <?php foreach ($productosEnCarrito as $p): 
            $idRow = $p['id'] ?? ($p['id_producto'] ?? ($p['ID_producto'] ?? null));
            $img = (!empty($p['imagen']) && file_exists($p['imagen'])) ? $p['imagen'] : 'images/placeholder.png';
        ?>
            <li>
                <img src="<?php echo htmlspecialchars($img); ?>" alt="<?php echo htmlspecialchars($p['nombre']); ?>" class="cart-img">
                <strong><?php echo htmlspecialchars($p['nombre']); ?></strong>
                (x<?php echo $p['cantidad']; ?>)
                - $<?php echo number_format($p['subtotal'], 2); ?>
            </li>
        <?php endforeach; ?>
    </ul>

    <p><strong>Total: $<?php echo number_format($total,2); ?></strong></p>
    <div class="bono-info">
        Por tu compra recibirás un bono del 10%: <strong>$<?php echo number_format($bono,2); ?></strong>
    </div>

    <div class="pago-info">¿Cómo deseas pagar?</div>
    <form method="post" id="formPago">
        <input type="hidden" name="total_final" value="<?php echo $total_final ?? $total; ?>">
        <div class="pago-metodos">
            <button type="submit" name="metodo_pago" value="tarjeta" class="pago-btn btn">Tarjeta de crédito/débito</button>
            <button type="submit" name="metodo_pago" value="paypal" class="pago-btn btn">PayPal</button>
            <button type="submit" name="metodo_pago" value="tienda" class="pago-btn btn">Pago en tienda</button>
        </div>
    </form>
<?php endif; ?>
</main>

<footer>
    <p>&copy; <?php echo date("Y"); ?> Novaplay</p>
</footer>
</body>
</html>
