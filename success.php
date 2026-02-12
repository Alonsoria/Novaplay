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
    <title>Novaplay</title>
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <link rel="stylesheet" href="./style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css">
    <link rel="icon" href="./images/novaplay icono.png">
</head>
<header>
    <div class="header-container">
        <nav class="navbar">
            <ul>
                <li><a href="productos.php">Productos</a></li>
                <li><a href="combos.php">Combos</a></li>
                <li><a href="about_us.php">Acerca de nosotros</a></li>

                <!-- LOGO -->
                <li class="logo-item">
                    <a href="index.php">
                        <img src="./images/novaplay logo 2.png" alt="Novaplay Logo" class="logo">
                    </a>
                </li>

                <!-- MENU DE PLATAFORMAS -->
                <li class="platforms-wrapper">
                    <button id="platformToggle" class="platform-toggle" aria-expanded="false">
                        Plataformas ▼
                    </button>
                    <div id="platformMenu" class="submenu" aria-hidden="true" role="menu">
                        <button id="platformClose" class="submenu-close" aria-label="Cerrar menú">✕</button>
                        <ul>
                            <?php foreach($platformsArr as $plat): ?>
                                <li>
                                    <a href="index.php?plataforma=<?php echo (int)$plat['id_plataforma']; ?>">
                                        <img src="<?php echo htmlspecialchars($plat['icono']); ?>" alt="<?php echo htmlspecialchars($plat['nombre']); ?>" class="plat-icon">
                                        <?php echo htmlspecialchars($plat['nombre']); ?>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </li>

                <li>
                    <a href="carrito.php">
                        Carrito <span class="cart-badge"><?php echo $cartCount; ?></span>
                    </a>
                </li>

                <!-- LOGIN -->
                <li class="login-item">
                    <a href="login.php" class="login-btn">Login</a>
                </li>
            </ul>
        </nav>
    </div>
</header>

<footer class="footer">
    <div class="footer-container">
        <p>&copy; <?php echo date("Y"); ?> Novaplay - E-commerce de Videojuegos</p>
        <div class="footer-links">
            <a href="aviso_privacidad.php" class="footer-links">Aviso de Privacidad</a>
            <span>|</span>
            <a href="terminos_condiciones.php" class="footer-links">Términos y Condiciones</a>
            <span>|</span>
            <a href="politica_cookies.php" class="footer-links">Política de Cookies</a>
        </div>
        <p class="footer-note">
            La información proporcionada será tratada conforme a nuestro Aviso de Privacidad.
        </p>
    </div>
</footer>
</body>
</html>
