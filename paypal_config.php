<?php
require 'vendor/autoload.php';

use PayPal\Rest\ApiContext;
use PayPal\Auth\OAuthTokenCredential;

$paypal = new ApiContext(
    new OAuthTokenCredential(
        'AZJp8fzGVpSLY6dqtLeujdpK7HkFTFZxzlindd8Qvv3l7aFDDWP7Uw147fZmpzdijyZTts5Cr2NikjhN',     // 👉 coloca aquí tu Client ID
        'EJEFn4Oh9q7ewc_kDeLNrsStgmUh3zKHrpwz-jtrHSY9oKPTV8eAiwiZRYmqf4_qM1_-sOzkKKAc_Idt'  // 👉 y aquí tu Secret
    )
);

$paypal->setConfig([
    'mode' => 'sandbox', // cambia a 'live' en producción
]);
?>