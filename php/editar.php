<?php
require 'conexao.php';

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$id = $_GET['id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $mensagem = $_POST['mensagem'];

    $sql = "UPDATE mensagens_contato SET nome = :nome, email = :email, mensagem = :mensagem WHERE id = :id";
    $stmt = $conexao->prepare($sql);
    $stmt->execute(['nome' => $nome, 'email' => $email, 'mensagem' => $mensagem, 'id' => $id]);

    header("Location: index.php");
    exit;
}

$sql = "SELECT * FROM mensagens_contato WHERE id = :id";
$stmt = $conexao->prepare($sql);
$stmt->execute(['id' => $id]);
$msg = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <title>Editar Mensagem</title>
  <link rel="stylesheet" href="../css/style.css">
</head>
<body>
  <main>
    <section class="secao">
      <h2>Editar Registro #<?= $msg['id'] ?></h2>
      <form method="POST" class="form-contato">
        <div class="campo-group">
          <label>Nome:</label>
          <input type="text" name="nome" value="<?= htmlspecialchars($msg['nome']) ?>" required>
        </div>
        <div class="campo-group">
          <label>E-mail:</label>
          <input type="email" name="email" value="<?= htmlspecialchars($msg['email']) ?>" required>
        </div>
        <div class="campo-group">
          <label>Mensagem:</label>
          <textarea name="mensagem" rows="4" required><?= htmlspecialchars($msg['mensagem']) ?></textarea>
        </div>
        <button type="submit">Atualizar</button>
        <a href="index.php" class="btn" style="background: #7f8c8d;">Cancelar</a>
      </form>
    </section>
  </main>
</body>
</html>