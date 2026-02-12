<?php
session_start();
include("config.php");

// 🔥 Solo redirige si REALMENTE hay sesión válida
if (isset($_SESSION['user_id']) && !empty($_SESSION['user_id'])) {
  header("Location: index.php");
  exit;
}

include("config.php");

// 🔥 Solo redirige si REALMENTE hay sesión válida
if (isset($_SESSION['user_id']) && !empty($_SESSION['user_id'])) {
  header("Location: index.php");
  exit;
}

$modalType = '';
$modalMessage = '';
$formData = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $formData = $_POST;

    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    $stmt = $conn->prepare("SELECT id_usuario, nombre, contraseña FROM usuarios WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['contraseña'])) {
            $_SESSION['user_id'] = $user['id_usuario'];
            $_SESSION['user_name'] = $user['nombre'];

            $modalType = 'success';
            $modalMessage = 'Inicio de sesión exitoso. ¡A jugar!';
            $formData = [];
        } else {
            $modalType = 'error';
            $modalMessage = 'Contraseña o correo incorrecto';
        }
    } else {
        $modalType = 'error';
        $modalMessage = 'Contraseña o correo incorrecto';
    }

    $stmt->close();
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <title>NovaPlay - Login</title>
  <link rel="stylesheet" href="styles_login.css" />
  <script src="https://accounts.google.com/gsi/client" async defer></script>
  <style>
    .modal-overlay {
        position: fixed;
        inset: 0;
        background: rgba(0,0,0,0.55);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 999;
        opacity: 0;
        pointer-events: none;
        transition: opacity 0.4s ease;
    }
    .modal-overlay.show {
        opacity: 1;
        pointer-events: all;
    }
    .modal-box {
        background: linear-gradient(135deg, #1e1e2f, #2b0a3d);
        border-radius: 20px;
        padding: 40px 30px;
        width: 90%;
        max-width: 380px;
        text-align: center;
        color: white;
        box-shadow: 0 25px 60px rgba(0,0,0,0.4);
        transform: scale(0.7);
        animation: popIn 0.5s forwards;
    }
    .modal-box.error {
        background: linear-gradient(135deg, #7a0000, #c00000);
    }
    .modal-icon {
        width: 80px;
        height: 80px;
        margin: 0 auto 20px;
        border-radius: 50%;
        display: flex;
        justify-content: center;
        align-items: center;
        font-size: 42px;
        animation: bounce 1s infinite;
    }
    .modal-box.success .modal-icon {
        background: linear-gradient(135deg, #ff3cac, #784ba0, #2b86c5);
    }
    .modal-box.error .modal-icon {
        background: linear-gradient(135deg, #ff0000, #ff4d4d);
    }
    .modal-message {
        font-size: 1.1rem;
        margin-top: 10px;
    }
    @keyframes popIn {
        to { transform: scale(1); }
    }
    @keyframes bounce {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-6px); }
    }
  </style>
</head>
<body>
  <div class="background">
    <div class="blob pink"></div>
    <div class="blob purple"></div>
  </div>

  <div class="login-container">
    <div class="card">
      <div class="logo">🎮</div>
      <h1>Bienvenido a NovaPlay</h1>
      <p>Inicia sesión para continuar tu aventura</p>

      <form id="loginForm" method="POST" action="">
        <label>Correo Electrónico</label>
        <div class="input-group">
          <span>📧</span>
          <input type="email" name="email" id="email" placeholder="tu@email.com" required value="<?= htmlspecialchars($formData['email'] ?? '') ?>" />
        </div>

        <label>Contraseña</label>
        <div class="input-group">
          <span>🔒</span>
          <input type="password" name="password" id="password" placeholder="••••••••" required minlength="6" />
        </div>

        <button type="submit" class="btn-primary">→ Iniciar Sesión</button>
      </form>

      <div class="divider">o</div>

      <div id="g_id_onload"
           data-client_id="TU_CLIENT_ID_DE_GOOGLE"
           data-context="signin"
           data-ux_mode="popup"
           data-callback="handleGoogleLogin"
           data-auto_prompt="false">
      </div>

      <div class="g_id_signin"
           data-type="standard"
           data-shape="pill"
           data-theme="filled_black"
           data-text="signin_with"
           data-size="large"
           data-logo_alignment="left">
      </div>
    </div>

    <a href="index.php" class="back-link">← Volver al inicio</a>
  </div>

  <?php if (!empty($modalType)): ?>
  <div class="modal-overlay show" id="modalOverlay">
      <div class="modal-box <?= $modalType ?>">
          <div class="modal-icon">
              <?= $modalType === 'success' ? '✔️' : '❌' ?>
          </div>
          <div class="modal-message"><?= $modalMessage ?></div>
      </div>
  </div>
  <?php endif; ?>

  <script>
    const modalOverlay = document.getElementById('modalOverlay');

    if (modalOverlay) {
        modalOverlay.addEventListener('click', closeModal);
        setTimeout(closeModal, 3000);

        function closeModal() {
            modalOverlay.classList.remove('show');

            <?php if ($modalType === 'success'): ?>
                setTimeout(() => {
                    window.location.href = "index.php";
                }, 400);
            <?php endif; ?>
        }
    }
  </script>

  <script src="js/app.js"></script>
</body>
</html>
