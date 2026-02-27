<h2 class="title">Pagamento de Ordem de Serviço</h2>

<?php

if(isset($_GET['pagar'])){
    $id = $_GET['pagar'];

    $os = $conn->query("SELECT * FROM ordem_servico WHERE id=$id");
    $dados = $os->fetch_assoc();
?>

<h3>OS #<?php echo $dados['id']; ?></h3>
<p>Valor: R$ <?php echo number_format($dados['valor'],2,',','.'); ?></p>

<form method="POST">
<input type="hidden" name="id" value="<?php echo $dados['id']; ?>">

Forma de Pagamento:<br>
<select name="forma">
<option>Dinheiro</option>
<option>Cartão</option>
<option>Pix</option>
</select><br><br>

<input type="submit" name="confirmar" value="Confirmar Pagamento">
</form>

<?php
}

if(isset($_POST['confirmar'])){
    $id = $_POST['id'];
    $forma = $_POST['forma'];

    $conn->query("UPDATE ordem_servico 
                  SET status='Finalizada',
                      forma_pagamento='$forma',
                      data_pagamento=NOW()
                  WHERE id=$id");

    echo "<p style='color:green;'>Pagamento confirmado!</p>";
}
?>
