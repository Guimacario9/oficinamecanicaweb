<h2 class="title">Ordem de Serviço</h2>

<style>
.btn {
    padding: 10px 18px;
    border: none;
    border-radius: 6px;
    font-weight: bold;
    cursor: pointer;
    text-decoration: none;
    display: inline-block;
    background-color: #007BFF;
    color: #fff !important;
    transition: 0.3s;
}

.btn:hover {
    background-color: #0056b3;
}

.botoes {
    display: flex;
    gap: 10px;
    margin-top: 8px;
}

.box {
    border: 1px solid #ddd;
    padding: 10px;
    border-radius: 6px;
    margin-bottom: 10px;
}
</style>

<?php
// =========================
// CRIAR OS
// =========================
if(isset($_POST['salvar'])){

    $veiculo_id = $_POST['veiculo_id'];
    $descricao  = $_POST['descricao'];
    $tipo       = $_POST['tipo_manutencao'];
    $pecas      = $_POST['pecas'];
    $valor      = str_replace(",", ".", $_POST['valor']);
    $desconto   = str_replace(",", ".", $_POST['desconto']);
    $obs        = $_POST['observacoes'];
    $data_prev  = $_POST['data_prevista'];

    $stmt = $conn->prepare("
        INSERT INTO ordem_servico 
        (veiculo_id, descricao, tipo_manutencao, pecas_utilizadas, valor, desconto, observacoes, status, data_abertura, data_prevista)
        VALUES (?, ?, ?, ?, ?, ?, ?, 'Aberta', NOW(), ?)
    ");

    $stmt->bind_param("isssddss",
        $veiculo_id,
        $descricao,
        $tipo,
        $pecas,
        $valor,
        $desconto,
        $obs,
        $data_prev
    );

    $stmt->execute();

    echo "<p style='color:green;'>Ordem criada com sucesso!</p>";
}

// =========================
// VEÍCULOS
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

Desconto:<br>
<input type="text" name="desconto" value="0"><br><br>

Observações:<br>
<textarea name="observacoes"></textarea><br><br>

Data Prevista:<br>
<input type="date" name="data_prevista" required><br><br>

<input type="submit" name="salvar" value="Criar OS" class="btn">

</form>
<br />
<hr>

<h3>Ordens em Aberto</h3>

<?php
$abertas = $conn->query("
SELECT os.*, v.modelo, v.placa
FROM ordem_servico os
JOIN veiculos v ON os.veiculo_id = v.id
WHERE os.status='Aberta'
");

while($os = $abertas->fetch_assoc()):

$total = $os['valor'] - $os['desconto'];
?>

<div class="box">

<strong>OS #<?php echo $os['id']; ?></strong> - 
<?php echo $os['modelo']." - ".$os['placa']; ?><br>

<strong>Descrição:</strong><br>
<?php echo nl2br($os['descricao']); ?><br>

<strong>Peças:</strong><br>
<?php echo nl2br($os['pecas_utilizadas']); ?><br>

<strong>Valor:</strong> R$ <?php echo number_format($os['valor'],2,',','.'); ?><br>
<strong>Desconto:</strong> R$ <?php echo number_format($os['desconto'],2,',','.'); ?><br>
<strong>Total:</strong> R$ <?php echo number_format($total,2,',','.'); ?><br>

<strong>Entrega:</strong> <?php echo $os['data_prevista']; ?><br>

<?php if(!empty($os['observacoes'])): ?>
<strong>Observações:</strong><br>
<?php echo nl2br($os['observacoes']); ?><br>
<?php endif; ?>

<div class="botoes">

<a class="btn" href="?area=pagamento&pagar=<?php echo $os['id']; ?>">
Pagar
</a>

<a class="btn" href="pdf_os.php?id=<?php echo $os['id']; ?>" target="_blank">
Imprimir
</a>

</div>

</div>

<?php endwhile; ?>

<hr>

<h3>Ordens Finalizadas</h3>

<?php
$finalizadas = $conn->query("
SELECT os.*, v.modelo, v.placa
FROM ordem_servico os
JOIN veiculos v ON os.veiculo_id = v.id
WHERE os.status='Finalizada'
");

while($os = $finalizadas->fetch_assoc()):

$total = $os['valor'] - $os['desconto'];
?>

<div class="box">

<strong>OS #<?php echo $os['id']; ?></strong> - 
<?php echo $os['modelo']." - ".$os['placa']; ?><br>

<strong>Descrição:</strong><br>
<?php echo nl2br($os['descricao']); ?><br>

<strong>Total Pago:</strong> R$ <?php echo number_format($total,2,',','.'); ?><br>

<strong>Pago em:</strong> <?php echo $os['data_pagamento']; ?><br>
<strong>Forma:</strong> <?php echo $os['forma_pagamento']; ?><br>

<?php if(!empty($os['observacoes'])): ?>
<strong>Observações:</strong><br>
<?php echo nl2br($os['observacoes']); ?><br>
<?php endif; ?>

<div class="botoes">

<a class="btn" href="pdf_os.php?id=<?php echo $os['id']; ?>" target="_blank">
Imprimir
</a>

</div>

</div>

<?php endwhile; ?>