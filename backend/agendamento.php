<?php
session_start();
require_once '../conexao/conexao.php';

// Verifica se o usuário está logado
if (!isset($_SESSION['username'])) {
    header("Location: ../frontend/login.html");
    exit();
}

$username = $_SESSION['username'];

// Conecta ao banco de dados
$conn = new mysqli($servername, $username_db, $password_db, $dbname);

// Verifica se a conexão foi bem-sucedida
if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}

// Obtém o CPF do usuário logado
$stmt = $conn->prepare("SELECT cpf FROM usuario WHERE login = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$cpf = $user['cpf'];

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['data']) && isset($_POST['hora'])) {
    // Insere um novo agendamento
    $data = $_POST['data'];
    $hora = $_POST['hora'];

    $stmt = $conn->prepare("INSERT INTO agendamento (data, hora, usuario_cpf) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $data, $hora, $cpf);
    if ($stmt->execute()) {
        echo "<script>alert('Agendamento realizado com sucesso!');</script>";
    } else {
        echo "<script>alert('Falha ao realizar o agendamento.');</script>";
    }
}

// Obtém os agendamentos do usuário
$stmt = $conn->prepare("SELECT * FROM agendamento WHERE usuario_cpf = ?");
$stmt->bind_param("s", $cpf);
$stmt->execute();
$result = $stmt->get_result();
$agendamentos = $result->fetch_all(MYSQLI_ASSOC);

// Obtém todos os agendamentos para exibir na tabela
$todos_agendamentos = $conn->query("SELECT usuario_cpf, data, hora FROM agendamento")->fetch_all(MYSQLI_ASSOC);

// Fecha a conexão com o banco de dados
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agendamentos - Agendamento de Vacinação</title>
    <link rel="stylesheet" href="../estilos/styles.css">
</head>
<body>
    <div class="container">
        <h2>Seus Agendamentos</h2>
        <?php if (count($agendamentos) > 0): ?>
            <table>
                <tr>
                    <th>Data</th>
                    <th>Hora</th>
                </tr>
                <?php foreach ($agendamentos as $agendamento): ?>
                    <tr>
                        <td><?php echo $agendamento['data']; ?></td>
                        <td><?php echo $agendamento['hora']; ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>
        <?php else: ?>
            <form action="agendamento.php" method="POST">
                <div class="input-group">
                    <label for="data">Data:</label>
                    <input type="date" id="data" name="data" required>
                </div>
                <div class="input-group">
                    <label for="hora">Horário:</label>
                    <input type="time" id="hora" name="hora" required>
                </div>
                <div class="input-group">
                    <label for="local">Local:</label>
                    <input type="text" id="local" name="local" value="Vila Germânica (Setor 3)" readonly>
                </div>
                <button type="submit">Agendar</button>
            </form>
        <?php endif; ?>
        <p>Não se esqueça de levar seu comprovante de vacinação ou sua carteirinha de vacinação.</p>
        <h2>Todos os Agendamentos</h2>
        <table>
            <tr>
                <th>CPF</th>
                <th>Data</th>
                <th>Hora</th>
            </tr>
            <?php foreach ($todos_agendamentos as $agendamento): ?>
                <tr>
                    <td><?php echo $agendamento['usuario_cpf']; ?></td>
                    <td><?php echo $agendamento['data']; ?></td>
                    <td><?php echo $agendamento['hora']; ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>
</body>
</html>
