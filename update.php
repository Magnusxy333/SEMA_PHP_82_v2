<?php
session_start();

// Incluir o arquivo de conexão e autenticação
include 'server.php';
require_once 'auth.php';

// Verificar se o usuário está logado
requireLogin();

// Verificar se a conexão foi estabelecida
if (!isset($conecta_db) || !$conecta_db) {
    die('Erro: Conexão com o banco de dados não estabelecida.');
}

// Obter o username atual da sessão
$username_atual = $_SESSION['username'];
$action_performed = false;

// -------------------------------------------------------------------
// ✅ LÓGICA 1: ATUALIZAÇÃO DA FOTO DE PERFIL (Formulário de upload)
// -------------------------------------------------------------------
if (isset($_FILES['foto_perfil']) && $_FILES['foto_perfil']['error'] === UPLOAD_ERR_OK) {
    
    // Verificar tipo de arquivo
    $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
    $file_type = mime_content_type($_FILES['foto_perfil']['tmp_name']);
    
    if (!in_array($file_type, $allowed_types)) {
        header('Location: html/profile.php?error=invalid_file_type');
        exit;
    }
    
    // Verificar tamanho do arquivo (max 2MB)
    if ($_FILES['foto_perfil']['size'] > 2097152) {
        header('Location: html/profile.php?error=file_too_large');
        exit;
    }

    $file_tmp = $_FILES['foto_perfil']['tmp_name'];
    $file_name = basename($_FILES['foto_perfil']['name']);

    // Criar nome de arquivo único
    $ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
    $new_file_name = $username_atual . '_' . time() . '.' . $ext;

    // Pasta de destino
    $upload_dir = 'uploads/';
    
    // Criar diretório se não existir
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }
    
    $destination = $upload_dir . $new_file_name;

    if (move_uploaded_file($file_tmp, $destination)) {
        // Sucesso no upload
        $photo_path_db = mysqli_real_escape_string($conecta_db, $destination);

        // Query de atualização da foto
        $sql_update_photo = "UPDATE tb_register 
                             SET profile_picture_url = '$photo_path_db' 
                             WHERE username = ?";
        
        $stmt = mysqli_prepare($conecta_db, $sql_update_photo);
        mysqli_stmt_bind_param($stmt, "s", $username_atual);
        
        if (mysqli_stmt_execute($stmt)) {
            $action_performed = true;
            // Atualizar na sessão
            $_SESSION['profile_picture_url'] = $photo_path_db;
            header('Location: html/profile.php?upload=success');
            exit;
        } else {
            header('Location: html/profile.php?error=database_error');
            exit;
        }
        
        mysqli_stmt_close($stmt);
    } else {
        header('Location: html/profile.php?error=upload_failed');
        exit;
    }
}

// -------------------------------------------------------------------
// ✅ LÓGICA 2: ATUALIZAÇÃO DE INFORMAÇÕES GERAIS (Formulário bt_incluir)
// -------------------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['bt_incluir'])) {
    
    // Coletar e sanitizar os dados
    $novo_username  = mysqli_real_escape_string($conecta_db, $_POST['username'] ?? '');
    $novo_email     = mysqli_real_escape_string($conecta_db, $_POST['email'] ?? '');
    $novo_nome      = mysqli_real_escape_string($conecta_db, $_POST['nome'] ?? '');
    $novo_sobrenome = mysqli_real_escape_string($conecta_db, $_POST['sobrenome'] ?? '');
    $nova_senha     = $_POST['senha'] ?? '';
    
    // Verificar se o novo username já existe (se for diferente do atual)
    if ($novo_username !== $username_atual) {
        $check_sql = "SELECT id FROM tb_register WHERE username = ? AND username != ?";
        $check_stmt = mysqli_prepare($conecta_db, $check_sql);
        mysqli_stmt_bind_param($check_stmt, "ss", $novo_username, $username_atual);
        mysqli_stmt_execute($check_stmt);
        mysqli_stmt_store_result($check_stmt);
        
        if (mysqli_stmt_num_rows($check_stmt) > 0) {
            mysqli_stmt_close($check_stmt);
            header('Location: html/sub_links/changeInfo.php?error=username_taken');
            exit;
        }
        mysqli_stmt_close($check_stmt);
    }
    
    // Construir a query de atualização usando prepared statements
    $sql_update = "UPDATE tb_register SET username = ?, email = ?, nome = ?, sobrenome = ?";
    $params = [$novo_username, $novo_email, $novo_nome, $novo_sobrenome];
    $types = "ssss";
    
    // Atualizar senha apenas se foi fornecida
    if (!empty($nova_senha)) {
        // ⚠️ SENHA EM TEXTO PLANO (conforme solicitado)
        $sql_update .= ", senha = ?";
        $params[] = $nova_senha; // Senha em texto plano
        $types .= "s";
    }
    
    $sql_update .= " WHERE username = ?";
    $params[] = $username_atual;
    $types .= "s";
    
    // Executar a atualização
    $stmt = mysqli_prepare($conecta_db, $sql_update);
    
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, $types, ...$params);
        
        if (mysqli_stmt_execute($stmt)) {
            $action_performed = true;
            
            // Se o usuário mudou o username, atualize a sessão
            if ($novo_username !== $username_atual) {
                $_SESSION['username'] = $novo_username;
                $username_atual = $novo_username;
            }
            
            // Redirecionar com mensagem de sucesso
            header('Location: html/profile.php?update=success');
            exit;
        } else {
            // Redirecionar com mensagem de erro
            $error_message = urlencode("Erro ao atualizar dados: " . mysqli_error($conecta_db));
            header('Location: html/sub_links/changeInfo.php?update=failed&message=' . $error_message);
            exit;
        }
        
        mysqli_stmt_close($stmt);
    } else {
        $error_message = urlencode("Erro na preparação da query: " . mysqli_error($conecta_db));
        header('Location: html/sub_links/changeInfo.php?update=failed&message=' . $error_message);
        exit;
    }
}

// -------------------------------------------------------------------
// ✅ TRATAMENTO FINAL (Se a página foi acessada sem POST ou com POST irrelevante)
// -------------------------------------------------------------------
if (!$action_performed) {
    // Redirecionar se a página foi acessada diretamente sem formulário
    header('Location: html/sub_links/changeInfo.php');
    exit;
}
?>