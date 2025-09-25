<?php
session_start();
include 'config.php'; 

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
    header("Location: productos.php");
    exit;
}

$platformsArr = [];
$platRes = $conn->query("SELECT * FROM plataformas ORDER BY nombre");
if ($platRes) {
    while ($r = $platRes->fetch_assoc()) $platformsArr[] = $r;
}

$categorias = [
    "suscripcion" => "Suscripciones disponibles",
    "accesorio"   => "Accesorios disponibles",
    "videojuego"  => "Videojuegos disponibles",
    "tarjeta"     => "Tarjetas prepago",
    "DLC"         => "Contenido adicional (DLC)",
    "moneda_virtual" => "Monedas virtuales",
    "paquete"     => "Paquetes especiales"
];

$cartCount = 0;
foreach ($_SESSION['carrito'] as $q) $cartCount += $q;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Novaplay - Productos</title>
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <link rel="stylesheet" href="style.css">
</head>
<body>
<header>
    <div class="header-container">
        <img src="./images/novaplay logo 2.png" alt="Novaplay Logo" class="logo">
        <nav class="navbar">
            <ul>
                <li><a href="index.php">Inicio</a></li>
                <li><a href="productos.php">Productos</a></li>
                <li><a href="combos.php">Combos</a></li>
                <li><a href="about_us.php">Acerca de nosotros</a></li>

                <!-- MENU DE PLATAFORMAS -->
                <li class="platforms-wrapper">
                    <button id="platformToggle" class="platform-toggle" aria-expanded="false">
                        Plataformas ▾
                    </button>
                    <div id="platformMenu" class="submenu" aria-hidden="true" role="menu">
                        <button id="platformClose" class="submenu-close" aria-label="Cerrar menú">✕</button>
                        <ul>
                            <?php foreach($platformsArr as $plat): ?>
                                <li>
                                    <a href="productos.php?plataforma=<?php echo (int)$plat['id_plataforma']; ?>">
                                        <img src="<?php echo htmlspecialchars($plat['icono']); ?>" alt="<?php echo htmlspecialchars($plat['nombre']); ?>" class="plat-icon"> 
                                        <?php echo htmlspecialchars($plat['nombre']); ?>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </li>

                <li><a href="carrito.php">🛒 Carrito <span class="cart-badge"><?php echo $cartCount; ?></span></a></li>
            </ul>
        </nav>

        <div class="user-login">
            <a href="login.php" class="btn-login">👤 Iniciar Sesión</a>
        </div>
    </div>
</header>

<main>
    <?php
    foreach ($categorias as $clave => $titulo):
        $sql = "SELECT * FROM productos WHERE categoria='$clave' ORDER BY nombre";
        $result = $conn->query($sql);
        if ($result && $result->num_rows > 0):
    ?>
        <h2><?php echo $titulo; ?></h2>
        <div class="grid">
            <?php while($row = $result->fetch_assoc()):
                $idProd = (int)$row['id_producto'];
                $imgPath = (!empty($row['imagen']) && file_exists($row['imagen'])) ? $row['imagen'] : './images/placeholder.png';
            ?>
            <div class="card">
                <img src="<?php echo htmlspecialchars($imgPath); ?>" alt="<?php echo htmlspecialchars($row['nombre']); ?>" class="product-img">
                <h3><?php echo htmlspecialchars($row['nombre']); ?></h3>
                <p><?php echo htmlspecialchars($row['descripcion']); ?></p>
                <p><strong>$<?php echo number_format($row['precio'],2); ?></strong></p>

                <!-- Plataformas del producto -->
                <div class="plat-list">
                    <?php
                    $pSql = "SELECT pl.* FROM plataformas pl
                             INNER JOIN producto_plataforma pp ON pl.id_plataforma = pp.id_plataforma
                             WHERE pp.id_producto = $idProd";
                    $pRes = $conn->query($pSql);
                    while ($pRow = $pRes->fetch_assoc()):
                        $icon = !empty($pRow['icono']) ? $pRow['icono'] : './images/platforms/placeholder.png';
                    ?>
                        <img src="<?php echo htmlspecialchars($icon); ?>" alt="<?php echo htmlspecialchars($pRow['nombre']); ?>" title="<?php echo htmlspecialchars($pRow['nombre']); ?>" class="plat-thumb">
                    <?php endwhile; ?>
                </div>

                <a href="productos.php?add=<?php echo $idProd; ?>" class="btn">Agregar</a>
            </div>
            <?php endwhile; ?>
        </div>
    <?php
        endif;
    endforeach;
    ?>
</main>

<footer>
    <p>&copy; <?php echo date("Y"); ?> Novaplay - E-commerce de Videojuegos</p>
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
