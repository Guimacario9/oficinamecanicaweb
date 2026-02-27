<?php
require __DIR__ . '/fpdf/fpdf.php';

// =============================
// CONEXÃO
// =============================
$conn = new mysqli("localhost","root","","oficinasoft");

if ($conn->connect_error) {
    die("Erro de conexão: " . $conn->connect_error);
}

// =============================
// RECEBER DATAS
// =============================
if(isset($_GET['data_inicio'])){
    $dataInicio = $_GET['data_inicio'];
}else{
    $dataInicio = date('Y-m-01');
}

if(isset($_GET['data_fim'])){
    $dataFim = $_GET['data_fim'];
}else{
    $dataFim = date('Y-m-d');
}

// =============================
// BUSCAR DADOS
// =============================
$sql = "
    SELECT id, valor, forma_pagamento, data_pagamento
    FROM ordem_servico
    WHERE status='Finalizada'
    AND data_pagamento BETWEEN '$dataInicio' AND '$dataFim'
    ORDER BY data_pagamento DESC
";

$result = $conn->query($sql);

if(!$result){
    die("Erro na consulta: " . $conn->error);
}

// =============================
// CALCULAR TOTAIS
// =============================
$total = 0;
$totalDinheiro = 0;
$totalCartao = 0;
$totalPix = 0;

$dados = array();

while($row = $result->fetch_assoc()){
    $dados[] = $row;
    $total += $row['valor'];

    if($row['forma_pagamento'] == 'Dinheiro'){
        $totalDinheiro += $row['valor'];
    }

    if($row['forma_pagamento'] == 'Cartao'){
        $totalCartao += $row['valor'];
    }

    if($row['forma_pagamento'] == 'Pix'){
        $totalPix += $row['valor'];
    }
}

// =============================
// CRIAR PDF
// =============================
$pdf = new FPDF();
$pdf->AddPage();

// Título
$pdf->SetFont('Arial','B',16);
$pdf->Cell(0,10,'RELATORIO FINANCEIRO',0,1,'C');

$pdf->SetFont('Arial','',11);
$pdf->Cell(0,8,'Periodo: '.date('d/m/Y',strtotime($dataInicio)).' ate '.date('d/m/Y',strtotime($dataFim)),0,1,'C');

$pdf->Ln(5);

// Cabeçalho tabela
$pdf->SetFont('Arial','B',10);
$pdf->Cell(20,8,'OS',1);
$pdf->Cell(40,8,'Valor',1);
$pdf->Cell(50,8,'Pagamento',1);
$pdf->Cell(40,8,'Data',1);
$pdf->Ln();

// Dados
$pdf->SetFont('Arial','',10);

foreach($dados as $os){

    $pdf->Cell(20,8,'#'.$os['id'],1);
    $pdf->Cell(40,8,'R$ '.number_format($os['valor'],2,',','.'),1);
    $pdf->Cell(50,8,$os['forma_pagamento'],1);
    $pdf->Cell(40,8,date('d/m/Y',strtotime($os['data_pagamento'])),1);
    $pdf->Ln();
}

$pdf->Ln(8);

// Totais
$pdf->SetFont('Arial','B',11);

$pdf->Cell(0,8,'TOTAL GERAL: R$ '.number_format($total,2,',','.'),0,1);
$pdf->Cell(0,8,'Dinheiro: R$ '.number_format($totalDinheiro,2,',','.'),0,1);
$pdf->Cell(0,8,'Cartao: R$ '.number_format($totalCartao,2,',','.'),0,1);
$pdf->Cell(0,8,'Pix: R$ '.number_format($totalPix,2,',','.'),0,1);

$pdf->Output();
?>
