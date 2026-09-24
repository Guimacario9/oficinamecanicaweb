<h2 class="title">Cadastro de Veículos</h2>

<?php

$clientes = $conn->query("SELECT * FROM clientes");

$editar = false;

// =====================
// EDITAR VEICULO
// =====================

if(isset($_GET['editar'])){

$id = intval($_GET['editar']);

$res = $conn->query("SELECT * FROM veiculos WHERE id=$id");

$veiculo = $res->fetch_assoc();

$editar = true;

}

// =====================
// SALVAR VEICULO
// =====================

if(isset($_POST['salvar'])){

$cliente_id = $_POST['cliente_id'];
$marca  = $_POST['marca'];
$modelo = $_POST['modelo'];
$ano    = $_POST['ano'];
$placa  = strtoupper($_POST['placa']);
$chassi = $_POST['chassi'];

$stmt = $conn->prepare("
INSERT INTO veiculos
(cliente_id,marca,modelo,ano,placa,chassi)
VALUES (?,?,?,?,?,?)
");

$stmt->bind_param("isssss",
$cliente_id,
$marca,
$modelo,
$ano,
$placa,
$chassi
);

$stmt->execute();

echo "<p style='color:green'>Veículo cadastrado!</p>";

}

// =====================
// ATUALIZAR VEICULO
// =====================

if(isset($_POST['atualizar'])){

$id = $_POST['id'];

$cliente_id = $_POST['cliente_id'];
$marca  = $_POST['marca'];
$modelo = $_POST['modelo'];
$ano    = $_POST['ano'];
$placa  = strtoupper($_POST['placa']);
$chassi = $_POST['chassi'];

$stmt = $conn->prepare("
UPDATE veiculos
SET cliente_id=?, marca=?, modelo=?, ano=?, placa=?, chassi=?
WHERE id=?
");

$stmt->bind_param("isssssi",
$cliente_id,
$marca,
$modelo,
$ano,
$placa,
$chassi,
$id
);

$stmt->execute();

echo "<p style='color:green'>Veículo atualizado!</p>";

}

// =====================
// EXCLUIR VEICULO
// =====================

if(isset($_GET['excluir'])){

$id = intval($_GET['excluir']);

$conn->query("DELETE FROM veiculos WHERE id=$id");

echo "<p style='color:red'>Veículo excluído!</p>";

}

?>

<form method="POST">

<?php if($editar){ ?>

<input type="hidden" name="id" value="<?php echo $veiculo['id']; ?>">

<?php } ?>

Cliente:<br>

<select name="cliente_id" required>

<option value="">Selecione</option>

<?php

$clientes->data_seek(0);

while($c = $clientes->fetch_assoc()){

$sel = ($editar && $veiculo['cliente_id']==$c['id']) ? "selected" : "";

echo "<option value='{$c['id']}' $sel>{$c['nome']}</option>";

}

?>

</select>

<br><br>


Marca:<br>

<select name="marca" id="marca" onchange="carregarModelos()" required>

<option value="">Selecione</option>
<option value="fiat">Fiat</option>
<option value="volkswagen">Volkswagen</option>
<option value="chevrolet">Chevrolet</option>
<option value="ford">Ford</option>
<option value="toyota">Toyota</option>
<option value="honda">Honda</option>
<option value="hyundai">Hyundai</option>

</select>

<br><br>

Modelo:<br>

<select name="modelo" id="modelo" required>

<option value="">Selecione a marca primeiro</option>

</select>

<br><br>


Ano de Fabricação:<br>

<select name="ano" required>

<?php
for($i=date("Y"); $i>=1950; $i--){
echo "<option value='$i'>$i</option>";
}
?>

</select>

<br><br>


Placa:<br>

<input type="text" name="placa"
value="<?php echo $editar ? $veiculo['placa'] : ''; ?>" required>

<br><br>


Chassi:<br>

<input type="text" name="chassi"
value="<?php echo $editar ? $veiculo['chassi'] : ''; ?>">

<br><br>

<?php if($editar){ ?>

<input type="submit" name="atualizar" value="Atualizar Veículo">

<?php }else{ ?>

<input type="submit" name="salvar" value="Cadastrar Veículo">

<?php } ?>

</form>

<hr>

<h3>Lista de Veículos</h3>

<table border="1" cellpadding="8" width="100%">

<tr>
<th>ID</th>
<th>Marca</th>
<th>Modelo</th>
<th>Ano</th>
<th>Placa</th>
<th>Cliente</th>
<th>Ações</th>
</tr>

<?php

$lista = $conn->query("
SELECT v.*, c.nome
FROM veiculos v
JOIN clientes c ON v.cliente_id = c.id
ORDER BY v.id DESC
");

while($v = $lista->fetch_assoc()){

echo "<tr>";

echo "<td>".$v['id']."</td>";

echo "<td>".$v['marca']."</td>";

echo "<td>".$v['modelo']."</td>";

echo "<td>".$v['ano']."</td>";

echo "<td>".$v['placa']."</td>";

echo "<td>".$v['nome']."</td>";

echo "<td>

<a href='?area=veiculos&editar=".$v['id']."'>Editar</a> |

<a href='?area=veiculos&excluir=".$v['id']."'
onclick=\"return confirm('Excluir veículo?')\">Excluir</a>

</td>";

echo "</tr>";

}

?>

</table>

<script>

function carregarModelos(){

var marca = document.getElementById("marca").value;
var modelo = document.getElementById("modelo");

var modelos = {

fiat:["Uno","Palio","Argo","Cronos","Strada","Mobi","Toro"],

volkswagen:["Gol","Polo","Virtus","Saveiro","Nivus","T-Cross","Amarok","Fusca"],

chevrolet:["Onix","Prisma","Cruze","Tracker","S10","Spin","Monza","Cobalt", "Classic","Prisma", "Vectra (Elegance, Expression)", "Astra","Celta", "Corsa (incluindo Maxx, Premium, Sedan)","Montana", "S-10", "Blazer", "Captiva","Agile"],

ford:["Ka","Fiesta","Focus","Ranger","EcoSport"],

toyota:["Corolla","Yaris","Hilux","Etios","SW4"],

honda:["Civic","City","Fit","HR-V"],

hyundai:["HB20","Creta","Tucson","Santa Fe"]

};

modelo.innerHTML = "<option value=''>Selecione</option>";

if(modelos[marca]){

modelos[marca].forEach(function(m){

var option = document.createElement("option");
option.value = m;
option.text = m;

modelo.appendChild(option);

});

}


}

// FORMATA PLACA AUTOMATICAMENTE
document.getElementById("placa").addEventListener("input", function(){

this.value = this.value.toUpperCase().replace(/[^A-Z0-9]/g,'');

});

</script>