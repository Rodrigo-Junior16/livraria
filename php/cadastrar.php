<?php
require 'conexao.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $mensagem = $_POST['mensagem'];

    $sql = "INSERT INTO mensagens_contato (nome, email, mensagem) VALUES (:nome, :email, :mensagem)";
    $stmt = $conexao->prepare($sql);
    $stmt->execute(['nome' => $nome, 'email' => $email, 'mensagem' => $mensagem]);

    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <title>Nova Mensagem</title>
  <link rel="stylesheet" href="../css/style.css">
</head>
<body>
  <main>
    <section class="secao">
      <h2>Cadastrar Nova Mensagem</h2>
      <form method="POST" class="form-contato">
        <div class="campo-group">
          <label>Nome:</label>
          <input type="text" name="nome" required>
        </div>
        <div class="campo-group">
          <label>E-mail:</label>
          <input type="email" name="email" required>
        </div>
        <div class="campo-group">
          <label>Mensagem:</label>
          <textarea name="mensagem" rows="4" required></textarea>
        </div>
        <button type="submit">Salvar</button>
        <a href="index.php" class="btn" style="background: #7f8c8d;">Cancelar</a>
      </form>
    </section>
  </main>
</body>
</html>