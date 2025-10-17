<?php
$servername = "db.fr-pari1.bengt.wasmernet.com";
$port = 10272;
$username = "28c30b0b7f68800076e85d6a2f01"; // tu usuario exacto
$password = "068f28c3-0b0c-7124-8000-2a06da50ca8e";       // pon la contraseña que aparece en Wasmer
$database = "Novaplay";

// Conexión
$conn = new mysqli($servername, $username, $password, $database, $port);

// Verificar conexión
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}
?>