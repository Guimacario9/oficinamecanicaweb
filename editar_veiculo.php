<?php

$conn = new mysqli(
"sql212.infinityfree.com",
"if0_41221516",
"eMUzZeSUhPF",
"if0_41221516_db_oficinamecanica"
);

// pegar ID
$id = intval($_GET['id']);

// salvar alteração
if(isset($_POST['atualizar'])){

$cliente_id = $_POST['cliente_id'];
$marca = $_POST['marca'];
$modelo = $_POST['modelo'];
$ano = $_POST['ano'];
$placa = $_POST['placa'];
$chassi = $_POST['chassi'];

$conn->query("
UPDATE veiculos SET
cliente_id='$cliente_id',
marca='$marca',
modelo='$modelo',
ano='$ano',
placa='$placa',
chassi='$chassi'
WHERE id=$id
");

echo "<p style='color:green;'>Veículo atualizado com sucesso!</p>";
}

// buscar dados do veículo
$veiculo = $conn->query("SELECT * FROM veiculos WHERE id=$id");
$v = $veiculo->fetch_assoc();

// buscar clientes
$clientes = $conn->query("SELECT * FROM clientes");

?>

<h2>Editar Veículo</h2>

<form method="POST">

Cliente:<br>
<select name="cliente_id" required>

<?php while($c = $clientes->fetch_assoc()): ?>

<option value="<?php echo $c['id']; ?>"
<?php if($c['id'] == $v['cliente_id']) echo "selected"; ?>>

<?php echo $c['nome']; ?>

</option>

<?php endwhile; ?>

</select>

<br><br>

Marca:<br>
<select name="marca">

<option value="fiat" <?php if($v['marca']=="fiat") echo "selected"; ?>>Fiat</option>
<option value="chevrolet" <?php if($v['marca']=="chevrolet") echo "selected"; ?>>Chevrolet</option>
<option value="volkswagen" <?php if($v['marca']=="volkswagen") echo "selected"; ?>>Volkswagen</option>
<option value="ford" <?php if($v['marca']=="ford") echo "selected"; ?>>Ford</option>
<option value="honda" <?php if($v['marca']=="honda") echo "selected"; ?>>Honda</option>
<option value="hyundai" <?php if($v['marca']=="hyundai") echo "selected"; ?>>Hyundai</option>
<option value="jeep" <?php if($v['marca']=="jeep") echo "selected"; ?>>Jeep</option>

</select>

<br><br>

Modelo:<br>
<input type="text" name="modelo" value="<?php echo $v['modelo']; ?>">

<br><br>

Ano:<br>
<input type="text" name="ano" value="<?php echo $v['ano']; ?>">

<br><br>

Placa:<br>
<input type="text" name="placa" value="<?php echo $v['placa']; ?>">

<br><br>

Chassi:<br>
<input type="text" name="chassi" value="<?php echo $v['chassi']; ?>">

<br><br>

<input type="submit" name="atualizar" value="Salvar Alterações">

</form>