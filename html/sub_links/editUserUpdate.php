<?php
session_start();
include '../../server.php'; 
require_once '../../auth.php';  // ADICIONADO

// 1. VERIFICAR AUTENTICAÇÃO E PERMISSÃO DE ADM (ATUALIZADO)
if (!isLoggedIn() || !isAdmin()) {
    header('Location: ../../index.php?error=access_denied');
    exit;
}

// 2. VERIFICAR MÉTODO POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['bt_incluir'])) {
    header('Location: ../listagemAdm.php?error=invalid_access&page=1');
    exit;
}

// 3. VALIDAR CAMPOS OBRIGATÓRIOS
$required_fields = ['id', 'username', 'email', 'nome', 'sobrenome'];
foreach ($required_fields as $field) {
    if (!isset($_POST[$field]) || empty(trim($_POST[$field]))) {
        header('Location: ../listagemAdm.php?error=missing_fields&page=1');
        exit;
    }
}

// 4. SANITIZAR E VALIDAR DADOS
$id_usuario_edicao = (int) $_POST['id'];
$page_to_redirect = isset($_POST['list_page']) ? max(1, (int) $_POST['list_page']) : 1;

// Validar ID
if ($id_usuario_edicao <= 0) {
    header('Location: ../listagemAdm.php?error=invalid_id&page=1');
    exit;
}

// Sanitizar dados
$novo_username = trim($_POST['username']);
$novo_email = trim($_POST['email']);
$novo_nome = trim($_POST['nome']);
$novo_sobrenome = trim($_POST['sobrenome']);
$nova_senha = isset($_POST['senha']) ? trim($_POST['senha']) : '';

// Validações
if (!filter_var($novo_email, FILTER_VALIDATE_EMAIL)) {
    header("Location: ../listagemAdm.php?error=invalid_email&page={$page_to_redirect}");
    exit;
}

if (strlen($novo_username) < 3 || strlen($novo_username) > 50) {
    header("Location: ../listagemAdm.php?error=invalid_username&page={$page_to_redirect}");
    exit;
}

// 5. VERIFICAR SE EMAIL JÁ EXISTE (EXCETO PARA O PRÓPRIO USUÁRIO)
$check_email_query = "SELECT id FROM tb_register WHERE email = ? AND id != ?";
$stmt_check = $conecta_db->prepare($check_email_query);
$stmt_check->bind_param("si", $novo_email, $id_usuario_edicao);
$stmt_check->execute();
$result_check = $stmt_check->get_result();

if ($result_check->num_rows > 0) {
    $stmt_check->close();
    header("Location: ../listagemAdm.php?error=email_exists&page={$page_to_redirect}");
    exit;
}
$stmt_check->close();

// 6. CONSTRUIR E EXECUTAR QUERY COM PREPARED STATEMENT
$sql_update = "UPDATE tb_register SET username = ?, email = ?, nome = ?, sobrenome = ?";
$types = "ssss";
$params = [$novo_username, $novo_email, $novo_nome, $novo_sobrenome];

// Adicionar senha se fornecida
if (!empty($nova_senha)) {
    // USAR PASSWORD_HASH EM VEZ DE MD5 (SEGURANÇA)
    if (strlen($nova_senha) < 6) {
        header("Location: ../listagemAdm.php?error=weak_password&page={$page_to_redirect}");
        exit;
    }
    $hashed_senha = password_hash($nova_senha, PASSWORD_DEFAULT);
    $sql_update .= ", senha = ?";
    $types .= "s";
    $params[] = $hashed_senha;
}

$sql_update .= " WHERE id = ?";
$types .= "i";
$params[] = $id_usuario_edicao;

// 7. EXECUTAR UPDATE
$stmt_update = $conecta_db->prepare($sql_update);
if (!$stmt_update) {
    error_log("Erro na preparação da query: " . $conecta_db->error);
    header("Location: ../listagemAdm.php?update=failed&message=db_error&page={$page_to_redirect}");
    exit;
}

$stmt_update->bind_param($types, ...$params);

if ($stmt_update->execute()) {
    $stmt_update->close();
    // Registro de log (opcional)
    error_log("Admin {$_SESSION['username']} atualizou usuário ID {$id_usuario_edicao}");
    
    header("Location: ../listagemAdm.php?update=success&id={$id_usuario_edicao}&page={$page_to_redirect}");
    exit;
} else {
    $error_msg = $stmt_update->error;
    $stmt_update->close();
    
    error_log("Erro ao atualizar usuário: " . $error_msg);
    $error_message = urlencode("Erro ao atualizar dados");
    header("Location: ../listagemAdm.php?update=failed&message={$error_message}&page={$page_to_redirect}");
    exit;
}
?>