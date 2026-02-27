<h2 class="title">Ordem de Serviço</h2>

<?php
// CONEXÃO SEPARADA
require_once __DIR__ . "/config/conexao.php";

// =========================
// CRIAR OS
// =========================
if(isset($_POST['salvar'])){

    $veiculo_id = isset($_POST['veiculo_id']) ? intval($_POST['veiculo_id']) : 0;
    $descricao  = isset($_POST['descricao']) ? trim($_POST['descricao']) : '';
    $tipo       = isset($_POST['tipo_manutencao']) ? $_POST['tipo_manutencao'] : '';
    $pecas      = isset($_POST['pecas']) ? $_POST['pecas'] : '';
    $valor      = isset($_POST['valor']) ? str_replace(",", ".", $_POST['valor']) : 0;
    $data_prev  = isset($_POST['data_prevista']) ? $_POST['data_prevista'] : null;

    if($veiculo_id > 0 && $descricao != '' && $valor > 0){

        $stmt = $conn->prepare("
            INSERT INTO ordem_servico 
            (veiculo_id, descricao, tipo_manutencao, pecas_utilizadas, valor, status, data_abertura, data_prevista)
            VALUES (?, ?, ?, ?, ?, 'Aberta', NOW(), ?)
        ");

        $stmt->bind_param("isssds",
            $veiculo_id,
            $descricao,
            $tipo,
            $pecas,
            $valor,
            $data_prev
        );

        if($stmt->execute()){
            echo "<p style='color:green;'>Ordem criada com sucesso!</p>";
        } else {
            echo "<p style='color:red;'>Erro ao criar ordem.</p>";
        }

        $stmt->close();

    } else {
        echo "<p style='color:red;'>Preencha os campos obrigatórios.</p>";
    }
}

// =========================
// BUSCAR VEÍCULOS
// =========================
$veiculos = $conn->query("
    SELECT v.id, v.modelo, v.placa, c.nome 
    FROM veiculos v
    JOIN clientes c ON v.cliente_id = c.id
");
?>

<form method="POST">

Veículo:<br>
<select name="veiculo_id" required>
<option value="">Selecione</option>
<?php while($v = $veiculos->fetch_assoc()): ?>
<option value="<?php echo $v['id']; ?>">
<?php echo $v['modelo']." - ".$v['placa']." (".$v['nome'].")"; ?>
</option>
<?php endwhile; ?>
</select><br><br>

Tipo de Manutenção:<br>
<select name="tipo_manutencao" required>
<option value="Preventiva">Preventiva</option>
<option value="Corretiva">Corretiva</option>
<option value="Revisão">Revisão</option>
<option value="Troca de Peças">Troca de Peças</option>
</select><br><br>

Descrição:<br>
<textarea name="descricao" required></textarea><br><br>

Peças Utilizadas:<br>
<textarea name="pecas"></textarea><br><br>

Valor:<br>
<input type="text" name="valor" required><br><br>

Data Prevista de Entrega:<br>
<input type="date" name="data_prevista" required><br><br>

<input type="submit" name="salvar" value="Criar OS">

</form>

<hr>

<h3>Ordens em Aberto</h3>

<?php

$sql_abertas = "
    SELECT os.*, v.modelo, v.placa
    FROM ordem_servico os
    INNER JOIN veiculos v ON os.veiculo_id = v.id
    WHERE os.status = 'Aberta'
    ORDER BY os.id DESC
";

$result_abertas = $conn->query($sql_abertas);

if($result_abertas && $result_abertas->num_rows > 0){

    while($os = $result_abertas->fetch_assoc()){

        echo "<div style='margin-bottom:10px; border-bottom:1px solid #ddd; padding:5px;'>";

        echo "<strong>OS #".$os['id']."</strong> - ";
        echo $os['modelo']." - ".$os['placa']."<br>";
        echo "Valor: R$ ".number_format($os['valor'],2,',','.')."<br>";
        echo "Status: ".$os['status']."<br>";

        if(isset($os['data_prevista'])){
            echo "Entrega prevista: ".date('d/m/Y', strtotime($os['data_prevista']))."<br>";
        }

        echo "</div>";
    }

}else{
    echo "<p>Nenhuma ordem em aberto.</p>";
}
?>

<hr>
<h3>Ordens Finalizadas / Pagas</h3>

<?php

$sql_finalizadas = "
    SELECT os.*, v.modelo, v.placa, p.forma_pagamento, p.data_pagamento
    FROM ordem_servico os
    INNER JOIN veiculos v ON os.veiculo_id = v.id
    LEFT JOIN pagamentos p ON os.id = p.ordem_id
    WHERE os.status = 'Finalizada'
    ORDER BY os.id DESC
";

$result_finalizadas = $conn->query($sql_finalizadas);

if($result_finalizadas && $result_finalizadas->num_rows > 0){

    while($os = $result_finalizadas->fetch_assoc()){

        echo "<div style='margin-bottom:10px; border-bottom:1px solid #ddd; padding:5px;'>";

        echo "<strong>OS #".$os['id']."</strong> - ";
        echo $os['modelo']." - ".$os['placa']."<br>";
        echo "Valor: R$ ".number_format($os['valor'],2,',','.')."<br>";

        if(isset($os['data_pagamento']) && $os['data_pagamento'] != ''){
            echo "Pago em: ".date('d/m/Y', strtotime($os['data_pagamento']))."<br>";
        }

        if(isset($os['forma_pagamento']) && $os['forma_pagamento'] != ''){
            echo "Forma de Pagamento: ".$os['forma_pagamento']."<br>";
        }

        echo "</div>";
    }

}else{
    echo "<p>Nenhuma ordem finalizada.</p>";
}
?>