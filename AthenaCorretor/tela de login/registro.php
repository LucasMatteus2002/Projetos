<?php
session_start();

include '../includes/conexao.php';


$usuario = $_POST['usuario'];
$senha_plana = $_POST['senha']; 


if (empty($usuario) || empty($senha_plana)) {
    echo "Usuário ou senha não podem estar em branco.";
    echo '<a href="cadastro.php">Tentar novamente</a>';
    exit;
}


$senha_hash = password_hash($senha_plana, PASSWORD_DEFAULT);


$stmt = $conn->prepare("SELECT id FROM usuarios WHERE usuario = ?");
$stmt->bind_param("s", $usuario);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->num_rows > 0) {
   
    echo "Este nome de usuário já está em uso.";
    echo '<a href="cadastro.php">Tentar novamente</a>';
} else {
    
    $stmt_insert = $conn->prepare("INSERT INTO usuarios (usuario, senha) VALUES (?, ?)");
    $stmt_insert->bind_param("ss", $usuario, $senha_hash);
    
    if ($stmt_insert->execute()) {
        
        echo "Cadastro realizado com sucesso!";
        echo '<a href="login.php">Ir para o Login</a>';
    } else {
        
        echo "Erro ao cadastrar: " . $stmt_insert->error;
    }
    $stmt_insert->close();
}

$stmt->close();
$conn->close();

?>