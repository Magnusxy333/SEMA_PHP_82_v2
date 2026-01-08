<?php
// auth.php - Sistema de autenticação e autorização
// Localização: raiz do projeto (mesmo diretório que index.php)

// Iniciar sessão se não estiver iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start([
        'cookie_httponly' => true,
        'cookie_samesite' => 'Lax',
        'cookie_secure' => isset($_SERVER['HTTPS']),
        'use_strict_mode' => true
    ]);
}

// Configurar timeout de sessão (30 minutos)
$session_timeout = 1800; // 30 minutos em segundos
if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > $session_timeout)) {
    session_unset();
    session_destroy();
    header('Location: index.php?error=session_expired');
    exit;
}
$_SESSION['LAST_ACTIVITY'] = time();

// Regenerar ID de sessão periodicamente (a cada 5 minutos)
if (!isset($_SESSION['CREATED'])) {
    $_SESSION['CREATED'] = time();
} else if (time() - $_SESSION['CREATED'] > 300) { // 5 minutos
    session_regenerate_id(true);
    $_SESSION['CREATED'] = time();
}

/**
 * Verifica se o usuário está logado
 */
function isLoggedIn() {
    return isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true;
}

/**
 * Verifica se o usuário é admin (admin ou superadmin)
 */
function isAdmin() {
    return isset($_SESSION['user_role']) && 
           in_array($_SESSION['user_role'], ['admin', 'superadmin']);
}

/**
 * Verifica se o usuário é superadmin
 */
function isSuperAdmin() {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'superadmin';
}

/**
 * Requer que o usuário esteja logado
 */
function requireLogin() {
    if (!isLoggedIn()) {
        $redirect = urlencode($_SERVER['REQUEST_URI']);
        header('Location: html/login.php?redirect=' . $redirect);
        exit;
    }
}

/**
 * Requer que o usuário seja admin
 */
function requireAdmin() {
    requireLogin();
    if (!isAdmin()) {
        header('Location: index.php?error=access_denied');
        exit;
    }
}

/**
 * Requer que o usuário seja superadmin
 */
function requireSuperAdmin() {
    requireLogin();
    if (!isSuperAdmin()) {
        header('Location: index.php?error=access_denied_admin');
        exit;
    }
}

/**
 * Obtém os dados do usuário atual
 */
function getCurrentUser() {
    if (!isLoggedIn()) return null;
    
    return [
        'id' => $_SESSION['user_id'] ?? null,
        'username' => $_SESSION['username'] ?? '',
        'role' => $_SESSION['user_role'] ?? 'user',
        'profile_picture' => $_SESSION['profile_picture_url'] ?? 'images/default-profile.png'
    ];
}

/**
 * Gera um token CSRF
 */
function generateCSRFToken() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Verifica um token CSRF
 */
function verifyCSRFToken($token) {
    if (!isset($_SESSION['csrf_token'])) {
        return false;
    }
    return hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Gera um token CSRF para formulário
 */
function csrfField() {
    $token = generateCSRFToken();
    return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($token) . '">';
}

/**
 * Limpa e valida entrada de dados
 */
function sanitizeInput($input) {
    if (is_array($input)) {
        foreach ($input as $key => $value) {
            $input[$key] = sanitizeInput($value);
        }
        return $input;
    }
    
    $input = trim($input);
    $input = stripslashes($input);
    return htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
}

/**
 * Redireciona com mensagem de erro
 */
function redirectWithError($url, $error) {
    $_SESSION['error_message'] = $error;
    header('Location: ' . $url);
    exit;
}

/**
 * Obtém mensagem de erro da sessão
 */
function getErrorMessage() {
    if (isset($_SESSION['error_message'])) {
        $message = $_SESSION['error_message'];
        unset($_SESSION['error_message']);
        return $message;
    }
    return '';
}
?>