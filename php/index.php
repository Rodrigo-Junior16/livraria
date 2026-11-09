<?php
require 'conexao.php';

try {
    $sql = "SELECT * FROM mensagens_contato ORDER BY data_envio DESC";
    $stmt = $conexao->query($sql);
    $mensagens = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erro ao buscar mensagens: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Painel Admin - Estante Virtual</title>
  <link rel="stylesheet" href="../css/style.css">
</head>
<body>

  <!-- Menu de Navegação (Caminhos ajustados com ../) -->
  <nav>
    <a href="../index.html">Início</a>
    <a href="../pages/explorar.html">Explorar</a>
    <a href="../pages/estante.html">Estante</a>
    <a href="index.php">Painel Admin</a>
  </nav>

  <main>
    <section class="secao">
      <h1>Gerenciamento de Mensagens</h1>
      <p>Gerencie todas as mensagens recebidas pelo formulário do site:</p>
      <br>
      <a href="cadastrar.php" class="btn">+ Nova Mensagem Manual</a>
      <br><br>

      <table>
        <thead>
          <tr>
            <th>ID</th>
            <th>Nome</th>
            <th>E-mail</th>
            <th>Mensagem</th>
            <th>Data</th>
            <th>Ações</th>
          </tr>
        </thead>
        <tbody>
          <?php if (count($mensagens) > 0): ?>
            <?php foreach ($mensagens as $msg): ?>
            <tr>
              <td><?= $msg['id'] ?></td>
              <td><?= htmlspecialchars($msg['nome']) ?></td>
              <td><?= htmlspecialchars($msg['email']) ?></td>
              <td><?= htmlspecialchars($msg['mensagem']) ?></td>
              <td><?= date('d/m/Y H:i', strtotime($msg['data_envio'])) ?></td>
              <td>
                <a href="editar.php?id=<?= $msg['id'] ?>" class="btn-editar">Editar</a>
                <a href="excluir.php?id=<?= $msg['id'] ?>" class="btn-excluir" onclick="return confirm('Deseja apagar esta mensagem?')">Excluir</a>
              </td>
            </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr>
              <td colspan="6" style="text-align: center;">Nenhuma mensagem registrada.</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </section>
  </main>

  <footer>
    <p>&copy; 2026 Estante Virtual</p>
  </footer>

</body>
</html>