<?php
require 'conexao.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    $sql = "DELETE FROM mensagens_contato WHERE id = :id";
    $stmt = $conexao->prepare($sql);
    $stmt->execute(['id' => $id]);
}

header("Location: index.php");
exit;
?>