<?php include("config.php"); ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Iniciar Sesión - Novaplay</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="login-container">
        <h2>👤 Iniciar Sesión</h2>
        <form action="login_process.php" method="POST">
            <input type="text" name="usuario" placeholder="Usuario o Email" required>
            <input type="password" name="password" placeholder="Contraseña" required>
            <button type="submit" class="btn-login">Entrar</button>
        </form>
        <p>No tienes cuenta <a href="registro.php">Regístrate aquí</a></p>
    </div>
</body>
</html>
