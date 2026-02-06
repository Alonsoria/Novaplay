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
<body>
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


<section class="hero">

  <!-- LOGO DECORATIVO (DETRÁS DE SPLASHES) -->
  <img src="./images/TOTKlogo.png" class="hero-bg-image" alt="">

  <!-- SPLASHES -->
  <img src="./images/ManchaNeon2k.png" class="splash splash-1" alt="">
  <img src="./images/ManchaNeon2k.png" class="splash splash-2" alt="">

  <!-- TEXTO -->
  <div class="contenidoJuego">
    <h1 class="TituloHero">THE LEGEND OF ZELDA:TEARS OF THE KINGDOM</h1>
    <div class="rating-container">
        <span class="rating-text">Rating: </span>

        <div class="stars">
            <i class="fa-solid fa-star"></i>
            <i class="fa-solid fa-star"></i>
            <i class="fa-solid fa-star"></i>
            <i class="fa-solid fa-star"></i>

            <!-- estrella a la mitad -->
            <i class="fa-solid fa-star-half-stroke"></i>

        </div>

    </div>

    <span class="rating-text">Mayo 2023 </span>

    <p class="parrafoHero">
      Conviértete en el héroe que Hyrule necesita
      y explora un mundo lleno de misterio.
    </p>  
    <button class="btn2">VER JUEGO</button>


  </div>

  <!-- PERSONAJE -->
  <img src="./images/linkTOTK.png" class="hero-character" alt="Link">

</section>

<main>
    <section class="carrusel-destacados">
        <h2>Juegos Destacados</h2>
        <div class="swiper mySwiper">
            <div class="swiper-wrapper">
                <div class="swiper-slide"><img src="./images/gta.jpg" alt="GTA V"></div>
                <div class="swiper-slide"><img src="./images/tlou.jpg" alt="The Last of Us"></div>
                <div class="swiper-slide"><img src="./images/fifa24.jpg" alt="FIFA 24"></div>
                <div class="swiper-slide"><img src="./images/minecraft.jpg" alt="Minecraft"></div>
                <div class="swiper-slide"><img src="./images/Halo_infinite.png" alt="Halo Infinite"></div>
            </div>

            <div class="swiper-button-prev"></div>
            <div class="swiper-button-next"></div>
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

    

<footer class="footer">
    <div class="footer-container">
        <p>&copy; <?php echo date("Y"); ?> Novaplay - E-commerce de Videojuegos</p>
        <div class="footer-links">
            <a href="aviso_privacidad.php">Aviso de Privacidad</a>
            <span>|</span>
            <a href="terminos_condiciones.php">Términos y Condiciones</a>
            <span>|</span>
            <a href="politica_cookies.php">Política de Cookies</a>
        </div>
        <p class="footer-note">
            La información proporcionada será tratada conforme a nuestro Aviso de Privacidad.
        </p>
    </div>
</footer>

<!-- Modal: login required -->
<div id="loginRequiredModal" class="modal" aria-hidden="true" style="display:none;">
    <div class="modal-content">
        <button class="modal-close" aria-label="Cerrar">✕</button>
        <h3>Necesitas iniciar sesión</h3>
        <p>Debes iniciar sesión para agregar productos al carrito.</p>
        <div class="modal-actions">
            <button id="modalLoginBtn" class="btn-login">Iniciar sesión</button>
            <button id="modalContinueBtn" class="btn">Seguir viendo</button>
        </div>
    </div>
</div>

<script>
(function(){
    function isLogged() { return !!localStorage.getItem('novaplay_user'); }
    const headerBtn = document.getElementById('headerLoginBtn');
    function updateHeader() {
        if (!headerBtn) return;
        if (isLogged()) {
            headerBtn.textContent = 'Cerrar sesión';
            headerBtn.href = '#';
            headerBtn.addEventListener('click', function(e){ e.preventDefault(); localStorage.removeItem('novaplay_user'); location.reload(); });
        } else {
            headerBtn.textContent = 'Iniciar sesión';
            headerBtn.href = 'login.php';
        }
    }
})
</script>

<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
<script>
var swiper = new Swiper(".mySwiper", {
    effect: "coverflow",
    grabCursor: true,
    centeredSlides: true,
    slidesPerView: 2, 
    loop: true,
    coverflowEffect: {
        rotate: 0,      
        stretch: -30,   
        depth: 250,    
        modifier: 2,    
        slideShadows: false,
    },
    autoplay: {
        delay: 2500,
        disableOnInteraction: false,
    },
    navigation: {
        nextEl: ".swiper-button-next",
        prevEl: ".swiper-button-prev",
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

<aside class="sidebar">
  <a href="#" class="sidebar-icon">
    <i class="fa-solid fa-house"></i>
  </a>
  <a href="#" class="sidebar-icon">
    <i class="fa-solid fa-gamepad"></i>
  </a>
  <a href="#" class="sidebar-icon">
    <i class="fa-solid fa-heart"></i>
  </a>
  <a href="#" class="sidebar-icon">
    <i class="fa-solid fa-user"></i>
  </a>
</aside>


</html>
