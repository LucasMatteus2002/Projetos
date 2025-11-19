<?php
session_start();

// Se o usuário NÃO estiver logado, redireciona para o login.
if (!isset($_SESSION['usuario'])) {
    header("Location: ../tela de login/login.php");
    exit;
}