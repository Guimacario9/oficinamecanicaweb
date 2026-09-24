<?php
require_once __DIR__ . "/config/conexao.php";

$os_id = intval($_GET['os'] ?? 0);

if(isset($_POST['enviar'])){

    $os_id = intval($_POST['os_id']);
    $cliente_id = intval($_POST['cliente_id']);
    $nota = intval($_POST['nota']);
    $comentario = trim($_POST['comentario']);

    if($nota >= 1 && $nota <= 5){

        $stmt = $conn->prepare(
            "INSERT INTO pesquisa_satisfacao 
             (os_id, cliente_id, nota, comentario)
             VALUES (?, ?, ?, ?)"
        );

        $stmt->bind_param("iiis", $os_id, $cliente_id, $nota, $comentario);
        $stmt->execute();

        echo "<h2>Obrigado pela sua avaliação! ⭐</h2>";
        exit;
    }
}

// Buscar dados da OS
$stmt = $conn->prepare("
    SELECT os.id, c.id AS cliente_id, c.nome 
    FROM ordem_servico os
    INNER JOIN veiculos v ON os.veiculo_id = v.id
    INNER JOIN clientes c ON v.cliente_id = c.id
    WHERE os.id = ?
");

$stmt->bind_param("i", $os_id);
$stmt->execute();
$res = $stmt->get_result();
$dados = $res->fetch_assoc();

if(!$dados){
    die("Ordem não encontrada.");
}
?>

<h2>Pesquisa de Satisfação</h2>

<p>Olá <strong><?php echo htmlspecialchars($dados['nome']); ?></strong></p>

<form method="POST">

<input type="hidden" name="os_id" value="<?php echo $os_id; ?>">
<input type="hidden" name="cliente_id" value="<?php echo $dados['cliente_id']; ?>">

<label>Como você avalia nosso atendimento?</label><br><br>

<select name="nota" required>
    <option value="">Selecione</option>
    <option value="5">⭐⭐⭐⭐⭐ Excelente</option>
    <option value="4">⭐⭐⭐⭐ Muito Bom</option>
    <option value="3">⭐⭐⭐ Bom</option>
    <option value="2">⭐⭐ Regular</option>
    <option value="1">⭐ Ruim</option>
</select>

<br><br>

<label>Comentário (opcional)</label><br>
<textarea name="comentario" rows="4" cols="40"></textarea>

<br><br>

<input type="submit" name="enviar" value="Enviar Avaliação">

</form>