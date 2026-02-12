<?php
session_start();

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
<title>Recompensa Diaria</title>
<link rel="stylesheet" href="RecompensaDiaria.css">

<style>
/* ===== RECOMPENSA ===== */
.recompensa-container {
    max-width: 900px;
    margin: 40px auto;
    text-align: center;
}

.calendario {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    gap: 14px;
    margin: 30px 0;
}

.dia {
    background: #1a1a2e;
    border: 2px solid #7c0ba1;
    border-radius: 14px;
    padding: 20px 10px;
    box-shadow: 0 0 12px rgba(124,11,161,.4);
}

.dia.activo {
    border-color: #ffcc00;
    box-shadow: 0 0 25px #ffcc00;
    transform: scale(1.05);
}

.dia.completado {
    opacity: .4;
}

.puntos {
    font-size: 26px;
    margin-top: 10px;
    color: #ffcc00;
}

/* ===== MODAL ===== */
.modal-overlay {
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,.7);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 10000;
}

.modal-content {
    --neon: <?= $colorModal ?>;

    position: relative;
    background: #1a1a2e;
    border-radius: 20px;
    padding: 40px;
    width: 420px;
    max-width: 90%;
    text-align: center;

    animation: modalIn .7s ease-out;

    box-shadow:
        0 0 25px var(--neon),
        inset 0 0 15px rgba(0,0,0,.6);
}

@keyframes neonRun {
    to { transform: rotate(360deg); }
}

@keyframes modalIn {
    from {
        transform: scale(.7) translateY(60px);
        opacity: 0;
    }
    to {
        transform: scale(1) translateY(0);
        opacity: 1;
    }
}

.modal-close {
    position: absolute;
    top: 12px;
    right: 14px;
    background: none;
    border: none;
    font-size: 22px;
    color: #fff;
    cursor: pointer;
}

.modal-images {
    position: relative;
    height: 220px;          /* un poco más de espacio */
    display: flex;
    justify-content: center;
    align-items: center;    /* 🔥 centra vertical y horizontal */
}

.img-dia {
    width: 140px;
    animation: float 3s ease-in-out infinite;
    filter: drop-shadow(0 0 25px var(--neon));
    position: relative;
    z-index: 2;
}

@keyframes float {
    50% { transform: translateY(-18px); }
}

.aura-bg {
    position: absolute;
    width: 220px;
    opacity: .6;
    animation: auraSpin 10s linear infinite;
    filter: drop-shadow(0 0 30px var(--neon));
    z-index: 1;
}

@keyframes auraSpin {
    from { transform: rotate(0deg); }
    to   { transform: rotate(360deg); }
}

.modal-images {
    position: relative;
    height: 260px;
    display: flex;
    justify-content: center;
    align-items: center;
    overflow: visible;
    margin: 25px;
}

/* ===== CAPA DE FONDO ===== */
.bg-layer {
    position: absolute;
    width: 500px;
    height: 500px;
    object-fit: contain;
    z-index: 1;
    opacity: 0.9;
    animation: bgPulse 4s ease-in-out infinite;
}

/* ===== CAPA ESMERALDA ===== */
.img-dia {
    position: relative;
    width: 140px;
    margin-top:25px;
    z-index: 2;
    animation: float 3s ease-in-out infinite;
    filter: drop-shadow(0 0 25px var(--neon));
}

/* ANIMACIONES */
@keyframes float {
    50% { transform: translateY(-14px); }
}

@keyframes bgPulse {
    50% {
        transform: scale(1.05);
        opacity: 1;
    }
}

/* ===== MODAL PASO 1 Y 2 ===== */
.modal-step { 
    display: none; 
animation: fadeIn .6s ease forwards; }
.modal-step-1 { 
    display: block; }
.final-animation img { 
    width: 260px; 
    max-width: 100%; 
    margin: 20px auto; 
    display: block; 
    animation: pulse 2.5s infinite; 
}
@keyframes fadeIn { from { opacity: 0; transform: scale(.9); } to { opacity: 1; transform: scale(1); } }
@keyframes pulse { 50% { transform: scale(1.08); } }

.modal-close { position: absolute; top: 12px; right: 14px; background: none; border: none; font-size: 22px; color: #fff; cursor: pointer; }

</style>
</head>
<body>

<header>
<div class="header-container">
    <nav class="navbar">
        <ul>
            <li><a href="productos.php">Productos</a></li>
            <li><a href="combos.php">Combos</a></li>
            <li><a href="about_us.php">Acerca de nosotros</a></li>
            <li class="logo-item"><a href="index.php"><img src="./images/novaplay logo 2.png" alt="Novaplay Logo" class="logo"></a></li>
            <li class="platforms-wrapper">
                <button id="platformToggle" class="platform-toggle" aria-expanded="false">Plataformas ▾</button>
                <div id="platformMenu" class="submenu" aria-hidden="true" role="menu">
                    <button id="platformClose" class="submenu-close" aria-label="Cerrar menú">✕</button>
                    <ul>
                        <?php foreach($platformsArr as $plat): ?>
                        <li><a href="index.php?plataforma=<?php echo (int)$plat['id_plataforma']; ?>"><img src="<?php echo htmlspecialchars($plat['icono']); ?>" alt="<?php echo htmlspecialchars($plat['nombre']); ?>" class="plat-icon"><?php echo htmlspecialchars($plat['nombre']); ?></a></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </li>
            <li><a href="carrito.php">Carrito <span class="cart-badge"><?php echo $cartCount; ?></span></a></li>
            <li class="login-item"><a href="login.php" class="login-btn">Login</a></li>
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