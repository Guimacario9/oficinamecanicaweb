<?php
require_once(__DIR__ . '/fpdf/fpdf.php');

$conn = new mysqli("if0_41221516","sql212.infinityfree.com","eMUzZeSUhPF","if0_41221516_db_oficinamecanica");

if ($conn->connect_error) {
    die("Erro de conexão: " . $conn->connect_error);
}

/* ===============================
   FUNÇÃO PARA CORRIGIR ACENTOS
=================================*/
function formatar($texto){
    return utf8_decode($texto);
}

/* ===============================
   VALIDAR ID
=================================*/
if(!isset($_GET['id']) || empty($_GET['id'])){
    die("ID não informado.");
}

$id = intval($_GET['id']);

/* ===============================
   BUSCAR ORDEM
=================================*/
$sql = "
SELECT 
    os.*,
    v.modelo,
    v.marca,
    v.placa,
    c.nome AS cliente_nome
FROM ordem_servico os
INNER JOIN veiculos v ON os.veiculo_id = v.id
INNER JOIN clientes c ON v.cliente_id = c.id
WHERE os.id = $id
";

$result = $conn->query($sql);

if(!$result || $result->num_rows == 0){
    die("Ordem não encontrada.");
}

$os = $result->fetch_assoc();

/* ===============================
   BUSCAR PAGAMENTOS
=================================*/
$sqlPag = "SELECT * FROM pagamentos WHERE ordem_id = $id";
$resultPag = $conn->query($sqlPag);

$totalPago = 0;

$pagamentos = array();

if($resultPag){
    while($p = $resultPag->fetch_assoc()){
        $pagamentos[] = $p;
        $totalPago += $p['valor'];
    }
}

/* ===============================
   CÁLCULOS
=================================*/
$desconto = isset($os['desconto']) ? $os['desconto'] : 0;

$valorFinal = $os['valor'] - $desconto;
$saldo = $valorFinal - $totalPago;

/* ===============================
   GERAR PDF
=================================*/
$pdf = new FPDF();
$pdf->AddPage();

/* TÍTULO */
$pdf->SetFont('Arial','B',16);
$pdf->Cell(0,10,formatar('ORDEM DE SERVIÇO Nº '.$os['id']),0,1,'C');

$pdf->Ln(5);

/* DADOS DO CLIENTE */
$pdf->SetFont('Arial','B',12);
$pdf->Cell(0,8,formatar('Cliente'),0,1);

$pdf->SetFont('Arial','',12);
$pdf->Cell(0,8,formatar($os['cliente_nome']),0,1);
$pdf->Cell(0,8,formatar('Veículo: '.$os['marca'].' '.$os['modelo'].' - '.$os['placa']),0,1);

$pdf->Ln(5);

/* DADOS DA OS */
$pdf->SetFont('Arial','B',12);
$pdf->Cell(0,8,formatar('Detalhes da Ordem'),0,1);

$pdf->SetFont('Arial','',12);
$pdf->Cell(0,8,formatar('Tipo: '.$os['tipo_manutencao']),0,1);
$pdf->Cell(0,8,formatar('Status: '.$os['status']),0,1);
$pdf->Cell(0,8,formatar('Valor: R$ '.number_format($os['valor'],2,',','.')),0,1);
$pdf->Cell(0,8,formatar('Desconto: R$ '.number_format($desconto,2,',','.')),0,1);
$pdf->Cell(0,8,formatar('Valor Final: R$ '.number_format($valorFinal,2,',','.')),0,1);

$pdf->Ln(4);

$pdf->MultiCell(0,8,formatar('Descrição: '.$os['descricao']));
$pdf->Ln(3);
$pdf->MultiCell(0,8,formatar('Peças Utilizadas: '.$os['pecas_utilizadas']));

$pdf->Ln(6);

/* PAGAMENTOS */
$pdf->SetFont('Arial','B',12);
$pdf->Cell(0,8,formatar('Pagamentos'),0,1);

$pdf->SetFont('Arial','',12);

if(count($pagamentos) > 0){

    for($i = 0; $i < count($pagamentos); $i++){

        $data = date('d/m/Y', strtotime($pagamentos[$i]['data_pagamento']));
        $forma = $pagamentos[$i]['forma_pagamento'];
        $valor = number_format($pagamentos[$i]['valor'],2,',','.');

        $pdf->Cell(0,8,formatar($data.' - '.$forma.' - R$ '.$valor),0,1);
    }

}else{

    $pdf->Cell(0,8,formatar('Nenhum pagamento registrado.'),0,1);
}

$pdf->Ln(5);

/* RESUMO */
$pdf->SetFont('Arial','B',12);
$pdf->Cell(0,8,formatar('Resumo Financeiro'),0,1);

$pdf->SetFont('Arial','',12);
$pdf->Cell(0,8,formatar('Total Pago: R$ '.number_format($totalPago,2,',','.')),0,1);
$pdf->Cell(0,8,formatar('Saldo Restante: R$ '.number_format($saldo,2,',','.')),0,1);

$pdf->Ln(15);

/* ASSINATURA */
$pdf->Cell(0,8,formatar('________________________________________'),0,1,'C');
$pdf->Cell(0,8,formatar('Assinatura do Cliente'),0,1,'C');

$pdf->Output();
?>