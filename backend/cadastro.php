<?php
require_once '../conexao/conexao.php';

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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $cpf = $_POST['cpf'];
    $nome_completo = $_POST['nome_completo'];
    $senha = $_POST['senha'];
    $login = $_POST['login'];

    // Verifica se o login já existe
    $stmt = $conn->prepare("SELECT * FROM usuario WHERE login = ?");
    $stmt->bind_param("s", $login);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Login já existe, exibe mensagem de erro
        echo "<script>alert('Erro: Este login já está em uso. Por favor, escolha outro.'); window.location.href = '../frontend/cadastro.html';</script>";
    } else {
        // Verifica se o CPF já existe
        $stmt = $conn->prepare("SELECT * FROM usuario WHERE cpf = ?");
        $stmt->bind_param("s", $cpf);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // CPF já existe, exibe mensagem de erro
            echo "<script>alert('Erro: Este CPF já está cadastrado. Por favor, verifique suas informações.'); window.location.href = '../frontend/cadastro.html';</script>";
        } else {
            // CPF e login não existem, insere o novo usuário
            $stmt = $conn->prepare("INSERT INTO usuario (cpf, nome_completo, senha, login) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $cpf, $nome_completo, $senha, $login);

            if ($stmt->execute()) {
                echo "<script>alert('Cadastro realizado com sucesso!'); window.location.href = '../frontend/login.html';</script>";
            } else {
                echo "<script>alert('Erro ao realizar o cadastro. Por favor, tente novamente.'); window.location.href = '../frontend/cadastro.html';</script>";
            }
        }
    }

    $stmt->close();
}

$conn->close();
?>