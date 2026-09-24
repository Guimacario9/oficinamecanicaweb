<h2 class="title">Cadastro de Clientes</h2>

<?php

// ========================
// FUNÇÕES DE VALIDAÇÃO
// ========================

function validarCPF($cpf){
    $cpf = preg_replace('/[^0-9]/', '', $cpf);
    if(strlen($cpf) != 11 || preg_match('/(\d)\1{10}/', $cpf)) return false;

    for ($t = 9; $t < 11; $t++) {
        for ($d = 0, $c = 0; $c < $t; $c++) {
            $d += $cpf[$c] * (($t + 1) - $c);
        }
        $d = ((10 * $d) % 11) % 10;
        if ($cpf[$c] != $d) return false;
    }
    return true;
}

function validarCNPJ($cnpj){
    $cnpj = preg_replace('/[^0-9]/', '', $cnpj);
    if(strlen($cnpj) != 14) return false;
    return true;
}

// ========================
// EDITAR CLIENTE
// ========================

$editar = false;

if(isset($_GET['editar'])){

$id = intval($_GET['editar']);

$res = $conn->query("SELECT * FROM clientes WHERE id=$id");

$cliente = $res->fetch_assoc();

$editar = true;

}

// ========================
// EXCLUIR
// ========================

if(isset($_GET['excluir'])){

$id = intval($_GET['excluir']);

$conn->query("DELETE FROM clientes WHERE id=$id");

echo "<p style='color:red'>Cliente excluído!</p>";

}

// ========================
// BUSCAR
// ========================

$where = "";

if(isset($_GET['buscar'])){

$busca = $conn->real_escape_string($_GET['buscar']);

$where = "WHERE nome LIKE '%$busca%'";

}

// ========================
// SALVAR CLIENTE
// ========================

if(isset($_POST['salvar'])){

$tipo = $_POST['tipo'];
$doc  = $_POST['cpf_cnpj'];

if($tipo == "PF" && !validarCPF($doc)){

echo "<p style='color:red;'>CPF inválido!</p>";

}
elseif($tipo == "PJ" && !validarCNPJ($doc)){

echo "<p style='color:red;'>CNPJ inválido!</p>";

}
else{

$stmt = $conn->prepare("
INSERT INTO clientes
(tipo,nome,nome_fantasia,telefone,celular,email,cpf_cnpj,endereco,numero,bairro,cidade,uf,cep)
VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)
");

$stmt->bind_param("sssssssssssss",

$_POST['tipo'],
$_POST['nome'],
$_POST['nome_fantasia'],
$_POST['telefone'],
$_POST['celular'],
$_POST['email'],
$_POST['cpf_cnpj'],
$_POST['endereco'],
$_POST['numero'],
$_POST['bairro'],
$_POST['cidade'],
$_POST['uf'],
$_POST['cep']

);

$stmt->execute();

echo "<p style='color:green;'>Cliente cadastrado!</p>";

}

}

// ========================
// ATUALIZAR CLIENTE
// ========================

