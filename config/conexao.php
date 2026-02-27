<?php
// config/conexao.php

$host = "localhost";
$user = "root";
$pass = "";
$banco = "oficinasoft";

$conn = new mysqli($host, $user, $pass, $banco);

if ($conn->connect_error) {
    die("Erro na conexão com o banco: " . $conn->connect_error);
}

$conn->set_charset("utf8");