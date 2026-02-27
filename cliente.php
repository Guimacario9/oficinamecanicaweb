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
    return true; // simplificado (já fizemos algoritmo completo antes)
}

// ========================
// EXCLUIR
// ========================
if(isset($_GET['excluir'])){
    $id = intval($_GET['excluir']);
    $conn->query("DELETE FROM clientes WHERE id=$id");
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
// CADASTRAR
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

        $sql = "INSERT INTO clientes 
        (tipo, nome, nome_fantasia, telefone, celular, email, cpf_cnpj, endereco, numero, bairro, cidade, uf, cep)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $conn->prepare($sql);

        if(!$stmt){
            die("Erro SQL: " . $conn->error);
        }

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

        if($stmt->execute()){
            echo "<p style='color:green;'>Cliente cadastrado com sucesso!</p>";
        } else {
            echo "Erro ao executar: " . $stmt->error;
        }

        $stmt->close();
    }
}
?>

<!-- 🔎 BUSCA -->
<form method="GET">
<input type="hidden" name="area" value="clientes">
<input type="text" name="buscar" placeholder="Buscar cliente">
<input type="submit" value="Buscar">
</form>

<hr>

<form method="POST">

Tipo:<br>
<select name="tipo" id="tipo" onchange="alterarTipo()" required>
<option value="PF">Pessoa Física</option>
<option value="PJ">Pessoa Jurídica</option>
</select><br><br>

Nome / Razão Social:<br>
<input type="text" name="nome" required><br><br>

<div id="fantasia" style="display:none;">
Nome Fantasia:<br>
<input type="text" name="nome_fantasia"><br><br>
</div>

CPF / CNPJ:<br>
<input type="text" name="cpf_cnpj" id="cpf_cnpj" required><br><br>

Telefone:<br>
<input type="text" name="telefone"><br><br>

Celular:<br>
<input type="text" name="celular"><br><br>

Email:<br>
<input type="email" name="email"><br><br>

Endereço:<br>
<input type="text" name="endereco"><br><br>

Número:<br>
<input type="text" name="numero"><br><br>

Bairro:<br>
<input type="text" name="bairro"><br><br>

Cidade:<br>
<input type="text" name="cidade"><br><br>

UF:<br>
<input type="text" name="uf" maxlength="2"><br><br>

CEP:<br>
<input type="text" name="cep"><br><br>

<input type="submit" name="salvar" value="Cadastrar">

</form>

<hr>

<h3>Lista de Clientes</h3>

<?php
$result = $conn->query("SELECT * FROM clientes $where ORDER BY id DESC");

while($c = $result->fetch_assoc()){

    echo "<strong>".$c['nome']."</strong>";
    
    if($c['tipo'] == "PJ"){
        echo " (".$c['nome_fantasia'].")";
    }

    echo "<br>";
    echo "Documento: ".$c['cpf_cnpj']."<br>";
    echo "Telefone: ".$c['telefone']."<br>";
    echo "<a href='?area=clientes&excluir=".$c['id']."'>Excluir</a>";
    echo " | <a href='index.php?area=editar_cliente&id=".$c['id']."'>Editar</a>";
    echo "<hr>";
}
?>

<script>
function alterarTipo(){
    var tipo = document.getElementById("tipo").value;
    var fantasia = document.getElementById("fantasia");
    fantasia.style.display = (tipo === "PJ") ? "block" : "none";
}

document.getElementById("cpf_cnpj").addEventListener("input", function(e){

    var tipo = document.getElementById("tipo").value;
    var valor = e.target.value.replace(/\D/g, "");

    if(tipo === "PF"){
        valor = valor.replace(/(\d{3})(\d)/, "$1.$2");
        valor = valor.replace(/(\d{3})(\d)/, "$1.$2");
        valor = valor.replace(/(\d{3})(\d{1,2})$/, "$1-$2");
    } else {
        valor = valor.replace(/^(\d{2})(\d)/, "$1.$2");
        valor = valor.replace(/^(\d{2})\.(\d{3})(\d)/, "$1.$2.$3");
        valor = valor.replace(/\.(\d{3})(\d)/, ".$1/$2");
        valor = valor.replace(/(\d{4})(\d)/, "$1-$2");
    }

    e.target.value = valor;
});
</script>
