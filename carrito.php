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
// Si no hay productos:
if (empty($carrito)) {
    $productosEnCarrito = [];
    $total = 0;
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

    // crear lista de ids seguros (enteros)
    $ids = array_map('intval', array_keys($carrito));
    // consulta por los ids
    $idsList = implode(",", $ids);
    $sql = "SELECT * FROM productos WHERE $pk IN ($idsList)";
    $result = $conn->query($sql);

    $productosEnCarrito = [];
    $total = 0;
    $productosMap = []; // id => row (para facilitar cálculos)
    while ($row = $result->fetch_assoc()) {
        $idRow = $row[$pk] ?? ($row['id'] ?? ($row['id_producto'] ?? null));
        $cantidad = $carrito[$idRow] ?? 0;
        $subtotal = $row['precio'] * $cantidad;
        $row['cantidad'] = $cantidad;
        $row['subtotal'] = $subtotal;
        $productosEnCarrito[] = $row;
        $total += $subtotal;
        $productosMap[$idRow] = $row;
    }

    // DETECCIÓN Y APLICACIÓN DE COMBOS (evitar doble uso de unidades)
    $descuentoTotal = 0;
    $alertas = [];

    // obtener combos y sus productos (GROUP_CONCAT facilita)
    $sqlCombos = "SELECT c.id, c.nombre, c.descuento, GROUP_CONCAT(cp.producto_id) AS productos
                  FROM combos c
                  JOIN combo_productos cp ON cp.combo_id = c.id
                  GROUP BY c.id";
    $resComb = $conn->query($sqlCombos);

    // copia de cantidades disponibles para evitar reutilizar unidades entre combos
    $available = $carrito;

    while ($combo = $resComb->fetch_assoc()) {
        $comboId = (int)$combo['id'];
        $comboName = $combo['nombre'];
        $comboDesc = floatval($combo['descuento']); // porcentaje
        $prodList = array_map('intval', explode(',', $combo['productos']));

        // determinar cuántas veces (veces entero >=1) puede aplicarse el combo según cantidades disponibles
        $timesPossible = PHP_INT_MAX;
        foreach ($prodList as $pid) {
            $qtyAvailable = $available[$pid] ?? 0;
            // asumimos 1 unidad requerida por producto en el combo
            $timesPossible = min($timesPossible, $qtyAvailable);
        }
        if ($timesPossible === PHP_INT_MAX || $timesPossible <= 0) {
            continue; // no se puede aplicar este combo
        }

        // aplicar combo 'timesPossible' veces
        $subtotalCombo = 0;
        foreach ($prodList as $pid) {
            // si ese producto no está en el mapa (tal vez producto ya no existe), saltar combo
            if (!isset($productosMap[$pid])) {
                $subtotalCombo = 0;
                break;
            }
            $precioProd = $productosMap[$pid]['precio'];
            $subtotalCombo += $precioProd * 1 * $timesPossible; // 1 unidad por producto * veces
        }
        if ($subtotalCombo <= 0) continue;

        // calcular descuento
        $discountAmount = $subtotalCombo * ($comboDesc / 100.0);
        $descuentoTotal += $discountAmount;

        // decrementar cantidades disponibles (para evitar reutilizar las mismas unidades en otro combo)
        foreach ($prodList as $pid) {
            $available[$pid] = max(0, $available[$pid] - $timesPossible);
        }

        // Alerta: si se aplicó una o más veces
        $alertas[] = "🎉 ¡Combo detectado!: {$comboName} x{$timesPossible} ({$comboDesc}% off)";
    }
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
    <a href="index.php" class="btn">← Seguir comprando</a>
    <a href="carrito.php?action=clear" class="btn" style="margin-left:10px;background:#444;">Vaciar carrito</a>
    <hr>

<?php if (empty($productosEnCarrito)): ?>
    <p>No hay productos en el carrito.</p>
<?php else: ?>
    <ul class="cart-list">
        <?php foreach ($productosEnCarrito as $p): 
            // detectar id del producto en la fila
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

    <?php if (!empty($alertas)): ?>
        <?php foreach ($alertas as $a): ?>
            <div class="combo-alert"><?php echo $a; ?></div>
        <?php endforeach; ?>
    <?php endif; ?>

    <p><strong>Total: $<?php echo number_format($total,2); ?></strong></p>

    <?php if (!empty($descuentoTotal) && $descuentoTotal > 0): ?>
        <p class="descuento">Descuento combos: -$<?php echo number_format($descuentoTotal,2); ?></p>
        <p class="final">Total con descuento: $<?php echo number_format($total - $descuentoTotal,2); ?></p>
    <?php endif; ?>

<?php endif; ?>
</main>

<footer>
    <p>&copy; <?php echo date("Y"); ?> Novaplay</p>
</footer>
</body>
</html>
