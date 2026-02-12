<?php
$servername = "localhost";
$port = 3306;
$username = "root"; // tu usuario exacto
$password = "";       // pon la contraseña que aparece en Wasmer
$database = "Novaplay";

// Conexión
$conn = new mysqli($servername, $username, $password, $database, $port);

// Verificar conexión
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}
?>