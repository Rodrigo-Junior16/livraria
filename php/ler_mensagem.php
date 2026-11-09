<?php
require_once 'conexao.php';

try {
    $sql = "SELECT * FROM mensagens_contato ORDER BY data_envio DESC";
    $stmt = $conexao->prepare($sql);
    $stmt->execute();
    
    $mensagens = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo "<h1>Caixa de Entrada - Estante Virtual</h1>";
    echo "<a href='index.html'>Voltar para o Início</a><hr>";

    if (count($mensagens) > 0) {
        foreach ($mensagens as $msg) {
            $nomeSeguro = htmlspecialchars($msg['nome']);
            $emailSeguro = htmlspecialchars($msg['email']);
            $textoSeguro = htmlspecialchars($msg['mensagem']);
            
            echo "<div style='background: #fff; padding: 15px; border: 1px solid #ccc; margin-bottom: 10px;'>";
            echo "<p><strong>De:</strong> $nomeSeguro ($emailSeguro)</p>";
            echo "<p><strong>Mensagem:</strong><br> $textoSeguro</p>";
            echo "</div>";
        }
    } else {
        echo "<p>Nenhuma mensagem recebida ainda.</p>";
    }

} catch (PDOException $e) {
    echo "Erro ao buscar as mensagens: " . $e->getMessage();
}
?>