<?php
// Inicia a sessão
session_start(); 

// Verifica se o formulário foi submetido
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtém os dados do formulário
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Configurações do banco de dados
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

    // Prepara a consulta SQL para verificar o login
    $stmt = $conn->prepare("SELECT * FROM usuario WHERE login=? AND senha=?");
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    // Verifica se encontrou algum usuário com as credenciais fornecidas
    if ($result->num_rows > 0) {
        // Usuário autenticado com sucesso
        $_SESSION['username'] = $username; // Salva o nome do usuário na sessão
        header("Location: ../frontend/tela_com_agendamento.php");
        exit();
    } else {
        // Usuário não encontrado ou credenciais inválidas
        echo "<script>alert('Usuário ou senha inválidos!');</script>";
        header("Refresh: 0; url=../frontend/login.html");
    }

    // Fecha a conexão com o banco de dados
    $stmt->close();
    $conn->close();
}
?>