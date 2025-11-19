<?php
session_start();
include 'includes/conexao.php';

$usuario = $_POST["usuario"];
$senha_postada = $_POST["senha"];

if (empty($usuario) || empty($senha_postada)) {
    echo "<p style='color:red;'>Usuário ou senha não informados.</p>";
    echo '<a href="tela de login/login.php">Tentar novamente</a>';
    exit;
}

$stmt = $conn->prepare("SELECT * FROM usuarios WHERE usuario = ?");
$stmt->bind_param("s", $usuario);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->num_rows === 1) {
    $user = $resultado->fetch_assoc();
    $senha_hash_db = $user['senha'];

    if (password_verify($senha_postada, $senha_hash_db)) {
        $_SESSION['usuario'] = $user['usuario'];
        header("Location: Corretor.php"); // ✅ vai para a página principal
        exit;
    } else {
        echo "<p style='color:red;'>Usuário ou senha incorretos.</p>";
        echo '<a href="tela de login/login.php">Tentar novamente</a>';
    }
} else {
    echo "<p style='color:red;'>Usuário ou senha incorretos.</p>";
    echo '<a href="tela de login/login.php">Tentar novamente</a>';
}

$stmt->close();
$conn->close();
?>
