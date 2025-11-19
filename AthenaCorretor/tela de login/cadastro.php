<?php
session_start();
if (isset($_SESSION['usuario'])) {
    header('Location: ../Corretor.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Cadastro no Olimpo</title>
  <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
  <link rel="stylesheet" href="stayle.css"> 
</head>

<body>
  <main class="container">
    
    <form action="registro.php" method="POST">
      <h1>Crie sua conta, Mortal</h1>
      
      <div class="input-box">
        <input placeholder="Novo Usuário" type="text" name="usuario" required>
        <i class="bx bxs-user"></i>
      </div>

      <div class="input-box">
        <input placeholder="Nova Senha" type="password" name="senha" required>
        <i class="bx bxs-lock-alt"></i>
      </div>
      
      <button type="submit">Cadastrar</button>
        
      <div class="registro-link">
          <p>Já tem uma conta? <a href="login.php">Faça Login</a></p>
      </div>
    
    </form>
  </main>
</body>
</html>