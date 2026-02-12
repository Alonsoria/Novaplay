<?php
session_start();
include 'config.php'; 

if (!isset($_SESSION['carrito'])) {
    $_SESSION['carrito'] = [];
}

// Agregar al carrito
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

// Obtener plataformas
$platformsArr = [];
$platRes = $conn->query("SELECT * FROM plataformas ORDER BY nombre");
if ($platRes) {
    while ($r = $platRes->fetch_assoc()) $platformsArr[] = $r;
}

// Categorías
$categorias = [
    "suscripcion" => "Suscripciones disponibles",
    "accesorio"   => "Accesorios disponibles",
    "videojuego"  => "Videojuegos disponibles",
    "tarjeta"     => "Tarjetas prepago",
    "DLC"         => "Contenido adicional (DLC)",
    "moneda_virtual" => "Monedas virtuales",
    "paquete"     => "Paquetes especiales"
];

// Contar carrito
$cartCount = 0;
foreach ($_SESSION['carrito'] as $q) $cartCount += $q;

// Filtro de plataforma
$filtroPlataforma = isset($_GET['plataforma']) ? (int)$_GET['plataforma'] : 0;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Novaplay - Productos</title>
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
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


<main>
    <?php
    foreach ($categorias as $clave => $titulo):
        if ($filtroPlataforma > 0) {
            $sql = "SELECT DISTINCT p.* 
                    FROM productos p
                    INNER JOIN producto_plataforma pp ON p.id_producto = pp.id_producto
                    WHERE p.categoria='$clave' AND pp.id_plataforma=$filtroPlataforma
                    ORDER BY p.nombre";
        } else {
            $sql = "SELECT * FROM productos WHERE categoria='$clave' ORDER BY nombre";
        }

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

document.addEventListener('DOMContentLoaded', function(){
    document.querySelectorAll('.sidebar a, .sidebar .icon').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            // Opcional: Si quieres que el splash salga exacto de donde diste clic:
            // const rect = this.getBoundingClientRect();
            
            this.classList.add('splash-effect');

            for (let i = 0; i < 8; i++) { // Aumenté a 8 gotas para que se vea más "pro"
                const drop = document.createElement('span');
                drop.classList.add('splash-drop');

                // Direcciones aleatorias más amplias (hasta 150px)
                const x = (Math.random() - 0.5) * 200 + 'px';
                const y = (Math.random() - 0.5) * 200 + 'px';

                drop.style.setProperty('--x', x);
                drop.style.setProperty('--y', y);

                this.appendChild(drop);

                setTimeout(() => {
                    drop.remove();
                }, 600);
            }
        });
    });
});
</script>

