<?php
session_start();
if (isset($_SESSION['user'])) {
  header("Location: dashboard.php");
  exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <title>NovaPlay - Login</title>
  <link rel="stylesheet" href="styles_login.css" />
  <script src="https://accounts.google.com/gsi/client" async defer></script>
</head>
<body>
  <div class="background">
    <div class="blob pink"></div>
    <div class="blob purple"></div>
  </div>

  <div class="login-container">
    <div class="card">
      <div class="logo">
        🎮
      </div>
      <h1>Bienvenido a NovaPlay</h1>
      <p>Inicia sesión para continuar tu aventura</p>

      <form id="loginForm" method="POST" action="">
        <label>Correo Electrónico</label>
        <div class="input-group">
          <span>📧</span>
          <input type="email" name="email" id="email" placeholder="tu@email.com" required />
        </div>

        <label>Contraseña</label>
        <div class="input-group">
          <span>🔒</span>
          <input type="password" name="password" id="password" placeholder="••••••••" required minlength="6" />
        </div>

        <button type="button" class="btn-primary" onclick="window.location.href='index.php'">
          → Iniciar Sesión
        </button>
      </form>

      <div class="divider">o</div>

      <!-- Google Sign In -->
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

      <p class="demo-note">Demo: Usa cualquier email y contraseña</p>
    </div>

    <a href="index.php" class="back-link">← Volver al inicio</a>
  </div>

  <script src="js/app.js"></script>
</body>
</html>
