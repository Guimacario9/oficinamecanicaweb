<?php
require_once __DIR__ . "/config/conexao.php";

if(session_status() == PHP_SESSION_NONE){
    session_start();
}

// ===============================
// CADASTRAR
// ===============================
if(isset($_POST['cadastrar'])){

    $nome  = trim($_POST['nome']);
    $login = trim($_POST['login']);
    $senha = trim($_POST['senha']);
    $nivel = trim($_POST['nivel']);

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
        }

        $check->close();
    }
}

// ===============================
// EDITAR
// ===============================
if(isset($_POST['editar'])){

    $id    = intval($_POST['id']);
    $nome  = trim($_POST['nome']);
    $login = trim($_POST['login']);
    $nivel = trim($_POST['nivel']);
    $nova_senha = trim($_POST['nova_senha']);

    if($nivel != 'admin' && $nivel != 'funcionario'){
        $nivel = 'funcionario';
    }

    if(!empty($nova_senha)){

        $senha_hash = password_hash($nova_senha, PASSWORD_DEFAULT);

        $stmt = $conn->prepare(
            "UPDATE usuarios 
             SET nome=?, login=?, nivel=?, senha=? 
             WHERE id=?"
        );
        $stmt->bind_param("ssssi", $nome, $login, $nivel, $senha_hash, $id);

    } else {

        $stmt = $conn->prepare(
            "UPDATE usuarios 
             SET nome=?, login=?, nivel=? 
             WHERE id=?"
        );
        $stmt->bind_param("sssi", $nome, $login, $nivel, $id);
    }

    $stmt->execute();

    echo "<p style='color:green;'>Usuário atualizado!</p>";
}

// ===============================
// EXCLUIR
// ===============================
if(isset($_GET['excluir'])){

    $idExcluir = intval($_GET['excluir']);

    // Impedir excluir a si mesmo
    if($idExcluir == $_SESSION['usuario_oficina']['id']){
        echo "<p style='color:red;'>Você não pode excluir seu próprio usuário!</p>";
    } else {

        $stmt = $conn->prepare("DELETE FROM usuarios WHERE id=?");
        $stmt->bind_param("i", $idExcluir);
        $stmt->execute();

        echo "<p style='color:green;'>Usuário excluído!</p>";
    }
}
?>

<h2 class="title">Registrar Usuário</h2>

<form method="POST">
Nome:<br>
<input type="text" name="nome" required style="width:250px; padding:8px;"><br><br>

Login:<br>
<input type="text" name="login" required style="width:250px; padding:8px;"><br><br>

Senha:<br>
<input type="password" name="senha" required style="width:250px; padding:8px;"><br><br>

Nível:<br>
<select name="nivel" style="width:268px; padding:8px;">
    <option value="funcionario">Funcionário</option>
    <option value="admin">Administrador</option>
</select><br><br>

<input type="submit" name="cadastrar" value="Registrar">
</form>

<hr>

<h2 class="title">Usuários Cadastrados</h2>

<table border="1" cellpadding="8" cellspacing="0" style="border-collapse:collapse; width:100%;">
<tr style="background:#ddd;">
    <th>ID</th>
    <th>Nome</th>
    <th>Login</th>
    <th>Nível</th>
    <th>Nova Senha</th>
    <th>Ações</th>
</tr>

<?php
$usuarios = $conn->query("SELECT id, nome, login, nivel FROM usuarios ORDER BY id DESC");

while($u = $usuarios->fetch_assoc()):
?>

<tr <?php if($u['nivel']=='admin') echo "style='background:#f9f2d0;'"; ?>>
<form method="POST">

<td>
    <?php echo $u['id']; ?>
    <input type="hidden" name="id" value="<?php echo $u['id']; ?>">
</td>

<td>
    <input type="text" name="nome"
     value="<?php echo htmlspecialchars($u['nome']); ?>">
</td>

<td>
    <input type="text" name="login"
     value="<?php echo htmlspecialchars($u['login']); ?>">
</td>

<td>
    <select name="nivel">
        <option value="funcionario"
        <?php if($u['nivel']=='funcionario') echo 'selected'; ?>>
            Funcionário
        </option>
        <option value="admin"
        <?php if($u['nivel']=='admin') echo 'selected'; ?>>
            Administrador
        </option>
    </select>
</td>

<td>
    <input type="password" name="nova_senha"
    placeholder="Deixe em branco p/ manter">
</td>

<td>
    <input type="submit" name="editar" value="Salvar">
    <a href="?area=registrar_usuario&excluir=<?php echo $u['id']; ?>"
       onclick="return confirm('Tem certeza que deseja excluir este usuário?')"
       style="color:red; margin-left:10px;">
       Excluir
    </a>
</td>

</form>
</tr>

<?php endwhile; ?>
</table>