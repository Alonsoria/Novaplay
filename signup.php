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
    <title>NovaPlay - Registro</title>
    <link rel="stylesheet" href="styles_login.css">
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
        <div class="logo">
            <svg xmlns="http://www.w3.org/2000/svg" width="70" height="70" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-gamepad2-icon lucide-gamepad-2"><line x1="6" x2="10" y1="11" y2="11"/><line x1="8" x2="8" y1="9" y2="13"/><line x1="15" x2="15.01" y1="12" y2="12"/><line x1="18" x2="18.01" y1="10" y2="10"/><path d="M17.32 5H6.68a4 4 0 0 0-3.978 3.59c-.006.052-.01.101-.017.152C2.604 9.416 2 14.456 2 16a3 3 0 0 0 3 3c1 0 1.5-.5 2-1l1.414-1.414A2 2 0 0 1 9.828 16h4.344a2 2 0 0 1 1.414.586L17 18c.5.5 1 1 2 1a3 3 0 0 0 3-3c0-1.545-.604-6.584-.685-7.258-.007-.05-.011-.1-.017-.151A4 4 0 0 0 17.32 5z"/></svg>
        </div>
        <h1>Regístrate en NovaPlay</h1>
        <p>Crea tu cuenta para comenzar tu aventura</p>

        <form id="signupForm" method="POST" action="">
            
            <!-- 🔥 Nombre + Apellido en la misma fila -->
            <div class="row">
                <div class="field">
                    <label>Nombre</label>
                    <div class="input-group">
                        <span>
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-user-round-icon lucide-user-round"><circle cx="12" cy="8" r="5"/><path d="M20 21a8 8 0 0 0-16 0"/></svg>
                        </span>
                        <input type="text" name="nombre" id="nombre" placeholder="Tu nombre" required value="<?= htmlspecialchars($formData['nombre'] ?? '') ?>">
                    </div>
                </div>

                <div class="field">
                    <label>Apellido</label>
                    <div class="input-group">
                        <span>
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-user-round-plus-icon lucide-user-round-plus"><path d="M2 21a8 8 0 0 1 13.292-6"/><circle cx="10" cy="8" r="5"/><path d="M19 16v6"/><path d="M22 19h-6"/></svg>
                        </span>
                        <input type="text" name="apellido" id="apellido" placeholder="Tu apellido" required value="<?= htmlspecialchars($formData['apellido'] ?? '') ?>">
                    </div>
                </div>
            </div>

            <label>Correo Electrónico</label>
            <div class="input-group">
                <span>
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-mail-icon lucide-mail"><path d="m22 7-8.991 5.727a2 2 0 0 1-2.009 0L2 7"/><rect x="2" y="4" width="20" height="16" rx="2"/></svg>
                </span>
                <input type="email" name="email" id="email" placeholder="tu@email.com" required value="<?= htmlspecialchars($formData['email'] ?? '') ?>">
            </div>

            <label>Contraseña</label>
            <div class="input-group">
                <span>
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-lock-icon lucide-lock"><rect width="18" height="11" x="3" y="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                </span>
                <input type="password" name="password" id="password" placeholder="••••••••" required minlength="6">
            </div>

            <label>Teléfono</label>
            <div class="input-group">
                <span>
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-smartphone-icon lucide-smartphone"><rect width="14" height="20" x="5" y="2" rx="2" ry="2"/><path d="M12 18h.01"/></svg>
                </span>
                <input type="tel" name="telefono" id="telefono" placeholder="Tu teléfono" required value="<?= htmlspecialchars($formData['telefono'] ?? '') ?>">
            </div>

            <label>Dirección</label>
            <div class="input-group">
                <span>
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-house-icon lucide-house"><path d="M15 21v-8a1 1 0 0 0-1-1h-4a1 1 0 0 0-1 1v8"/><path d="M3 10a2 2 0 0 1 .709-1.528l7-6a2 2 0 0 1 2.582 0l7 6A2 2 0 0 1 21 10v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/></svg>
                </span>
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
