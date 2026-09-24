<?php
require_once __DIR__ . "/config/conexao.php";

$resultado = $conn->query("
SELECT p.*, c.nome 
FROM pesquisa_satisfacao p
INNER JOIN clientes c ON p.cliente_id = c.id
ORDER BY p.id DESC
");

$media = $conn->query("SELECT AVG(nota) AS media FROM pesquisa_satisfacao");
$mediaFinal = $media->fetch_assoc()['media'];
?>

<h2>Relatório de Satisfação</h2>

<p><strong>Média Geral:</strong> 
<?php echo number_format($mediaFinal,1); ?> ⭐</p>

<hr>

<table border="1" cellpadding="6">
<tr>
<th>Cliente</th>
<th>Nota</th>
<th>Comentário</th>
<th>Data</th>
</tr>

<?php while($r = $resultado->fetch_assoc()): ?>
<tr>
<td><?php echo htmlspecialchars($r['nome']); ?></td>
<td><?php echo $r['nota']; ?> ⭐</td>
<td><?php echo htmlspecialchars($r['comentario']); ?></td>
<td><?php echo $r['data_resposta']; ?></td>
</tr>
<?php endwhile; ?>

</table>