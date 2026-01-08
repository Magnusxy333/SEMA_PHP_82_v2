<?php
session_start();
include 'server.php';

// Verifica se o usuário está logado
if (!isset($_SESSION['user_id'])) {
    header('Location: html/login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// Usando a conexão mysqli do server.php
if (isset($conn)) {
    // Deleta o usuário do banco de dados
    $user_id = mysqli_real_escape_string($conn, $user_id);
    $sql = "DELETE FROM tb_register WHERE id = '$user_id'";
    $result = mysqli_query($conn, $sql);
    
    if ($result) {
        // Destrói a sessão e redireciona para página de confirmação
        session_destroy();
        header('Location: deletedAccount.php');
        exit;
    } else {
        echo "Erro ao deletar a conta: " . mysqli_error($conn);
    }
} else {
    echo "Erro: Conexão com o banco de dados não estabelecida.";
}
?>