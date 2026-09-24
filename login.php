<?php
// 🔒 Inicia sessão somente se ainda não estiver ativa
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// CONEXÃO SEPARADA
require_once __DIR__ . "/config/conexao.php";

if(isset($_POST['logar'])){

    $login = trim($_POST['login']);
    $senha = trim($_POST['senha']);

    if(!empty($login) && !empty($senha)){

        $stmt = $conn->prepare(
            "SELECT id, nome, senha, nivel FROM usuarios WHERE login=?"
        );

        $stmt->bind_param("s", $login);
        $stmt->execute();

        $stmt->bind_result($id, $nome, $senha_hash, $nivel);

        if($stmt->fetch() && password_verify($senha, $senha_hash)){

            session_regenerate_id(true);

            $_SESSION['usuario_oficina'] = array(
                'id'    => $id,
                'nome'  => $nome,
                'nivel' => $nivel
            );

            $stmt->close();

            // 🔥 LOG DE LOGIN
            $ip = $_SERVER['REMOTE_ADDR'];
            $acao = "Login realizado";

            $stmt_log = $conn->prepare(
                "INSERT INTO logs (usuario_id, acao, ip) VALUES (?, ?, ?)"
            );

            if($stmt_log){
                $stmt_log->bind_param("iss", $id, $acao, $ip);
                $stmt_log->execute();
                $stmt_log->close();
            }

            echo "<script>location.href='index.php';</script>";
            exit;

        } else {
            echo "<p style='color:red;'>Login inválido!</p>";
        }

        $stmt->close();

    } else {
        echo "<p style='color:red;'>Preencha todos os campos.</p>";
    }
}
?>

<h2 class="title">Login do Sistema</h2>

<form method="POST">
Login:<br>
<input type="text" name="login" required style="width:250px; padding:8px;">
<br><br>

Senha:<br>
<input type="password" name="senha" required style="width:250px; padding:8px;"><br><br>

<input type="submit" name="logar" value="Entrar">
</form>