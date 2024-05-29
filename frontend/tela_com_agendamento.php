<?php
session_start();
require_once '../conexao/conexao.php';

// Verifica se o usuário está logado
if (!isset($_SESSION['username'])) {
    header("Location: ../frontend/login.html");
    exit();
}

$username = $_SESSION['username'];

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

// Obtém o CPF do usuário logado
$stmt = $conn->prepare("SELECT cpf FROM usuario WHERE login = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$cpf = $user['cpf'];

$edit_agendamento = null;
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['data']) && isset($_POST['hora']) && isset($_POST['edit_id'])) {
        // Atualiza um agendamento existente
        $data = $_POST['data'];
        $hora = $_POST['hora'];
        $edit_id = $_POST['edit_id'];

        $stmt = $conn->prepare("UPDATE agendamento SET data=?, hora=? WHERE idagendamento=? AND usuario_cpf=?");
        $stmt->bind_param("ssis", $data, $hora, $edit_id, $cpf);
        if ($stmt->execute()) {
            echo "<script>alert('Agendamento atualizado com sucesso!');</script>";
        } else {
            echo "<script>alert('Falha ao atualizar o agendamento.');</script>";
        }
    } elseif (isset($_POST['edit']) && isset($_POST['id'])) {
        // Prepara os dados para edição
        $edit_id = $_POST['id'];
        $stmt = $conn->prepare("SELECT * FROM agendamento WHERE idagendamento=? AND usuario_cpf=?");
        $stmt->bind_param("is", $edit_id, $cpf);
        $stmt->execute();
        $result = $stmt->get_result();
        $edit_agendamento = $result->fetch_assoc();
    } elseif (isset($_POST['data']) && isset($_POST['hora'])) {
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
    } elseif (isset($_POST['delete']) && isset($_POST['id'])) {
        // Exclui um agendamento existente
        $delete_id = $_POST['id'];

        $stmt = $conn->prepare("DELETE FROM agendamento WHERE idagendamento=? AND usuario_cpf=?");
        $stmt->bind_param("is", $delete_id, $cpf);
        if ($stmt->execute()) {
            echo "<script>alert('Agendamento excluído com sucesso!');</script>";
        } else {
            echo "<script>alert('Falha ao excluir o agendamento.');</script>";
        }
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
    <link rel="icon" type="image/png" href="../imagens/LogoJanela.jpg">
</head>
<body>
    <div class="container" id="containerAgendamentos">
        <img src="../imagens/vacine-se.png" alt="Logo" width="250px" class="logo" >
        <h2>Seus Agendamentos</h2>
        <?php if ($edit_agendamento): ?>
            <form action="tela_com_agendamento.php" method="POST" id="formAgendamentos">
                <div class="input-group" id="input-group-agendamentos">
                    <label for="data">Data:</label>
                    <input type="date" id="data" name="data" value="<?php echo $edit_agendamento['data']; ?>" required>
                </div>
                <div class="input-group" id="input-group-agendamentos">
                    <label for="hora">Horário:</label>
                    <input type="time" id="hora" name="hora" value="<?php echo $edit_agendamento['hora']; ?>" required>
                </div>
                <div class="input-group" id="input-group-agendamentos">
                    <label for="local">Local:</label>
                    <input type="text" id="local" name="local" value="Vila Germânica (Setor 3)" readonly>
                </div>
                <input type="hidden" name="edit_id" value="<?php echo $edit_agendamento['idagendamento']; ?>">
                <button type="submit">Atualizar</button>
            </form>
        <?php else: ?>
            <?php if (count($agendamentos) > 0): ?>
                <table class="table">
                    <tr>
                        <th>Data</th>
                        <th>Hora</th>
                        <th>Ações</th>
                    </tr>
                    <?php foreach ($agendamentos as $agendamento): ?>
                        <tr>
                            <td><?php echo $agendamento['data']; ?></td>
                            <td><?php echo $agendamento['hora']; ?></td>
                            <td>
                                <div class="action-buttons">
                                    <form action="tela_com_agendamento.php" method="POST">
                                        <input type="hidden" name="id" value="<?php echo $agendamento['idagendamento']; ?>">
                                        <input type="hidden" name="edit" value="1">
                                        <button type="submit">Editar</button>
                                    </form>
                                    <form action="tela_com_agendamento.php" method="POST">
                                        <input type="hidden" name="id" value="<?php echo $agendamento['idagendamento']; ?>">
                                        <input type="hidden" name="delete" value="1">
                                        <button type="submit">Excluir</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            <?php else: ?>
                <form action="tela_com_agendamento.php" method="POST">
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
        <?php endif; ?>
        <p class="message">Não se esqueça de levar seu comprovante de vacinação ou sua carteirinha de vacinação.</p>
        <h2>Todos os Agendamentos</h2>
        <table class="table">
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