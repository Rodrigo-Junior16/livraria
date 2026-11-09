<?php
require_once 'conexao.php';

$mensagem_status = "";
$sucesso = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = $_POST['nome'] ?? '';
    $email = $_POST['email'] ?? '';
    $mensagem = $_POST['mensagem'] ?? '';

    try {
        $sql = "INSERT INTO mensagens_contato (nome, email, mensagem) VALUES (?, ?, ?)";
        $stmt = $conexao->prepare($sql);
        $stmt->execute([$nome, $email, $mensagem]);

        $sucesso = true;
        $mensagem_status = "Obrigado, " . htmlspecialchars($nome) . "! Sua mensagem foi enviada com sucesso.";
    } catch (PDOException $e) {
        $sucesso = false;
        $mensagem_status = "Ocorreu um erro ao salvar sua mensagem: " . $e->getMessage();
    }
} else {
    $sucesso = false;
    $mensagem_status = "Acesso inválido ao formulário.";
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Contato - Estante Virtual</title>
  <link rel="stylesheet" href="../css/style.css">
</head>
<body>

  <!-- Menu de Navegação -->
  <nav>
    <a href="../index.html">Início</a>
    <a href="../pages/explorar.html">Explorar</a>
    <a href="../pages/estante.html">Estante</a>
    <a href="index.php">Painel Admin</a>
  </nav>

  <main>
    <section class="secao" style="text-align: center; padding: 40px 20px;">
      <?php if ($sucesso): ?>
        <h1 style="color: #27ae60; margin-bottom: 10px;">✓ Mensagem Enviada!</h1>
        <p style="font-size: 1.1rem; margin-bottom: 25px; color: #555;"><?= $mensagem_status ?></p>
      <?php else: ?>
        <h1 style="color: #e74c3c; margin-bottom: 10px;">✕ Erro no Envio</h1>
        <p style="font-size: 1.1rem; margin-bottom: 25px; color: #555;"><?= $mensagem_status ?></p>
      <?php endif; ?>

      <a href="../index.html" class="btn">Voltar para a Página Inicial</a>
    </section>
  </main>

  <footer>
    <p>&copy; 2026 Estante Virtual</p>
  </footer>

</body>
</html>