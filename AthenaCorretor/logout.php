<?php
session_start();

session_unset();

session_destroy();

header("Location: tela de login/login.php");
exit;
?>