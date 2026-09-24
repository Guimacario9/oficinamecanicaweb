<?php
require('fpdf/fpdf.php');

date_default_timezone_set('America/Sao_Paulo');

// ================= CONEXÃO =================
$conn = new mysqli(
"sql212.infinityfree.com",
"if0_41221516",
"eMUzZeSUhPF",
"if0_41221516_db_oficinamecanica"
);

if ($conn->connect_error) {
    die("Erro de conexão: " . $conn->connect_error);
}

$conn->set_charset("utf8");

// ================= PEGAR ID =================
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id <= 0) {
    die("ID inválido");
}

// ================= CONSULTA =================
$sql = "
SELECT os.*, 
c.nome,c.telefone,c.cpf_cnpj,c.endereco,
v.marca,v.modelo,v.placa,v.ano,v.chassi
FROM ordem_servico os
JOIN veiculos v ON os.veiculo_id = v.id
JOIN clientes c ON v.cliente_id = c.id
WHERE os.id = ?
";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    die("Erro no prepare: " . $conn->error);
}

$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    die("Ordem de serviço não encontrada.");
}

$dados = $result->fetch_assoc();

// ================= CALCULO =================
$desconto = isset($dados['desconto']) ? $dados['desconto'] : 0;
$total = $dados['valor'] - $desconto;

// ================= PDF =================
$pdf = new FPDF();
$pdf->AddPage();

// Borda
$pdf->Rect(5,5,200,287);

// ================= CABEÇALHO =================
$pdf->SetFont('Arial','B',16);
$pdf->Cell(0,10,'DUH GARAGE - ORDEM DE SERVICO',0,1,'C');

$pdf->SetFont('Arial','',10);
$pdf->Cell(0,5,'Av. Patativas, 117 - Pirajui / SP',0,1,'C');
$pdf->Cell(0,5,'Tel: (14) 99685-2568 | Email: edu631079@gmail.com',0,1,'C');

$pdf->Ln(4);
$pdf->Line(10,$pdf->GetY(),200,$pdf->GetY());
$pdf->Ln(5);

// ================= CLIENTE =================
$pdf->SetFont('Arial','B',12);
$pdf->Cell(0,6,'DADOS DO CLIENTE',0,1);

$pdf->SetFont('Arial','',10);
$pdf->Cell(0,6,'Nome: '.$dados['nome'],0,1);
$pdf->Cell(0,6,'Telefone: '.$dados['telefone'],0,1);
$pdf->Cell(0,6,'Documento: '.$dados['cpf_cnpj'],0,1);
$pdf->Cell(0,6,'Endereco: '.$dados['endereco'],0,1);

$pdf->Ln(3);
$pdf->Line(10,$pdf->GetY(),200,$pdf->GetY());
$pdf->Ln(5);

// ================= VEÍCULO =================
$pdf->SetFont('Arial','B',12);
$pdf->Cell(0,6,'DADOS DO VEICULO',0,1);

$pdf->SetFont('Arial','',10);
$pdf->Cell(0,6,'Veiculo: '.$dados['marca'].' '.$dados['modelo'].' ('.$dados['ano'].')',0,1);
$pdf->Cell(0,6,'Placa: '.$dados['placa'],0,1);
$pdf->Cell(0,6,'Chassi: '.$dados['chassi'],0,1);

$pdf->Ln(3);
$pdf->Line(10,$pdf->GetY(),200,$pdf->GetY());
$pdf->Ln(5);

// ================= ORDEM =================
$pdf->SetFont('Arial','B',12);
$pdf->Cell(0,6,'DETALHES DO SERVICO',0,1);

$pdf->SetFont('Arial','',10);
$pdf->Cell(0,6,'Numero OS: '.$dados['id'],0,1);
$pdf->Cell(0,6,'Tipo Manutencao: '.$dados['tipo_manutencao'],0,1);
$pdf->Cell(0,6,'Data Abertura: '.date('d/m/Y',strtotime($dados['data_abertura'])),0,1);

if (!empty($dados['data_prevista'])) {
    $pdf->Cell(0,6,'Data prevista: '.date('d/m/Y',strtotime($dados['data_prevista'])),0,1);
}

// Status colorido
$status = $dados['status'];

if ($status == "Paga") {
    $pdf->SetTextColor(0,150,0);
} else {
    $pdf->SetTextColor(200,0,0);
}

$pdf->Cell(0,6,'Status: '.$status,0,1);
$pdf->SetTextColor(0,0,0);

$pdf->Ln(3);

// ================= TABELA =================
$pdf->SetFont('Arial','B',10);
$pdf->Cell(70,8,'Servico',1,0,'C');
$pdf->Cell(60,8,'Pecas Utilizadas',1,0,'C');
$pdf->Cell(30,8,'Valor',1,0,'C');
$pdf->Cell(30,8,'Pagamento',1,1,'C');

$pdf->SetFont('Arial','',10);

$pagamento = !empty($dados['forma_pagamento']) 
    ? $dados['forma_pagamento'] 
    : "Pendente";

$yInicial = $pdf->GetY();

// Serviço
$pdf->MultiCell(70,8,$dados['descricao'],1);
$yFinal = $pdf->GetY();

// Peças
$pdf->SetXY(80,$yInicial);
$pdf->MultiCell(60,8,$dados['pecas_utilizadas'],1);

// Altura automática
$altura = $yFinal - $yInicial;

// Valor
$pdf->SetXY(140,$yInicial);
$pdf->Cell(30,$altura,'R$ '.number_format($dados['valor'],2,',','.'),1,0,'C');

// Pagamento
$pdf->SetXY(170,$yInicial);
$pdf->Cell(30,$altura,$pagamento,1,1,'C');

$pdf->Ln(5);

// ================= DESCONTO =================
$pdf->SetFont('Arial','B',11);
$pdf->Cell(0,6,'Desconto: R$ '.number_format($desconto,2,',','.'),0,1,'R');

// ================= TOTAL =================
$pdf->SetFont('Arial','B',14);
$pdf->Cell(0,8,'VALOR TOTAL: R$ '.number_format($total,2,',','.'),0,1,'R');

// ================= OBSERVAÇÕES =================
if (!empty($dados['observacoes'])) {
    $pdf->Ln(5);
    $pdf->SetFont('Arial','B',11);
    $pdf->Cell(0,6,'Observacoes:',0,1);

    $pdf->SetFont('Arial','',10);
    $pdf->MultiCell(0,6,$dados['observacoes']);
}

$pdf->Ln(10);

// ================= ASSINATURAS =================
$pdf->Cell(80,6,'_________________________',0,0,'C');
$pdf->Cell(30);
$pdf->Cell(80,6,'_________________________',0,1,'C');

$pdf->Cell(80,6,'Cliente',0,0,'C');
$pdf->Cell(30);
$pdf->Cell(80,6,'Responsavel',0,1,'C');

// ================= DATA =================
$pdf->SetY(260);
$pdf->SetFont('Arial','',9);
$pdf->Cell(0,5,'Data impressao: '.date('d/m/Y H:i'),0,1);

// ================= SAÍDA =================
$pdf->Output();
?>