<?php
$servername = "localhost";
$username = "alonsoria"; 
$password = 'alonsino30$A'; 
$dbname = "novaplay"; 

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}
?>
