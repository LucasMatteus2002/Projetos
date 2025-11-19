<?php
session_start();

// se o usuário JÁ estiver logado, manda pro Corretor
if (isset($_SESSION['usuario']) && !empty($_SESSION['usuario'])) {
    header('Location: ../Corretor.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Acesso ao Olimpo</title>
  <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
  <link rel="stylesheet" href="stayle.css">
</head>
<body>
  <main class="container">
    <form action="../validar_login.php" method="POST">
      <h1>Entre no Olimpo, Mortal</h1>
      <div class="input-box">
        <input placeholder="Usuário" type="text" name="usuario" required>
        <i class="bx bxs-user"></i>
      </div>
      <div class="input-box">
        <input placeholder="Senha" type="password" name="senha" required>
        <i class="bx bxs-lock-alt"></i>
      </div>
      <button class="entrar" type="submit">Entrar</button>
      <div class="registro-link">
        <p>Não tem uma conta? <a href="cadastro.php">Cadastre-se</a></p>
      </div>
    </form>
  </main>
</body>
</html>
