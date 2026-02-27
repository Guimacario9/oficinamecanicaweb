<?php
if(!isset($_GET['id'])){
    echo "Cliente não informado.";
    exit;
}

$id = intval($_GET['id']);

$stmt = $conn->prepare("SELECT * FROM clientes WHERE id=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$cliente = $result->fetch_assoc();

if(!$cliente){
    echo "Cliente não encontrado.";
    exit;
}
?>

<h2>Editar Cliente</h2>

<form method="POST">

Tipo:<br>
<select name="tipo">
    <option value="PF" <?php if($cliente['tipo']=="PF") echo "selected"; ?>>Pessoa Física</option>
    <option value="PJ" <?php if($cliente['tipo']=="PJ") echo "selected"; ?>>Pessoa Jurídica</option>
</select><br><br>

Nome:<br>
<input type="text" name="nome" value="<?= htmlspecialchars($cliente['nome']) ?>"><br><br>

Nome Fantasia:<br>
<input type="text" name="nome_fantasia" value="<?= htmlspecialchars($cliente['nome_fantasia']) ?>"><br><br>

CPF/CNPJ:<br>
<input type="text" name="cpf_cnpj" value="<?= htmlspecialchars($cliente['cpf_cnpj']) ?>"><br><br>

Telefone:<br>
<input type="text" name="telefone" value="<?= htmlspecialchars($cliente['telefone']) ?>"><br><br>

Celular:<br>
<input type="text" name="celular" value="<?= htmlspecialchars($cliente['celular']) ?>"><br><br>

Email:<br>
<input type="email" name="email" value="<?= htmlspecialchars($cliente['email']) ?>"><br><br>

Endereço:<br>
<input type="text" name="endereco" value="<?= htmlspecialchars($cliente['endereco']) ?>"><br><br>

Número:<br>
<input type="text" name="numero" value="<?= htmlspecialchars($cliente['numero']) ?>"><br><br>

Bairro:<br>
<input type="text" name="bairro" value="<?= htmlspecialchars($cliente['bairro']) ?>"><br><br>

Cidade:<br>
<input type="text" name="cidade" value="<?= htmlspecialchars($cliente['cidade']) ?>"><br><br>

UF:<br>
<input type="text" name="uf" value="<?= htmlspecialchars($cliente['uf']) ?>"><br><br>

CEP:<br>
<input type="text" name="cep" value="<?= htmlspecialchars($cliente['cep']) ?>"><br><br>

<input type="submit" name="atualizar" value="Atualizar">

</form>
<?php
if(isset($_POST['salvar'])){

    $nome = $_POST['nome'];
    $telefone = $_POST['telefone'];
    $email = $_POST['email'];

    $stmt = $conn->prepare("UPDATE clientes SET nome=?, telefone=?, email=? WHERE id=?");
    $stmt->bind_param("sssi", $nome, $telefone, $email, $id);
    $stmt->execute();

    echo "<p>Cliente atualizado com sucesso!</p>";
}
?>