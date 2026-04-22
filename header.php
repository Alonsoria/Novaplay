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
$cartCount = 0;
foreach ($_SESSION['carrito'] as $q) $cartCount += $q;

?>
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

                <!-- 👤 USUARIO / LOGIN -->
                <?php if (isset($_SESSION['user_id'])): 
                    $stmt = $conn->prepare("SELECT nombre, email, puntos FROM usuarios WHERE id_usuario = ?");
                    $stmt->bind_param("i", $_SESSION['user_id']);
                    $stmt->execute();
                    $user = $stmt->get_result()->fetch_assoc();
                    $stmt->close();
                ?>
                <li class="user-menu">
                    <span class="user-name">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-user-round-icon lucide-user-round"><circle cx="12" cy="8" r="5"/><path d="M20 21a8 8 0 0 0-16 0"/></svg>    
                    <?php echo htmlspecialchars($user['nombre']); ?> ▾</span>
                    <div class="user-dropdown">
                        <p><strong>Correo:</strong><br><?php echo htmlspecialchars($user['email']); ?></p>
                        <p><strong>Puntos:</strong> 
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-star-icon lucide-star"><path d="M11.525 2.295a.53.53 0 0 1 .95 0l2.31 4.679a2.123 2.123 0 0 0 1.595 1.16l5.166.756a.53.53 0 0 1 .294.904l-3.736 3.638a2.123 2.123 0 0 0-.611 1.878l.882 5.14a.53.53 0 0 1-.771.56l-4.618-2.428a2.122 2.122 0 0 0-1.973 0L6.396 21.01a.53.53 0 0 1-.77-.56l.881-5.139a2.122 2.122 0 0 0-.611-1.879L2.16 9.795a.53.53 0 0 1 .294-.906l5.165-.755a2.122 2.122 0 0 0 1.597-1.16z"/></svg>
                        <?php echo (int)$user['puntos']; ?></p>
                        <button class="logout-btn" id="logoutBtn">Cerrar sesión</button>
                    </div>
                </li>
                <?php else: ?>
                <li class="login-item">
                    <a href="login.php" class="login-btn">Login</a>
                </li>
                <li class="login-item">
                    <a href="signup.php" class="login-btn">Registrarse</a>
                </li>
                <?php endif; ?>
            </ul>
        </nav>
    </div>
</header>