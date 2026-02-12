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
    <?php else: ?>
        <p style="text-align:center;">Error al procesar el pago.</p>
    <?php endif; ?>
</main>

<footer>
    <p>&copy; <?php echo date("Y"); ?> Novaplay</p>
</footer>
</body>
</html>
