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
      <div class="logo">
        <svg xmlns="http://www.w3.org/2000/svg" width="60" height="60" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-gamepad2-icon lucide-gamepad-2"><line x1="6" x2="10" y1="11" y2="11"/><line x1="8" x2="8" y1="9" y2="13"/><line x1="15" x2="15.01" y1="12" y2="12"/><line x1="18" x2="18.01" y1="10" y2="10"/><path d="M17.32 5H6.68a4 4 0 0 0-3.978 3.59c-.006.052-.01.101-.017.152C2.604 9.416 2 14.456 2 16a3 3 0 0 0 3 3c1 0 1.5-.5 2-1l1.414-1.414A2 2 0 0 1 9.828 16h4.344a2 2 0 0 1 1.414.586L17 18c.5.5 1 1 2 1a3 3 0 0 0 3-3c0-1.545-.604-6.584-.685-7.258-.007-.05-.011-.1-.017-.151A4 4 0 0 0 17.32 5z"/></svg>
      </div>
      <h1>Bienvenido a NovaPlay</h1>
      <p>Inicia sesión para continuar tu aventura</p>

      <form id="loginForm" method="POST" action="">
        <label>Correo Electrónico</label>
        <div class="input-group">
          <span>
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-mail-icon lucide-mail"><path d="m22 7-8.991 5.727a2 2 0 0 1-2.009 0L2 7"/><rect x="2" y="4" width="20" height="16" rx="2"/></svg>
          </span>
          <input type="email" name="email" id="email" placeholder="tu@email.com" required value="<?= htmlspecialchars($formData['email'] ?? '') ?>" />
        </div>

        <label>Contraseña</label>
        <div class="input-group">
          <span>
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-lock-icon lucide-lock"><rect width="18" height="11" x="3" y="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
          </span>
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
