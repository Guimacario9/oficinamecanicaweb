<h2 class="title">Contato</h2>

<p>Entre em contato com a OficinaSoft:</p>

<ul>
    <li><strong>📍 Endereço:</strong> Rua das Oficinas, 123 - Centro</li>
    <li><strong>📞 Telefone:</strong> (11) 99999-9999</li>
    <li><strong>✉️ E-mail:</strong> contato@oficinasoft.com</li>
    <li><strong>🕒 Horário:</strong> Segunda a Sexta - 08h às 18h</li>
</ul>

<h3>Envie uma mensagem:</h3>

<form method="post">
    <p>
        Nome:<br>
        <input type="text" name="nome" required>
    </p>

    <p>
        E-mail:<br>
        <input type="email" name="email" required>
    </p>

    <p>
        Mensagem:<br>
        <textarea name="mensagem" rows="5" cols="40" required></textarea>
    </p>

    <p>
        <input type="submit" value="Enviar">
    </p>
</form>

<?php
if($_SERVER['REQUEST_METHOD'] == 'POST'){
    echo "<p><strong>Mensagem enviada com sucesso!</strong></p>";
}
?>
