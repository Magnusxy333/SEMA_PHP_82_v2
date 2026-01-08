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
$page_redirect = isset($_GET['page']) ? max(1, (int) $_GET['page']) : 1;

if ($id_usuario_edicao <= 0) {
    header('Location: ../listagemAdm.php?error=invalid_id');
    exit;
}

// 3. CONSULTAR DADOS DO USUÁRIO COM PREPARED STATEMENT
$query = "SELECT username, nome, sobrenome, email FROM tb_register WHERE id = ?";
$stmt = $conecta_db->prepare($query);

if (!$stmt) {
    header('Location: ../listagemAdm.php?error=db_error');
    exit;
}

$stmt->bind_param("i", $id_usuario_edicao);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->num_rows == 0) {
    $stmt->close();
    header('Location: ../listagemAdm.php?error=user_not_found');
    exit;
}

$linha = $resultado->fetch_assoc();
$stmt->close();

$username_a_excluir = $linha['username'];

// 4. IMPEDIR AUTO-EXCLUSÃO
if ($_SESSION['username'] === $username_a_excluir) {
    header("Location: ../listagemAdm.php?error=self_delete&page={$page_redirect}");
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmar Exclusão</title>
    <link rel="icon" href="../../images/icon-site.png">
    <link rel="stylesheet" href="../../css/deleteUserConfirm2.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <!-- Botão de alternância de tema -->
        <button class="theme-toggle" id="themeToggle" aria-label="Alternar tema">
          <i class="fas fa-sun"></i>
          <i class="fas fa-moon"></i>
        </button>
    <div class="main">

        <div class="main-content">
            <h2 class="title-delete">⚠️ ATENÇÃO: CONFIRMAR EXCLUSÃO</h2>
            
            <div class="user-details">
                <p><strong>ID:</strong> <?php echo htmlspecialchars($id_usuario_edicao); ?></p>
                <p><strong>Username:</strong> <?php echo htmlspecialchars($linha['username']); ?></p>
                <p><strong>Nome:</strong> <?php echo htmlspecialchars($linha['nome'] . ' ' . $linha['sobrenome']); ?></p>
                <p><strong>Email:</strong> <?php echo htmlspecialchars($linha['email']); ?></p>
            </div>
            
            <p><strong>Você tem certeza que deseja <span style="color: #dc3545;">EXCLUIR PERMANENTEMENTE</span> esta conta de usuário?</strong></p>
            <p class="warning-text">❌ Esta ação é <strong>IRREVERSÍVEL</strong> e todos os dados serão perdidos!</p>
            
            <div class="delete-container-buttons">
                <a href="../listagemAdm.php?page=<?php echo htmlspecialchars($page_redirect); ?>">
                    <button class="back-button" type="button">
                        <i class="fas fa-arrow-left"></i> Cancelar e Voltar
                    </button>
                </a>

                <button class="confirm-delete-button" type="button" id="confirmDeleteBtn">
                    <i class="fas fa-trash-alt"></i> Sim, Excluir Definitivamente
                </button>
            </div>
        </div>
    </div>

    <!-- SISTEMA DE DARK MODE -->
    <script>
      // ===== SISTEMA DE TEMA CLARO/ESCURO =====
      // Elementos DOM
      const themeToggle = document.getElementById('themeToggle');

      // Verificar preferência salva ou preferência do sistema
      function getThemePreference() {
          // Verificar se há uma preferência salva no localStorage
          const savedTheme = localStorage.getItem('theme');
          if (savedTheme) {
              return savedTheme;
          }
          
          // Verificar preferência do sistema
          if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
              return 'dark';
          }
          
          // Tema claro como padrão
          return 'light';
      }

      // Aplicar tema
      function applyTheme(theme) {
          if (theme === 'dark') {
              document.documentElement.setAttribute('data-theme', 'dark');
          } else {
              document.documentElement.removeAttribute('data-theme');
          }
      }

      // Alternar tema
      function toggleTheme() {
          const currentTheme = document.documentElement.getAttribute('data-theme');
          const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
          
          applyTheme(newTheme);
          localStorage.setItem('theme', newTheme);
      }

      // Inicializar tema
      function initTheme() {
          const theme = getThemePreference();
          applyTheme(theme);
          
          // Adicionar evento de clique no botão de alternância
          if (themeToggle) {
              themeToggle.addEventListener('click', toggleTheme);
          }
          
          // Ouvir mudanças na preferência do sistema
          if (window.matchMedia) {
              window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', e => {
                  // Só aplicar se o usuário não tiver uma preferência salva
                  if (!localStorage.getItem('theme')) {
                      applyTheme(e.matches ? 'dark' : 'light');
                  }
              });
          }
      }

      // Inicializar quando o DOM estiver carregado
      document.addEventListener('DOMContentLoaded', initTheme);
    </script>

    <script>
        document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
            if (confirm('ATENÇÃO FINAL!\n\nVocê está prestes a excluir permanentemente o usuário:\n\n' +
                       'ID: <?php echo $id_usuario_edicao; ?>\n' +
                       'Username: <?php echo addslashes($linha['username']); ?>\n\n' +
                       'Esta ação NÃO pode ser desfeita!\n\nContinuar?')) {
                
                // Desabilitar botão para evitar múltiplos cliques
                this.disabled = true;
                this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Excluindo...';
                
                // Redirecionar para exclusão
                window.location.href = 'deleteUser.php?id=<?php echo $id_usuario_edicao; ?>&page=<?php echo $page_redirect; ?>';
            }
        });
        
        // Prevenir clique direito e outras ações (opcional)
        document.addEventListener('contextmenu', function(e) {
            if (e.target.closest('.main-content')) {
                e.preventDefault();
            }
        });
    </script>
</body>
</html>