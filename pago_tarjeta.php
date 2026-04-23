<?php include("header.php"); ?>
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
<body>
  <main class="page-content">
  <div class="payment-wrapper">
    <div class="payment-card">
      <div class="icon-circle">
        <!-- Dollar icon -->

<svg xmlns="http://www.w3.org/2000/svg" width="80" height="80" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="0.9" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-receipt-icon lucide-receipt"><path d="M12 17V7"/><path d="M16 8h-6a2 2 0 0 0 0 4h4a2 2 0 0 1 0 4H8"/><path d="M4 3a1 1 0 0 1 1-1 1.3 1.3 0 0 1 .7.2l.933.6a1.3 1.3 0 0 0 1.4 0l.934-.6a1.3 1.3 0 0 1 1.4 0l.933.6a1.3 1.3 0 0 0 1.4 0l.933-.6a1.3 1.3 0 0 1 1.4 0l.934.6a1.3 1.3 0 0 0 1.4 0l.933-.6A1.3 1.3 0 0 1 19 2a1 1 0 0 1 1 1v18a1 1 0 0 1-1 1 1.3 1.3 0 0 1-.7-.2l-.933-.6a1.3 1.3 0 0 0-1.4 0l-.934.6a1.3 1.3 0 0 1-1.4 0l-.933-.6a1.3 1.3 0 0 0-1.4 0l-.933.6a1.3 1.3 0 0 1-1.4 0l-.934-.6a1.3 1.3 0 0 0-1.4 0l-.933.6a1.3 1.3 0 0 1-.7.2 1 1 0 0 1-1-1z"/></svg>     
      
        </div>
      <h1>¿Estás listo para pagar?</h1>
      <p class="subtitle">Agrega tus datos de tarjeta.</p>

      <form id="paymentForm" novalidate>
        <!-- Card Number -->
        <div class="input-group">
          <label for="cardNumber">Número de la tarjeta</label>
          <div class="input-wrapper">
          <input
              type="text"
              id="cardNumber"
              name="cardNumber"
              placeholder="1111-1111-1111-1111"
              inputmode="numeric"
              maxlength="19"
              required
            />
            <svg xmlns="http://www.w3.org/2000/svg" width="56" height="56" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-hash-icon lucide-hash"><line x1="4" x2="20" y1="9" y2="9"/><line x1="4" x2="20" y1="15" y2="15"/><line x1="10" x2="8" y1="3" y2="21"/><line x1="16" x2="14" y1="3" y2="21"/></svg>

          </div>
        </div>

        <!-- Card Holder -->
        <div class="input-group">
          <label for="cardHolder">Titular de la tarjeta</label>
          <div class="input-wrapper">
            <input
              type="text"
              id="cardHolder"
              name="cardHolder"
              placeholder="Fox McCloud"
              required
            />
             <svg xmlns="http://www.w3.org/2000/svg" width="56" height="56" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-user-star-icon lucide-user-star"><path d="M16.051 12.616a1 1 0 0 1 1.909.024l.737 1.452a1 1 0 0 0 .737.535l1.634.256a1 1 0 0 1 .588 1.806l-1.172 1.168a1 1 0 0 0-.282.866l.259 1.613a1 1 0 0 1-1.541 1.134l-1.465-.75a1 1 0 0 0-.912 0l-1.465.75a1 1 0 0 1-1.539-1.133l.258-1.613a1 1 0 0 0-.282-.866l-1.156-1.153a1 1 0 0 1 .572-1.822l1.633-.256a1 1 0 0 0 .737-.535z"/><path d="M8 15H7a4 4 0 0 0-4 4v2"/><circle cx="10" cy="7" r="4"/></svg>

          </div>
        </div>

        <div class="row">
          <!-- Expiry -->
          <div class="input-group">
            <label for="expiry">Fecha de vencimiento</label>
            <div class="input-wrapper">
              <input
                type="text"
                id="expiry"
                name="expiry"
                placeholder="01/29"
                inputmode="numeric"
                maxlength="5"
                required
              />
              <svg xmlns="http://www.w3.org/2000/svg" width="56" height="56" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-calendar1-icon lucide-calendar-1"><path d="M11 14h1v4"/><path d="M16 2v4"/><path d="M3 10h18"/><path d="M8 2v4"/><rect x="3" y="4" width="18" height="18" rx="2"/></svg>
            </div>
          </div>

          <!-- CVV -->
          <div class="input-group">
            <label for="cvv">CVV</label>
            <div class="input-wrapper">
              <input
                type="password"
                id="cvv"
                name="cvv"
                placeholder="•••"
                inputmode="numeric"
                maxlength="3"
                required
              />
              <svg xmlns="http://www.w3.org/2000/svg" width="56" height="56" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-lock-icon lucide-lock"><rect width="18" height="11" x="3" y="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
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
  </main>
</body>

 <?php include("footer.php"); ?>


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

        // Crear un formulario oculto para enviar los datos a carrito.php
        const hiddenForm = document.createElement('form');
        hiddenForm.method = 'POST';
        hiddenForm.action = 'carrito.php';

        const totalInput = document.createElement('input');
        totalInput.type = 'hidden';
        totalInput.name = 'metodo_pago';
        totalInput.value = 'tarjeta';
        hiddenForm.appendChild(totalInput);

        const compraRealizadaInput = document.createElement('input');
        compraRealizadaInput.type = 'hidden';
        compraRealizadaInput.name = 'compra_realizada';
        compraRealizadaInput.value = '1';
        hiddenForm.appendChild(compraRealizadaInput);

        document.body.appendChild(hiddenForm);
        hiddenForm.submit();
      });
  </script>
</body>
</html>
