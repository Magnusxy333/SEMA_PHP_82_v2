<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start([
        'cookie_httponly' => true,
        'cookie_samesite' => 'Lax',
    ]);
}

include 'server.php'; 

$username = $_POST["txt_username"] ?? '';
$password = $_POST["txt_password"] ?? '';

// Consulta incluindo user_role e is_active
$stmt = $conecta_db->prepare("SELECT id, username, senha, profile_picture_url, user_role, is_active FROM tb_register WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    
    // Verificar se a conta está ativa
    if (isset($row['is_active']) && $row['is_active'] == 0) {
        header('Location: html/fail_pages/accountDisabled.php');
        exit;
    }
    
    // Verificar senha (ATENÇÃO: senha em texto plano - considere usar password_hash())
    if ($row['senha'] === $password) {
        $_SESSION['loggedin'] = true;
        $_SESSION['username'] = $username;
        $_SESSION['user_id'] = $row['id'];
        $_SESSION['user_role'] = $row['user_role'] ?? 'user'; // ← IMPORTANTE: salvar role na sessão
        
        $photo_db_path = $row['profile_picture_url'] ?? '';

        if (!empty($photo_db_path)) {
            $_SESSION['profile_picture_url'] = $photo_db_path;
        } else {
            $_SESSION['profile_picture_url'] = 'images/default-profile.png';
        }

        header('Location: index.php');
        exit;
    } else {
        header('Location: html/fail_pages/accessDenied.php');
        exit;
    }
} else {
    header('Location: html/fail_pages/accessDenied.php');
    exit;
}
?>