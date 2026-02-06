<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Pago - UI Neon</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="pago_tarjeta.css">
    <link rel="stylesheet" href="style.css">
</head>
<header>
    <div class="header-container">
        <img src="./images/novaplay logo 2.png" alt="Novaplay Logo" class="logo">
        <nav class="navbar">
            <ul>
                <li><a href="index.php">Inicio</a></li>
                <li><a href="productos.php">Productos</a></li>
                <li><a href="combos.php">Combos</a></li>
                <li><a href="about_us.php">Acerca de nosotros</a></li>

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

                <li><a href="carrito.php"> Carrito <span class="cart-badge"><?php echo $cartCount; ?></span></a></li>
            </ul>
        </nav>
        <div class="user-login">
            <a href="login.php" id="headerLoginBtn" class="btn-login">Iniciar sesión</a>
        </div>
    </div>
</header>
<body>
  <div class="payment-wrapper">
    <div class="payment-card">
      <div class="icon-circle">
        <!-- Dollar icon -->
        <svg viewBox="0 0 24 24" aria-hidden="true">
          <path d="M12 1a1 1 0 011 1v1.07c2.28.27 4 1.58 4 3.38a1 1 0 01-2 0c0-.9-1.34-1.5-3-1.5s-3 .6-3 1.5c0 .87.88 1.22 3.32 1.86 2.82.74 4.68 1.63 4.68 4.14 0 2.05-1.72 3.36-4 3.65V22a1 1 0 01-2 0v-1.07c-2.28-.27-4-1.58-4-3.38a1 1 0 012 0c0 .9 1.34 1.5 3 1.5s3-.6 3-1.5c0-.93-.9-1.3-3.5-1.98C8.8 13.85 7 12.94 7 10.43c0-2.05 1.72-3.36 4-3.65V2a1 1 0 011-1z" />
        </svg>
      </div>

      <h1>¿Estás listo para pagar?</h1>
      <p class="subtitle">Agrega tus datos de tarjeta.</p>

      <form id="paymentForm" novalidate>
        <!-- Card Number -->
        <div class="input-group">
          <label for="cardNumber">Número de la tarjeta</label>
          <div class="input-wrapper">
            <svg viewBox="0 0 24 24" aria-hidden="true">
              <path d="M3 4h18a2 2 0 012 2v12a2 2 0 01-2 2H3a2 2 0 01-2-2V6a2 2 0 012-2zm0 4v10h18V8H3zm2 6h4v2H5v-2z" />
            </svg>
            <input
              type="text"
              id="cardNumber"
              name="cardNumber"
              placeholder="1111-1111-1111-1111"
              inputmode="numeric"
              maxlength="19"
              required
            />
          </div>
        </div>

        <!-- Card Holder -->
        <div class="input-group">
          <label for="cardHolder">Titular de la tarjeta</label>
          <div class="input-wrapper">
            <svg viewBox="0 0 24 24" aria-hidden="true">
              <path d="M12 12a5 5 0 100-10 5 5 0 000 10zm0 2c-4.42 0-8 2.24-8 5v1a1 1 0 001 1h14a1 1 0 001-1v-1c0-2.76-3.58-5-8-5z" />
            </svg>
            <input
              type="text"
              id="cardHolder"
              name="cardHolder"
              placeholder="Jon Doe"
              required
            />
          </div>
        </div>

        <div class="row">
          <!-- Expiry -->
          <div class="input-group">
            <label for="expiry">Fecha de vencimiento</label>
            <div class="input-wrapper">
              <svg viewBox="0 0 24 24" aria-hidden="true">
                <path d="M7 2a1 1 0 011 1v1h8V3a1 1 0 112 0v1h1a2 2 0 012 2v13a2 2 0 01-2 2H5a2 2 0 01-2-2V6a2 2 0 012-2h1V3a1 1 0 011-1zm12 8H5v9h14v-9zM7 12h4v4H7v-4z" />
              </svg>
              <input
                type="text"
                id="expiry"
                name="expiry"
                placeholder="01/29"
                inputmode="numeric"
                maxlength="5"
                required
              />
            </div>
          </div>

          <!-- CVV -->
          <div class="input-group">
            <label for="cvv">CVV</label>
            <div class="input-wrapper">
              <svg viewBox="0 0 24 24" aria-hidden="true">
                <path d="M12 1a5 5 0 015 5v3h1a2 2 0 012 2v9a2 2 0 01-2 2H6a2 2 0 01-2-2v-9a2 2 0 012-2h1V6a5 5 0 015-5zm-3 8h6V6a3 3 0 10-6 0v3z" />
              </svg>
              <input
                type="password"
                id="cvv"
                name="cvv"
                placeholder="•••"
                inputmode="numeric"
                maxlength="3"
                required
              />
            </div>
          </div>
        </div>

        <button type="submit" class="pay-button">
          <svg viewBox="0 0 24 24" aria-hidden="true">
            <path d="M20.29 5.71a1 1 0 00-1.41 0L9 15.59l-3.88-3.88a1 1 0 00-1.41 1.41l4.59 4.59a1 1 0 001.41 0l10.59-10.59a1 1 0 000-1.41z" />
          </svg>
          Realizar el pago
        </button>

        <div class="success-message" id="successMessage">
          ✔ Pago simulado con éxito. Gracias por tu confianza.
        </div>
      </form>

      <!-- Botón abajo del formulario -->
      <button
        type="button"
        class="index-button"
        style="width: 300px; margin: 20px auto 0; display: block;"
        onclick="window.location.href='index.php'">
        Regresar a la Página Principal
      </button>

    </div>
  </div>
</body>

  <script>
    const form = document.getElementById('paymentForm');
    const cardNumberInput = document.getElementById('cardNumber');
    const expiryInput = document.getElementById('expiry');
    const cvvInput = document.getElementById('cvv');
    const successMessage = document.getElementById('successMessage');

    // Format card number as XXXX-XXXX-XXXX-XXXX
    cardNumberInput.addEventListener('input', () => {
      let value = cardNumberInput.value.replace(/\D/g, '').slice(0, 16);
      let formatted = value.match(/.{1,4}/g);
      cardNumberInput.value = formatted ? formatted.join('-') : value;
    });

    // Format expiry as MM/YY
    expiryInput.addEventListener('input', () => {
      let value = expiryInput.value.replace(/\D/g, '').slice(0, 4);
      if (value.length >= 3) {
        value = value.slice(0, 2) + '/' + value.slice(2);
      }
      expiryInput.value = value;
    });

    // Only numbers for CVV
    cvvInput.addEventListener('input', () => {
      cvvInput.value = cvvInput.value.replace(/\D/g, '').slice(0, 4);
    });

    form.addEventListener('submit', (e) => {
      e.preventDefault();

      if (!form.checkValidity()) {
        form.reportValidity();
        return;
      }

      // Fake success animation
      successMessage.style.display = 'block';
      form.querySelector('.pay-button').disabled = true;
      form.querySelector('.pay-button').style.opacity = '0.7';
      form.querySelector('.pay-button').style.cursor = 'not-allowed';
    });
  </script>
</body>
</html>