<aside class="sidebar">
  <a href="index.php" class="sidebar-icon">
    <i class="fa-solid fa-house"></i>
  </a>
  <a href="#" class="sidebar-icon">
    <i class="fa-solid fa-gamepad"></i>
  </a>
  <!-- Icono de Mensajes -->
  <a href="#" class="icon">
    <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path d="M1.60175 4.20114C2.14997 3.47258 3.02158 3 4 3H20C20.9784 3 21.85 3.47258 22.3982 4.20113L12 11.7635L1.60175 4.20114Z" fill="#ffffff"></path> <path d="M1 6.2365V18C1 19.6523 2.34772 21 4 21H20C21.6523 21 23 19.6523 23 18V6.23649L13.1763 13.381C12.475 13.891 11.525 13.891 10.8237 13.381L1 6.2365Z" fill="#ffffff"></path> </g></svg>
  </a>
  <a href="#" class="sidebar-icon">
    <i class="fa-solid fa-heart"></i>
  </a>
  <!-- Icono de Ayuda -->
  <a href="about_us.php" class="icon">
    <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path fill-rule="evenodd" clip-rule="evenodd" d="M22 12C22 17.5228 17.5228 22 12 22C6.47715 22 2 17.5228 2 12C2 6.47715 6.47715 2 12 2C17.5228 2 22 6.47715 22 12ZM12 7.75C11.3787 7.75 10.875 8.25368 10.875 8.875C10.875 9.28921 10.5392 9.625 10.125 9.625C9.71079 9.625 9.375 9.28921 9.375 8.875C9.375 7.42525 10.5503 6.25 12 6.25C13.4497 6.25 14.625 7.42525 14.625 8.875C14.625 9.58584 14.3415 10.232 13.883 10.704C13.7907 10.7989 13.7027 10.8869 13.6187 10.9708C13.4029 11.1864 13.2138 11.3753 13.0479 11.5885C12.8289 11.8699 12.75 12.0768 12.75 12.25V13C12.75 13.4142 12.4142 13.75 12 13.75C11.5858 13.75 11.25 13.4142 11.25 13V12.25C11.25 11.5948 11.555 11.0644 11.8642 10.6672C12.0929 10.3733 12.3804 10.0863 12.6138 9.85346C12.6842 9.78321 12.7496 9.71789 12.807 9.65877C13.0046 9.45543 13.125 9.18004 13.125 8.875C13.125 8.25368 12.6213 7.75 12 7.75ZM12 17C12.5523 17 13 16.5523 13 16C13 15.4477 12.5523 15 12 15C11.4477 15 11 15.4477 11 16C11 16.5523 11.4477 17 12 17Z" fill="#ffffff"></path> </g></svg>  </a>

  <!-- EL CARRITO (Corregido) -->
  <a href="carrito.php" class="icon cart-link">
    <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" stroke="#ffffff">
      <g id="SVGRepo_iconCarrier"> 
        <path d="M2.08416 2.7512C2.22155 2.36044 2.6497 2.15503 3.04047 2.29242L3.34187 2.39838C3.95839 2.61511 4.48203 2.79919 4.89411 3.00139C5.33474 3.21759 5.71259 3.48393 5.99677 3.89979C6.27875 4.31243 6.39517 4.76515 6.4489 5.26153C6.47295 5.48373 6.48564 5.72967 6.49233 6H17.1305C18.8155 6 20.3323 6 20.7762 6.57708C21.2202 7.15417 21.0466 8.02369 20.6995 9.76275L20.1997 12.1875C19.8846 13.7164 19.727 14.4808 19.1753 14.9304C18.6236 15.38 17.8431 15.38 16.2821 15.38H10.9792C8.19028 15.38 6.79583 15.38 5.92943 14.4662C5.06302 13.5523 4.99979 12.5816 4.99979 9.64L4.99979 7.03832C4.99979 6.29837 4.99877 5.80316 4.95761 5.42295C4.91828 5.0596 4.84858 4.87818 4.75832 4.74609C4.67026 4.61723 4.53659 4.4968 4.23336 4.34802C3.91052 4.18961 3.47177 4.03406 2.80416 3.79934L2.54295 3.7075C2.15218 3.57012 1.94678 3.14197 2.08416 2.7512Z" fill="#ffffff"></path> 
        <path d="M7.5 18C8.32843 18 9 18.6716 9 19.5C9 20.3284 8.32843 21 7.5 21C6.67157 21 6 20.3284 6 19.5C6 18.6716 6.67157 18 7.5 18Z" fill="#ffffff"></path> 
        <path d="M16.5 18.0001C17.3284 18.0001 18 18.6716 18 19.5001C18 20.3285 17.3284 21.0001 16.5 21.0001C15.6716 21.0001 15 20.3285 15 19.5001C15 18.6716 15.6716 18.0001 16.5 18.0001Z" fill="#ffffff"></path> 
      </g>
    </svg>
    <!-- El contador dinámico -->
    <?php if ($cartCount > 0): ?>
       <span class="cart-count-badge"><?php echo $cartCount; ?></span>
    <?php endif; ?>
  </a>
    <a href="recompensaDiaria.php" class="icon">
    <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path d="M9.5 3C11.9853 3 14 7.02944 14 12M9.5 3C7.01472 3 5 7.02944 5 12C5 16.9706 7.01472 21 9.5 21M9.5 3H15C17.2091 3 19 7.02944 19 12M14 12C14 16.9706 11.9853 21 9.5 21M14 12H19M9.5 21H15C17.2091 21 19 16.9706 19 12M18.3264 17H13.2422M18.3264 7H13.2422M9.5 8C10.3284 8 11 9.79086 11 12C11 14.2091 10.3284 16 9.5 16C8.67157 16 8 14.2091 8 12C8 9.79086 8.67157 8 9.5 8Z" stroke="#ffffff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path> </g></svg>    </a>
    </a>
</aside>

</body>
</html>
