<?php
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
<p style="margin-top:15px;">
    Não possui conta?
    <a href="?area=registrar_usuario">Registrar usuário</a>
</p>
</form>
