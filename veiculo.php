<h2 class="title">Cadastro de Veículos</h2>

<?php

// Buscar clientes
$clientes = $conn->query("SELECT * FROM clientes");

// Salvar veículo
if(isset($_POST['salvar'])){

    $cliente_id = $_POST['cliente_id'];
    $marca  = $_POST['marca'];
    $modelo = $_POST['modelo'];
    $ano    = $_POST['ano'];
    $placa  = $_POST['placa'];
    $chassi = $_POST['chassi'];

    $conn->query("INSERT INTO veiculos 
        (cliente_id,marca,modelo,ano,placa,chassi)
        VALUES 
        ('$cliente_id','$marca','$modelo','$ano','$placa','$chassi')");

    echo "<p style='color:green;'>Veículo cadastrado com sucesso!</p>";
}

?>

<form method="POST">

Cliente:<br>
<select name="cliente_id" required>
<option value="">Selecione</option>
<?php while($c = $clientes->fetch_assoc()): ?>
<option value="<?php echo $c['id']; ?>">
<?php echo $c['nome']; ?>
</option>
<?php endwhile; ?>
</select><br><br>

Marca:<br>
<input type="text" name="marca" required><br><br>

Modelo:<br>
<input type="text" name="modelo" required><br><br>

Ano de Fabricação:<br>
<input type="text" name="ano" required><br><br>

Placa:<br>
<input type="text" name="placa" required><br><br>

Chassi:<br>
<input type="text" name="chassi" required><br><br>

<input type="submit" name="salvar" value="Cadastrar">

</form>

<hr>

<h3>Lista de Veículos</h3>

<?php
$lista = $conn->query("
    SELECT v.*, c.nome 
    FROM veiculos v
    JOIN clientes c ON v.cliente_id = c.id
");

while($v = $lista->fetch_assoc()){
    echo "<strong>".$v['marca']." ".$v['modelo']."</strong> - ";
    echo "Ano: ".$v['ano']." - ";
    echo "Placa: ".$v['placa']." - ";
    echo "Chassi: ".$v['chassi']." - ";
    echo "Cliente: ".$v['nome']."<br>";
}
?>
