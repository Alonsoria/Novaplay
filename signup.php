<?php
session_start();
require 'config.php';

$modalType = '';
$modalMessage = '';
$formData = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $formData = $_POST;

    $nombre = $_POST['nombre'] ?? '';
    $apellido = $_POST['apellido'] ?? '';
    $email = $_POST['email'] ?? '';
    $passwordRaw = $_POST['password'] ?? '';
    $telefono = $_POST['telefono'] ?? '';
    $direccion = $_POST['direccion'] ?? '';
    $fecha_registro = date('Y-m-d H:i:s');

    if ($passwordRaw) {
        $contraseña = password_hash($passwordRaw, PASSWORD_BCRYPT);
    }

    // 🔍 Verificar si el correo ya existe
    $checkStmt = $conn->prepare("SELECT id_usuario FROM usuarios WHERE email = ?");
    $checkStmt->bind_param("s", $email);
    $checkStmt->execute();
    $checkStmt->store_result();

    if ($checkStmt->num_rows > 0) {
        $modalType = 'error';
        $modalMessage = 'El correo ingresado ya se encuentra registrado.';
    } else {
        // Insertar nuevo usuario
        $stmt = $conn->prepare("INSERT INTO usuarios (nombre, apellido, email, contraseña, telefono, direccion, fecha_registro) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssss", $nombre, $apellido, $email, $contraseña, $telefono, $direccion, $fecha_registro);

        if ($stmt->execute()) {
            $modalType = 'success';
            $modalMessage = '¡Cuenta creada exitosamente!';
            $formData = []; // Limpiar formulario solo si fue exitoso
        } else {
            $modalType = 'error';
            $modalMessage = 'Ocurrió un error al registrar la cuenta.';
        }

        $stmt->close();
    }

    $checkStmt->close();
    $conn->close();
}
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
    <style>
        .row {
            display: flex;
            gap: 15px;
        }
        .row .field {
            flex: 1;
        }

        /* 🔥 MODAL */
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
        <h1>Regístrate en NovaPlay</h1>
        <p>Crea tu cuenta para comenzar tu aventura</p>

        <form id="signupForm" method="POST" action="">
            
            <!-- 🔥 Nombre + Apellido en la misma fila -->
            <div class="row">
                <div class="field">
                    <label>Nombre</label>
                    <div class="input-group">
                        <span>👤</span>
                        <input type="text" name="nombre" id="nombre" placeholder="Tu nombre" required value="<?= htmlspecialchars($formData['nombre'] ?? '') ?>">
                    </div>
                </div>

                <div class="field">
                    <label>Apellido</label>
                    <div class="input-group">
                        <span>👤</span>
                        <input type="text" name="apellido" id="apellido" placeholder="Tu apellido" required value="<?= htmlspecialchars($formData['apellido'] ?? '') ?>">
                    </div>
                </div>
            </div>

            <label>Correo Electrónico</label>
            <div class="input-group">
                <span>📧</span>
                <input type="email" name="email" id="email" placeholder="tu@email.com" required value="<?= htmlspecialchars($formData['email'] ?? '') ?>">
            </div>

            <label>Contraseña</label>
            <div class="input-group">
                <span>🔒</span>
                <input type="password" name="password" id="password" placeholder="••••••••" required minlength="6">
            </div>

            <label>Teléfono</label>
            <div class="input-group">
                <span>📞</span>
                <input type="tel" name="telefono" id="telefono" placeholder="Tu teléfono" required value="<?= htmlspecialchars($formData['telefono'] ?? '') ?>">
            </div>

            <label>Dirección</label>
            <div class="input-group">
                <span>🏠</span>
                <input type="text" name="direccion" id="direccion" placeholder="Tu dirección" required value="<?= htmlspecialchars($formData['direccion'] ?? '') ?>">
            </div>

            <button type="submit" class="btn-primary">→ Registrarse</button>
        </form>

        <p class="demo-note">Demo: Completa todos los campos para registrarte</p>
    </div>

    <a href="index.php" class="back-link">← Volver al inicio</a>
</div>

<!-- 🔥 MODAL -->
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

</body>
</html>
