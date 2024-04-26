<?php
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
    $sql = "SELECT * FROM usuario WHERE login='$username' AND senha='$password'";
    $result = $conn->query($sql);

    // Verifica se encontrou algum usuário com as credenciais fornecidas
    if ($result->num_rows > 0) {
        // Usuário autenticado com sucesso
        echo "Login bem-sucedido!";
    } else {
        // Usuário não encontrado ou credenciais inválidas
        echo "Usuário ou senha inválidos!";
    }

    // Fecha a conexão com o banco de dados
    $conn->close();
}
?>
