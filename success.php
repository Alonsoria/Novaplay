<?php
session_start();
require 'paypal_config.php';

use PayPal\Api\Payment;
use PayPal\Api\PaymentExecution;

// Verifica los parámetros de PayPal
if (!isset($_GET['paymentId']) || !isset($_GET['PayerID'])) {
    die('Pago inválido');
}

$paymentId = $_GET['paymentId'];
$payerId = $_GET['PayerID'];

$payment = Payment::get($paymentId, $paypal);
$execution = new PaymentExecution();
$execution->setPayerId($payerId);

$compraRealizada = false;
$codigosGenerados = [];
$bono = 0;
$total = 0;

try {
    // Ejecuta el pago
    $result = $payment->execute($execution, $paypal);
    $compraRealizada = true;

    // Recupera el total pagado desde la transacción
    $transactions = $payment->getTransactions();
    if (count($transactions) > 0) {
        $amount = $transactions[0]->getAmount();
        $total = floatval($amount->getTotal());
        $bono = $total * 0.10;
    }

    // Genera códigos únicos (uno por juego en carrito)
    $numJuegos = isset($_SESSION['carrito']) ? count($_SESSION['carrito']) : 1;
    for ($i = 0; $i < $numJuegos; $i++) {
        $codigosGenerados[] = strtoupper(bin2hex(random_bytes(8)));
    }

    // Vacía el carrito
    unset($_SESSION['carrito']);
} catch (Exception $ex) {
    echo "❌ Error al procesar el pago: " . $ex->getMessage();
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Compra completada - Novaplay</title>
    <link rel="stylesheet" href="style.css">
    <link rel="icon" href="./images/novaplay icono.png">
    <style>
        body {
            background-color: #0f0f1a;
            color: #fff;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        header {
            background: linear-gradient(to bottom, #7100c8, #3a0066);
            padding: 1rem;
            text-align: center;
        }
        .btn-cart {
            background: #9c27b0;
            color: white;
            border-radius: 10px;
            padding: 0.5rem 1rem;
            text-decoration: none;
            font-weight: bold;
            margin: 5px;
            transition: 0.3s;
        }
        .btn-cart:hover { background: #b832d8; }
        .compra-exito {
            text-align: center;
            margin-top: 30px;
            font-size: 1.3em;
            color: #bba7ff;
        }
        .codigo-item {
            background: #1f102f;
            border-radius: 10px;
            padding: 10px 15px;
            display: inline-block;
            color: #f0c800;
            font-weight: bold;
            margin: 10px;
            font-family: monospace;
        }
        .bono-info {
            text-align: center;
            margin-top: 15px;
            font-size: 1.2em;
            color: #fcd303;
        }
        footer {
            background: linear-gradient(to bottom, #3a0066, #7100c8);
            text-align: center;
            padding: 10px;
            position: fixed;
            bottom: 0;
            width: 100%;
            color: #ccc;
        }
        .cart-actions {
            text-align: center;
            margin-top: 20px;
        }
    </style>
</head>
<body>

<header>
    <h1>🛒 Carrito de compras</h1>
</header>

<main>
<?php if ($compraRealizada): ?>
    <div class="cart-actions">
        <a href="index.php" class="btn-cart">← Seguir comprando</a>
        <a href="carrito.php?action=clear" class="btn-cart">Vaciar carrito</a>
    </div>
    <div class="compra-exito">
        ¡Compra realizada correctamente!<br>
        Tus códigos de juego:
    </div>
    <div style="text-align:center;">
        <?php foreach ($codigosGenerados as $codigo): ?>
            <div class="codigo-item"><?php echo $codigo; ?></div>
        <?php endforeach; ?>
    </div>
    <div class="bono-info">
        Has recibido un bono del 10% de tu compra: <strong>$<?php echo number_format($bono, 2); ?></strong>
    </div>
<?php else: ?>
    <p style="text-align:center;">Error al procesar el pago.</p>
<?php endif; ?>
</main>

<footer>
    <p>&copy; <?php echo date("Y"); ?> Novaplay</p>
</footer>
</body>
</html>