if(isset($_POST['atualizar'])){

$id = $_POST['id'];

$stmt = $conn->prepare("
UPDATE clientes
SET tipo=?, nome=?, nome_fantasia=?, telefone=?, celular=?, email=?, cpf_cnpj=?, endereco=?, numero=?, bairro=?, cidade=?, uf=?, cep=?
WHERE id=?
");

$stmt->bind_param("sssssssssssssi",

$_POST['tipo'],
$_POST['nome'],
$_POST['nome_fantasia'],
$_POST['telefone'],
$_POST['celular'],
$_POST['email'],
$_POST['cpf_cnpj'],
$_POST['endereco'],
$_POST['numero'],
$_POST['bairro'],
$_POST['cidade'],
$_POST['uf'],
$_POST['cep'],
$id

);

$stmt->execute();

echo "<p style='color:green;'>Cliente atualizado!</p>";

}

?>

<!-- BUSCA -->

<form method="GET">

<input type="hidden" name="area" value="clientes">

<input type="text" name="buscar" placeholder="Buscar cliente">

<input type="submit" value="Buscar">

</form>

<hr>

<form method="POST">

<?php if($editar){ ?>

<input type="hidden" name="id" value="<?php echo $cliente['id']; ?>">

<?php } ?>

Tipo:<br>

<select name="tipo" id="tipo" onchange="alterarTipo()" required>

<option value="PF" <?php if($editar && $cliente['tipo']=="PF") echo "selected"; ?>>Pessoa Física</option>

<option value="PJ" <?php if($editar && $cliente['tipo']=="PJ") echo "selected"; ?>>Pessoa Jurídica</option>

</select><br><br>


Nome / Razão Social:<br>

<input type="text" name="nome"
value="<?php echo $editar ? $cliente['nome'] : ''; ?>" required><br><br>


<div id="fantasia" style="display:<?php echo ($editar && $cliente['tipo']=="PJ") ? 'block' : 'none'; ?>;">

Nome Fantasia:<br>

<input type="text" name="nome_fantasia"
value="<?php echo $editar ? $cliente['nome_fantasia'] : ''; ?>"><br><br>

</div>


CPF / CNPJ:<br>

<input type="text" name="cpf_cnpj" id="cpf_cnpj"
value="<?php echo $editar ? $cliente['cpf_cnpj'] : ''; ?>" required><br><br>


Telefone:<br>

<input type="text" name="telefone"
value="<?php echo $editar ? $cliente['telefone'] : ''; ?>"><br><br>


Celular:<br>

<input type="text" name="celular"
value="<?php echo $editar ? $cliente['celular'] : ''; ?>"><br><br>


Email:<br>

<input type="email" name="email"
value="<?php echo $editar ? $cliente['email'] : ''; ?>"><br><br>


Endereço:<br>

<input type="text" name="endereco"
value="<?php echo $editar ? $cliente['endereco'] : ''; ?>"><br><br>


Número:<br>

<input type="text" name="numero"
value="<?php echo $editar ? $cliente['numero'] : ''; ?>"><br><br>


Bairro:<br>

<input type="text" name="bairro"
value="<?php echo $editar ? $cliente['bairro'] : ''; ?>"><br><br>


Cidade:<br>

<input type="text" name="cidade"
value="<?php echo $editar ? $cliente['cidade'] : ''; ?>"><br><br>


UF:<br>

<input type="text" name="uf" maxlength="2"
value="<?php echo $editar ? $cliente['uf'] : ''; ?>"><br><br>


CEP:<br>

<input type="text" name="cep"
value="<?php echo $editar ? $cliente['cep'] : ''; ?>"><br><br>


<?php if($editar){ ?>

<input type="submit" name="atualizar" value="Atualizar Cliente">

<?php } else { ?>

<input type="submit" name="salvar" value="Cadastrar">

<?php } ?>

</form>

<hr>

<h3>Lista de Clientes</h3>

<?php

$result = $conn->query("SELECT * FROM clientes $where ORDER BY id DESC");

while($c = $result->fetch_assoc()){

echo "<strong>".$c['nome']."</strong>";

if($c['tipo']=="PJ"){
echo " (".$c['nome_fantasia'].")";
}

echo "<br>";
echo "Documento: ".$c['cpf_cnpj']."<br>";
echo "Telefone: ".$c['telefone']."<br>";

echo "
<a href='?area=clientes&editar=".$c['id']."' class='btn-editar'>Editar</a>

<a href='?area=clientes&excluir=".$c['id']."' 
class='btn-excluir'
onclick=\"return confirm('Excluir cliente?')\">
Excluir
</a>
";

echo "<hr>";

}

?>

<script>

function alterarTipo(){

var tipo = document.getElementById("tipo").value;

var fantasia = document.getElementById("fantasia");

fantasia.style.display = (tipo === "PJ") ? "block" : "none";

}

</script>