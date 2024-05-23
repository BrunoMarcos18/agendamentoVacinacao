<?php
$servername = "localhost";
$username_db = "root";
$password_db = "";
$dbname = "agendamento_vacinacao";

// Conecta ao banco de dados
$conn = new mysqli($servername, $username_db, $password_db, $dbname);

// Verifica se a conexão foi bem-sucedida
if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}
?>
