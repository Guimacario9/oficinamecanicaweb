<?php

if (isset($_GET['pagar'])) {

    $id = (int)$_GET['pagar'];

    $stmt = $conn->prepare("SELECT * FROM ordem_servico WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();

    $resultado = $stmt->get_result();

    if ($resultado->num_rows == 0) {

        echo "<div class='alert alert-danger'>Ordem de Serviço não encontrada.</div>";

    } else {

        $dados = $resultado->fetch_assoc();
?>

<div class="card">

<h3>Pagamento da OS #<?php echo $dados['id']; ?></h3>

<p>
    <strong>Valor:</strong>
    R$ <?php echo number_format($dados['valor'], 2, ',', '.'); ?>
</p>

<form method="post">

    <input type="hidden" name="id" value="<?php echo $dados['id']; ?>">

    <label>Forma de Pagamento</label>

    <select name="forma" required>
        <option value="">Selecione...</option>
        <option value="Dinheiro">Dinheiro</option>
        <option value="Cartão">Cartão</option>
        <option value="Pix">Pix</option>
    </select>

    <br><br>

    <input
        type="submit"
        name="confirmar"
        value="Confirmar Pagamento"
        class="btn btn-success">

    <a href="index.php?area=os" class="btn btn-danger">
        Cancelar
    </a>

</form>

</div>

<?php
    }
}

if (isset($_POST['confirmar'])) {

    $id = (int)$_POST['id'];
    $forma = $_POST['forma'];

    $stmt = $conn->prepare("
        UPDATE ordem_servico
        SET
            status='Finalizada',
            forma_pagamento=?,
            data_pagamento=NOW()
        WHERE id=?
    ");

    $stmt->bind_param("si", $forma, $id);

    if ($stmt->execute()) {

        // Redireciona para Ordem de Serviço
        echo "<script>
                alert('Pagamento confirmado com sucesso!');
                window.location.href='index.php?area=os';
              </script>";
        exit;

    } else {

        echo "<div class='alert alert-danger'>
                Erro ao confirmar o pagamento.
              </div>";

    }

}
?>