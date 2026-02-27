<h2 class="title">Relatório Financeiro</h2>

<?php
// CONEXÃO SEPARADA
require_once __DIR__ . "/config/conexao.php";

// ===============================
// FILTRO POR DATA
// ===============================
$data_inicio = isset($_GET['data_inicio']) ? $_GET['data_inicio'] : '';
$data_fim    = isset($_GET['data_fim']) ? $_GET['data_fim'] : '';

$where = "";

if($data_inicio != '' && $data_fim != ''){
    $where = " WHERE os.data_abertura BETWEEN '$data_inicio' AND '$data_fim' ";
}
?>

<form method="GET">
<input type="hidden" name="area" value="relatorio">

Data Início:
<input type="date" name="data_inicio" value="<?php echo $data_inicio; ?>">

Data Fim:
<input type="date" name="data_fim" value="<?php echo $data_fim; ?>">

<input type="submit" value="Filtrar">
</form>

<hr>

<?php
// ===============================
// TOTAL DE ORDENS
// ===============================
$sql_total = "
    SELECT COUNT(*) AS total
    FROM ordem_servico os
    $where
";

$result_total = $conn->query($sql_total);
$row_total = $result_total->fetch_assoc();
$total_os = isset($row_total['total']) ? $row_total['total'] : 0;


// ===============================
// TOTAL FATURADO
// ===============================
$sql_faturado = "
    SELECT SUM(os.valor) AS total
    FROM ordem_servico os
    $where
";

$result_faturado = $conn->query($sql_faturado);
$row_faturado = $result_faturado->fetch_assoc();
$faturado = isset($row_faturado['total']) ? $row_faturado['total'] : 0;

if(!$faturado){
    $faturado = 0;
}


// ===============================
// TOTAL RECEBIDO
// ===============================
$sql_recebido = "
    SELECT SUM(p.valor) AS total
    FROM pagamentos p
    INNER JOIN ordem_servico os ON os.id = p.ordem_id
    $where
";

$result_recebido = $conn->query($sql_recebido);

if($result_recebido){
    $row_recebido = $result_recebido->fetch_assoc();
    $recebido = isset($row_recebido['total']) ? $row_recebido['total'] : 0;
}else{
    $recebido = 0;
}

if(!$recebido){
    $recebido = 0;
}


// ===============================
// SALDO
// ===============================
$saldo = $faturado - $recebido;
?>

<div style="padding:15px; border:1px solid #ccc; margin-bottom:20px; background:#f9f9f9;">

<p><strong>Total de Ordens:</strong> <?php echo $total_os; ?></p>

<p><strong>Total Faturado:</strong> 
R$ <?php echo number_format($faturado,2,',','.'); ?>
</p>

<p><strong>Total Recebido:</strong> 
R$ <?php echo number_format($recebido,2,',','.'); ?>
</p>

<p><strong>Saldo em Aberto:</strong> 
R$ <?php echo number_format($saldo,2,',','.'); ?>
</p>

</div>

<hr>

<h3>Lista de Ordens</h3>

<?php

$sql_lista = "
    SELECT os.*, v.modelo, v.placa
    FROM ordem_servico os
    INNER JOIN veiculos v ON os.veiculo_id = v.id
    $where
    ORDER BY os.id DESC
";

$result_lista = $conn->query($sql_lista);

if($result_lista && $result_lista->num_rows > 0){

    while($os = $result_lista->fetch_assoc()){

        echo "<div style='margin-bottom:10px; border-bottom:1px solid #ddd; padding:5px;'>";

        echo "<strong>OS #".$os['id']."</strong> - ";
        echo $os['modelo']." - ".$os['placa']."<br>";
        echo "Valor: R$ ".number_format($os['valor'],2,',','.')."<br>";
        echo "Status: ".$os['status']."<br>";

        if(isset($os['data_abertura']) && $os['data_abertura'] != ''){
            echo "Abertura: ".date('d/m/Y', strtotime($os['data_abertura']))."<br>";
        }

        echo "</div>";
    }

}else{
    echo "<p>Nenhuma ordem encontrada.</p>";
}

?>