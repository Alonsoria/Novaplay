<?php
session_start();
include("config.php");

// Inicializar carrito
if (!isset($_SESSION['carrito'])) {
    $_SESSION['carrito'] = [];
}

// Agregar producto al carrito
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

// Obtener plataformas
$platformsArr = [];
$platRes = $conn->query("SELECT * FROM plataformas ORDER BY nombre");
if ($platRes) {
    while ($r = $platRes->fetch_assoc()) $platformsArr[] = $r;
}

// Filtro por plataforma
$filtroPlataforma = isset($_GET['plataforma']) ? (int)$_GET['plataforma'] : 0;

// Consulta productos
if ($filtroPlataforma > 0) {
    $sql = "SELECT DISTINCT p.* FROM productos p
            INNER JOIN producto_plataforma pp ON p.id_producto = pp.id_producto
            WHERE pp.id_plataforma = $filtroPlataforma
            ORDER BY p.nombre";
} else {
    $sql = "SELECT * FROM productos ORDER BY nombre";
}
$result = $conn->query($sql);

// contar items carrito
$cartCount = 0;
foreach ($_SESSION['carrito'] as $q) $cartCount += $q;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Novaplay</title>
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css"/>
</head>
<body>
<header>
    <div class="header-container">
        <img src="./images/novaplay logo 2.png" alt="Novaplay Logo" class="logo">

        <nav class="navbar">
            <ul>
                <li><a href="index.php">Inicio</a></li>
                <li><a href="#">Suscripciones</a></li>
                <li><a href="#">Combos</a></li>
                <li><a href="#">Acerca de nosotros</a></li>

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
                                    <a href="index.php?plataforma=<?php echo (int)$plat['id_plataforma']; ?>">
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
    <!-- 🎮 Carrusel de juegos destacados -->
    <section class="carrusel-destacados">
        <h2>Juegos Destacados</h2>
        <div class="swiper mySwiper">
            <div class="swiper-wrapper">
                <div class="swiper-slide"><img src="./images/gta.jpg" alt="GTA V"></div>
                <div class="swiper-slide"><img src="./images/tlou.jpg" alt="The Last of Us"></div>
                <div class="swiper-slide"><img src="./images/fifa24.jpg" alt="FIFA 24"></div>
                <div class="swiper-slide"><img src="./images/minecraft.jpg" alt="Minecraft"></div>
                <div class="swiper-slide"><img src="./images/halo.jpg" alt="Halo Infinite"></div>
            </div>
        </div>
    </section>

    <h2>Catálogo de Productos</h2>
    <div class="grid">
        <?php while($row = $result->fetch_assoc()) {
            $idProd = (int)$row['id_producto'];
            $imgPath = (!empty($row['imagen']) && file_exists($row['imagen'])) ? $row['imagen'] : './images/placeholder.png';
        ?>
            <div class="card">
                <img src="<?php echo htmlspecialchars($imgPath); ?>" alt="<?php echo htmlspecialchars($row['nombre']); ?>" class="product-img">
                <h3><?php echo htmlspecialchars($row['nombre']); ?></h3>
                <p><?php echo htmlspecialchars($row['descripcion']); ?></p>
                <p><strong>$<?php echo number_format($row['precio'],2); ?></strong></p>

                <!-- Plataformas -->
                <div class="plat-list">
                    <?php
                        $pSql = "SELECT pl.* FROM plataformas pl
                                 INNER JOIN producto_plataforma pp ON pl.id_plataforma = pp.id_plataforma
                                 WHERE pp.id_producto = $idProd";
                        $pRes = $conn->query($pSql);
                        while ($pRow = $pRes->fetch_assoc()) {
                            $icon = !empty($pRow['icono']) ? $pRow['icono'] : './images/platforms/placeholder.png';
                            echo '<img src="'.htmlspecialchars($icon).'" alt="'.htmlspecialchars($pRow['nombre']).'" title="'.htmlspecialchars($pRow['nombre']).'" class="plat-thumb">';
                        }
                    ?>
                </div>

                <a href="index.php?add=<?php echo $idProd; ?>" class="btn">Agregar</a>
            </div>
        <?php } ?>
    </div>
</main>

<footer>
    <p>&copy; <?php echo date("Y"); ?> Novaplay - E-commerce de Videojuegos</p>
</footer>

<!-- JS Swiper -->
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
<script>
var swiper = new Swiper(".mySwiper", {
    effect: "coverflow",
    grabCursor: true,
    centeredSlides: true,
    slidesPerView: 5,
    loop: true,
    coverflowEffect: {
        rotate: 0,
        stretch: 0,
        depth: 200,
        modifier: 2,
        slideShadows: false,
    },
    autoplay: {
        delay: 2500,
        disableOnInteraction: false,
    }
});
</script>

<!-- JS Toggle submenu -->
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
