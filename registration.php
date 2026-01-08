<?php
include 'server.php';

$username = $_POST["txt_username"] ?? '';
$nome = $_POST["txt_nome"] ?? '';
$sobrenome = $_POST["txt_sobrenome"] ?? '';
$email = $_POST["txt_email"] ?? '';
$password = $_POST["txt_password"] ?? '';
$confirmPassword = $_POST["txt_confirmPassword"] ?? '';

// Verificar se username ou email já existem
$stmt_check = $conecta_db->prepare("SELECT id FROM tb_register WHERE username = ? OR email = ?");
$stmt_check->bind_param("ss", $username, $email);
$stmt_check->execute();
$result_check = $stmt_check->get_result();

if ($password !== $confirmPassword) {
    header('Location: html/fail_pages/registerFail.php');
    exit;
} elseif ($result_check->num_rows > 0) {
    header('Location: html/fail_pages/twoAccounts.php');
    exit;
} elseif (empty($username) || empty($email) || empty($nome) || empty($sobrenome) || empty($password)) {
    header('Location: html/fail_pages/accessDenied.php');
    exit;
} else {
    // VALORES PADRÃO PARA NOVOS USUÁRIOS
    $user_role = 'user'; // Todos novos usuários são 'user' por padrão
    $is_active = 1; // Conta ativa por padrão
    
    // Inserir novo usuário incluindo user_role e is_active
    $stmt_insert = $conecta_db->prepare("INSERT INTO tb_register (username, nome, sobrenome, email, senha, user_role, is_active) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt_insert->bind_param("ssssssi", $username, $nome, $sobrenome, $email, $password, $user_role, $is_active);
    
    if ($stmt_insert->execute()) {
        header('Location: html/login.php?success=1');
        exit;
    } else {
        header('Location: html/fail_pages/accessDenied.php');
        exit;
    }
}
?>