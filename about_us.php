<?php
session_start();
include("config.php");

if (!isset($_SESSION['carrito'])) {
    $_SESSION['carrito'] = [];
}

// Plataformas para el menú
$platformsArr = [];
$platRes = $conn->query("SELECT * FROM plataformas ORDER BY nombre");
if ($platRes) {
    while ($r = $platRes->fetch_assoc()) $platformsArr[] = $r;
}

// Contar carrito
$cartCount = 0;
foreach ($_SESSION['carrito'] as $q) $cartCount += $q;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Acerca de Nosotros - Novaplay</title>
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
                                    <a href="index.php?plataforma=<?php echo (int)$plat['id_plataforma']; ?>">
                                        <img src="<?php echo htmlspecialchars($plat['icono']); ?>" alt="<?php echo htmlspecialchars($plat['nombre']); ?>" class="plat-icon"> 
                                        <?php echo htmlspecialchars($plat['nombre']); ?>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </li>

                <li><a href="carrito.php"> Carrito <span class="cart-badge"><?php echo $cartCount; ?></span></a></li>
            </ul>
        </nav>

        <div class="user-login">
            <a href="login.php" class="btn-login">Iniciar Sesión</a>
        </div>
    </div>
</header>

<main>
    <section class="aboutus-section">
        <h1 class="aboutus-title">Sobre Nosotros</h1>
        <div class="aboutus-grid">
            <div class="aboutus-text">
                <h2 class="aboutus-subtitle">Nuestra Historia</h2>
                <p class="aboutus-paragraph">
                Novaplay nació con la idea de transformar la manera en la que los gamers acceden a sus títulos favoritos. Comenzamos como un proyecto universitario y poco a poco fuimos construyendo una plataforma digital donde los jugadores pueden encontrar videojuegos, tarjetas de regalo y suscripciones de forma rápida, segura y confiable. Nuestro objetivo siempre ha sido conectar a la comunidad gamer con experiencias únicas a un solo clic.
                </p>
            </div>
            <div class="aboutus-img">
                <img src="./images/551-5512102_link-zelda-ocarina-of-time-png-download-zelda.png" alt="Imagen de historia" class="aboutus-photo">
            </div>
        </div>
        <div class="aboutus-grid">
            <div class="aboutus-img">
                <img src="./images/about2.jpg" alt="Imagen de equipo" class="aboutus-photo">
            </div>
            <div class="aboutus-text">
                <h2 class="aboutus-subtitle">Nuestro Equipo</h2>
                <p class="aboutus-paragraph">
                    Somos un grupo de apasionados por los videojuegos y la tecnología, con el compromiso de ofrecer un servicio cercano, innovador y transparente. Nuestro equipo combina la creatividad, la estrategia y el conocimiento técnico para hacer de Novaplay una plataforma pensada por gamers, para gamers.
                </p>
            </div>
        </div>
        <div class="aboutus-grid">
            <div class="aboutus-text">
                <h2 class="aboutus-subtitle">Nuestra Misión</h2>
                <p class="aboutus-paragraph">
                    En Novaplay trabajamos para hacer que cada jugador tenga acceso inmediato y seguro a los mejores videojuegos y servicios digitales del mercado. Nuestra misión es simplificar la compra, brindar precios competitivos y crear confianza en cada transacción, impulsando así la cultura gamer en México y más allá.
                </p>
            </div>
            <div class="aboutus-img">
                <img src="./images/pngimg.com - minecraft_PNG70.png" alt="Imagen de misión" class="aboutus-photo">
            </div>
        </div>
                <div class="aboutus-grid">
            <div class="aboutus-img">
                <img src="./images/nomans sky.png" alt="Imagen de equipo" class="aboutus-photo">
            </div>
            <div class="aboutus-text">
                <h2 class="aboutus-subtitle">Nuestra Visión</h2>
                <p class="aboutus-paragraph">
                    Nuestra visión es ser la plataforma líder en la distribución de videojuegos y servicios digitales en México y América Latina. Buscamos innovar constantemente y adaptarnos a las necesidades de nuestros usuarios, creando un ecosistema donde los gamers se sientan valorados y escuchados.
                </p>
            </div>
        </div>
        <div class="aboutus-grid">
            <div class="aboutus-text">
                <h2 class="aboutus-subtitle">Nuestros Valores</h2>
                <p class="aboutus-paragraph">
                    En Novaplay, nos guiamos por valores fundamentales que reflejan nuestra identidad y compromiso con la comunidad gamer. La pasión, la innovación y la transparencia son pilares en cada acción que emprendemos. Creemos en construir relaciones de confianza con nuestros usuarios, ofreciendo un servicio excepcional y productos de calidad.
                </p>
            </div>
            <div class="aboutus-img">
                <img src="./images/imagen_2025-09-24_215143423-fotor-bg-remover-20250924215152.png" alt="Imagen de misión" class="aboutus-photo">
            </div>
        </div>
    </section>
</main>

<footer>
    <p>&copy; <?php echo date("Y"); ?> Novaplay - E-commerce de Videojuegos</p>
</footer>

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
