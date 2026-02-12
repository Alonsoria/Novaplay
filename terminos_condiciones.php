<?php
session_start();
include("config.php");

if (!isset($_SESSION['carrito'])) {
    $_SESSION['carrito'] = [];
}

if (isset($_GET['add'])) {
    $id = (int)$_GET['add'];
    if ($id > 0) {
        if (!isset($_SESSION['carrito'][$id])) {
            $_SESSION['carrito'][$id] = 1;
        } else {
            $_SESSION['carrito'][$id]++;
        }
    }
    header("Location: index.php");
    exit;
}

$platformsArr = [];
$platRes = $conn->query("SELECT * FROM plataformas ORDER BY nombre");
if ($platRes) {
    while ($r = $platRes->fetch_assoc()) $platformsArr[] = $r;
}

$filtroPlataforma = isset($_GET['plataforma']) ? (int)$_GET['plataforma'] : 0;

if ($filtroPlataforma > 0) {
    $sql = "SELECT DISTINCT p.* FROM productos p
            INNER JOIN producto_plataforma pp ON p.id_producto = pp.id_producto
            WHERE pp.id_plataforma = $filtroPlataforma
            ORDER BY p.nombre";
} else {
    $sql = "SELECT * FROM productos ORDER BY nombre";
}
$result = $conn->query($sql);

$cartCount = 0;
foreach ($_SESSION['carrito'] as $q) $cartCount += $q;
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Novaplay</title>
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <link rel="stylesheet" href="./style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css"/>
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


<script>
(function(){
    const toggle = document.getElementById('platformToggle');
    const menu = document.getElementById('platformMenu');
    const closeBtn = document.getElementById('platformClose');

    function openMenu() {
        menu.classList.add('open');
        menu.setAttribute('aria-hidden','false');
        toggle.setAttribute('aria-expanded','true');
    }
    function closeMenu() {
        menu.classList.remove('open');
        menu.setAttribute('aria-hidden','true');
        toggle.setAttribute('aria-expanded','false');
    }
    toggle.addEventListener('click', function(e){
        e.stopPropagation();
        if (menu.classList.contains('open')) closeMenu();
        else openMenu();
    });
    closeBtn && closeBtn.addEventListener('click', function(e){
        e.stopPropagation();
        closeMenu();
    });
    document.addEventListener('click', function(e){
        if (!menu.contains(e.target) && !toggle.contains(e.target)) closeMenu();
    });
    document.addEventListener('keydown', function(e){
        if (e.key === 'Escape') closeMenu();
    });
})();
</script>
</body>
</html>
