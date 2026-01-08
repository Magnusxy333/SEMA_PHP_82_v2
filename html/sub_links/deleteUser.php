[file name]: deleteUser.php
[file content begin]
<?php
session_start();
include '../../server.php'; 
require_once '../../auth.php';  // ADICIONADO

// 1. VERIFICAR AUTENTICAÇÃO E PERMISSÃO DE ADM (ATUALIZADO)
if (!isLoggedIn() || !isAdmin()) {
    header('Location: ../../index.php?error=access_denied');
    exit;
}

// 2. VALIDAR ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: ../listagemAdm.php?error=no_id');
    exit;
}

$id_usuario_edicao = (int) $_GET['id'];
$page_to_redirect = isset($_GET['page']) ? max(1, (int) $_GET['page']) : 1;

if ($id_usuario_edicao <= 0) {
    header('Location: ../listagemAdm.php?error=invalid_id');
    exit;
}

// 3. CONSULTAR USERNAME COM PREPARED STATEMENT
$query_check = "SELECT username FROM tb_register WHERE id = ?";
$stmt_check = $conecta_db->prepare($query_check);

if (!$stmt_check) {
    header("Location: ../listagemAdm.php?delete=failed&message=db_error&page={$page_to_redirect}");
    exit;
}

$stmt_check->bind_param("i", $id_usuario_edicao);
$stmt_check->execute();
$result_check = $stmt_check->get_result();

if ($result_check->num_rows == 0) {
    $stmt_check->close();
    header("Location: ../listagemAdm.php?delete=failed&message=user_not_found&page={$page_to_redirect}");
    exit;
}

$user_info = $result_check->fetch_assoc();
$stmt_check->close();

// 4. IMPEDIR AUTO-EXCLUSÃO
if ($_SESSION['username'] === $user_info['username']) {
    header("Location: ../listagemAdm.php?error=self_delete&page={$page_to_redirect}");
    exit;
}

// 5. EXECUTAR EXCLUSÃO COM PREPARED STATEMENT
$sql_delete = "DELETE FROM tb_register WHERE id = ?";
$stmt_delete = $conecta_db->prepare($sql_delete);

if (!$stmt_delete) {
    header("Location: ../listagemAdm.php?delete=failed&message=db_error&page={$page_to_redirect}");
    exit;
}

$stmt_delete->bind_param("i", $id_usuario_edicao);

if ($stmt_delete->execute()) {
    $stmt_delete->close();
    
    // Registro de log (opcional)
    error_log("Admin {$_SESSION['username']} excluiu usuário ID {$id_usuario_edicao}");
    
    // Verificar se precisa ajustar a página
    $total_query = "SELECT COUNT(*) as total FROM tb_register";
    $total_result = mysqli_query($conecta_db, $total_query);
    $total_row = mysqli_fetch_assoc($total_result);
    $total_users = $total_row['total'];
    
    $limit = 10;
    $new_total_pages = ceil($total_users / $limit);
    
    if ($page_to_redirect > $new_total_pages && $new_total_pages > 0) {
        $page_to_redirect = $new_total_pages;
    }
    
    header("Location: ../listagemAdm.php?delete=success&id={$id_usuario_edicao}&page={$page_to_redirect}");
    exit;
} else {
    $stmt_delete->close();
    $error_message = urlencode("Erro ao excluir dados");
    header("Location: ../listagemAdm.php?delete=failed&message={$error_message}&page={$page_to_redirect}");
    exit;
}
?>