<?php
require 'paypal_config.php';

use PayPal\Api\Payer;
use PayPal\Api\Amount;
use PayPal\Api\Transaction;
use PayPal\Api\RedirectUrls;
use PayPal\Api\Payment;

if (!isset($_GET['total'])) {
    die("Error: no se recibió el total.");
}

$total = floatval($_GET['total']);

$payer = new Payer();
$payer->setPaymentMethod('paypal');

$amount = new Amount();
$amount->setCurrency('MXN')
       ->setTotal($total);

$transaction = new Transaction();
$transaction->setAmount($amount)
             ->setDescription("Compra en Novaplay - E-commerce de videojuegos");

$redirectUrls = new RedirectUrls();
$redirectUrls->setReturnUrl("http://localhost/novaplay/success.php")
             ->setCancelUrl("http://localhost/novaplay/cancel.php");

$payment = new Payment();
$payment->setIntent('sale')
        ->setPayer($payer)
        ->setRedirectUrls($redirectUrls)
        ->setTransactions([$transaction]);

try {
    $payment->create($paypal);
    header("Location: " . $payment->getApprovalLink());
    exit;
} catch (Exception $ex) {
    echo "Error al crear el pago: " . $ex->getMessage();
}
