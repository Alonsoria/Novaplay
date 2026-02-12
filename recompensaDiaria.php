<?php
session_start();
require_once 'config.php'; // conexión a la BD
$usuarioId = $_SESSION['id_usuario'] ?? null;

/* =======================
   INICIALIZAR SESIONES
======================= */
if (!isset($_SESSION['carrito']) || !is_array($_SESSION['carrito'])) {
    $_SESSION['carrito'] = [];
}

if (!isset($_SESSION['recompensa'])) {
    $_SESSION['recompensa'] = [
        'ultimo_dia' => null,
        'racha' => 1,
        'puntos' => 0
    ];
}

$recompensa = &$_SESSION['recompensa'];
$hoy = date('Y-m-d');
$mensaje = '';
$animar = false;

/* =======================
   CONTADOR CARRITO
======================= */
$cartCount = array_sum($_SESSION['carrito']);

/* =======================
   BOTÓN PASAR DÍA (TEST)
======================= */
if (isset($_POST['pasar_dia'])) {

    // Si nunca ha reclamado, ponemos ayer
    if (!$recompensa['ultimo_dia']) {
        $recompensa['ultimo_dia'] = date('Y-m-d', strtotime('-1 day'));
    } else {
        // Retrocedemos un día más
        $recompensa['ultimo_dia'] = date(
            'Y-m-d',
            strtotime($recompensa['ultimo_dia'] . ' -1 day')
        );
    }

    $mensaje = "Día avanzado manualmente (modo prueba)";
}

/* =======================
   LÓGICA RECOMPENSA
======================= */
if (isset($_POST['reclamar'])) {

    if ($recompensa['ultimo_dia']) {
        $diff = (strtotime($hoy) - strtotime($recompensa['ultimo_dia'])) / 86400;

        if ($diff > 1) {
            $recompensa['racha'] = 1;
        } elseif ($diff == 0) {
            $mensaje = "Ya reclamaste hoy, espera al dia siguiente para reclamar más recompensas!";
        }
    }

if ($mensaje === '') {
    $recompensa['puntos'] += 3;
    $recompensa['ultimo_dia'] = $hoy;
    $animar = true;

    if ($recompensa['racha'] < 7) {
        $recompensa['racha']++;
    } else {
        $recompensa['racha'] = 1;
    }

    if ($usuarioId) {
        $stmt = $conn->prepare("UPDATE usuarios SET puntos = puntos + 3 WHERE id_usuario = ?");
        $stmt->bind_param("i", $usuarioId);
        $stmt->execute();
        $stmt->close();
    }

    $mensaje = "+3 puntos obtenidos!";
}

}

/* =======================
   IMÁGENES POR DÍA
======================= */
$imagenesRecompensa = [
    1 => 'images/RecompensaDiaria/EsmeraldaVerde.png',
    2 => 'images/RecompensaDiaria/EsmeraldaAmarilla.png',
    3 => 'images/RecompensaDiaria/EsmeraldadBlanca.png',
    4 => 'images/RecompensaDiaria/EsmeraldaRoja.png',
    5 => 'images/RecompensaDiaria/EsmeraldaCyan.png',
    6 => 'images/RecompensaDiaria/EsmeraldaRosa.png',
    7 => 'images/RecompensaDiaria/esmeraldaAzul.png',
];

$diaMostrado = $recompensa['racha'] - 1;
if ($diaMostrado <= 0) $diaMostrado = 7;

$imagenDia = $imagenesRecompensa[$diaMostrado];

/* =======================
   COLOR MODAL POR DÍA
======================= */
$coloresDia = [
    1 => '#00cf30',
    2 => '#ffe100',
    3 => '#f3f3f3',
    4 => '#d50000',
    5 => '#00d5ec',
    6 => '#bf00d0',
    7 => '#0d00ff',
];

$colorModal = $coloresDia[$diaMostrado];
$esDiaFinal = ($diaMostrado == 7);
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

<main class="recompensa-container">

<h2>Recompensa Diaria</h2>

<div class="puntos">Puntos: <?= $recompensa['puntos'] ?></div>

<div class="calendario">
    <?php for ($i=1;$i<=7;$i++): ?>
    <div class="dia <?= $i < $recompensa['racha'] ? 'completado':'' ?> <?= $i==$recompensa['racha']?'activo':'' ?>">Día <?= $i ?><br>+3 pts</div>
    <?php endfor; ?>
</div>

<form method="post">
    <button class="btn2" name="reclamar">Reclamar recompensa</button>
</form>

<form method="post" style="margin-top:10px;">
    <button class="btn2" name="pasar_dia" style="opacity:.6;font-size:14px;">⏩ Pasar día (TEST)</button>
</form>

<p><?= $mensaje ?></p>

<?php

// Aqui va el personaje

$personajeImg = './images/RecompensaDiaria/Main_sonic.png';
$personajeClass = 'personaje-img';

if ($diaMostrado == 7 && $recompensa['ultimo_dia'] == $hoy) {
    $personajeImg = './images/RecompensaDiaria/ClassicSuperSonic.png';
    $personajeClass .= ' personaje-especial'; // 🔥 agregamos clase extra
}

?>
<div class="personaje-fondo">
    <img src="<?= $personajeImg ?>" alt="Personaje" class="<?= $personajeClass ?>">
</div>



</main>

<?php if ($animar): ?>
<div class="modal-overlay" id="rewardModal">
    <div class="modal-content">

        <!-- PASO 1 -->
        <div class="modal-step modal-step-1">
            <h2 id="typeText"></h2>
            <p>Día <?= $diaMostrado ?> completado</p>
            <div class="modal-images">
                <img src="images/RecompensaDiaria/SonicAura.png" class="bg-layer">
                <img src="<?= $imagenDia ?>" class="img-dia">
            </div>
            <button class="btn2" id="acceptReward">Continuar</button>
        </div>

        <!-- PASO 2 DÍA 7 -->
        <?php if ($esDiaFinal): ?>
        <div class="modal-step modal-step-2">
            <h2> FELICIDADES!</h2>
            <div class="final-animation">
                <img src="images/RecompensaDiaria/supersonic.gif" alt="Esmeralda Suprema">
            </div>
            <p>Has completado los 7 días de racha y recolectado todas las Esmeraldas del Caos!</p>
            <button class="btn2" id="closeFinal">Cerrar</button>
        </div>
        <?php endif; ?>

    </div>
</div>

<script>
const modal = document.getElementById("rewardModal");
const step1 = modal.querySelector(".modal-step-1");
const step2 = modal.querySelector(".modal-step-2");

document.getElementById("acceptReward").addEventListener("click", () => {
    <?php if ($esDiaFinal): ?>
    step1.style.display = "none";
    step2.style.display = "block";
    <?php else: ?>
    closeModal();
    <?php endif; ?>
});

<?php if ($esDiaFinal): ?>
document.getElementById("closeFinal").addEventListener("click", closeModal);
<?php endif; ?>

const text = <?= json_encode(
    $esDiaFinal ? 'Haz obtenido una Esmeralda del Caos!' : 'Haz obtenido una Esmeralda del Caos!'
) ?>;

let i=0;
const el=document.getElementById("typeText");
(function type() { if(i<text.length){el.innerHTML+=text.charAt(i++); setTimeout(type,40);} })();

function closeModal(){ modal.classList.add("modal-out"); setTimeout(()=>modal.remove(),400); }
</script>
<?php endif; ?>

</footer class="footer">
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