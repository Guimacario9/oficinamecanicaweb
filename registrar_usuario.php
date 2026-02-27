<?php
require_once __DIR__ . "/config/conexao.php";
?>
<?php

if(isset($_POST['cadastrar'])){

    $nome  = trim($_POST['nome']);
    $login = trim($_POST['login']);
    $senha = trim($_POST['senha']);
    $nivel = trim($_POST['nivel']); // agora vem do select

    // segurança extra: só permite esses dois valores
    if($nivel != 'admin' && $nivel != 'funcionario'){
        $nivel = 'funcionario';
    }

    if(!empty($nome) && !empty($login) && !empty($senha)){

        $check = $conn->prepare("SELECT id FROM usuarios WHERE login=?");
        $check->bind_param("s", $login);
        $check->execute();
        $check->store_result();

        if($check->num_rows > 0){

            echo "<p style='color:red;'>Login já existe!</p>";

        } else {

            $senha_hash = password_hash($senha, PASSWORD_DEFAULT);

            $stmt = $conn->prepare(
                "INSERT INTO usuarios (nome, login, senha, nivel)
                 VALUES (?, ?, ?, ?)"
            );

            $stmt->bind_param("ssss", $nome, $login, $senha_hash, $nivel);
            $stmt->execute();

            echo "<p style='color:green;'>Usuário cadastrado!</p>";

            echo "<script>
                    setTimeout(function(){
                        location.href='index.php?area=login';
                    },1500);
                  </script>";
        }

        $check->close();
    }
}
?>
<h2 class="title">Registrar Usuário</h2>

<form method="POST">

Nome:<br>
<input type="text" name="nome" required style="width:250px; padding:8px;">
<br><br>

Login:<br>
<input type="text" name="login" required style="width:250px; padding:8px;">
<br><br>

Senha:<br>
<input type="password" name="senha" required style="width:250px; padding:8px;">
<br><br>

Nível:<br>
<select name="nivel" style="width:268px; padding:8px;">
    <option value="funcionario">Funcionário</option>
    <option value="admin">Administrador</option>
</select>
<br><br>

<input type="submit" name="cadastrar" value="Registrar">

</form>

<p style="margin-top:15px;">
    <a href="?area=login">← Voltar para login</a>
</p>
