<?php
session_start();
// Verificar se o usuário está logado antes de permitir a exclusão
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Deletar Conta</title>
  <link rel="stylesheet" href="../../css/deleteVerification.css">
  <link rel="icon" href="../../images/icon-site.png" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>
  <style>
    /* Estilos para o botão de tema - IGUAL AO REGISTER.PHP */
    .theme-toggle {
        position: fixed;
        top: 20px;
        left: 20px;
        background: transparent;
        color: var(--text-color, #333);
        border: 2px solid var(--text-color, #333);
        border-radius: 50%;
        width: 40px;
        height: 40px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
        z-index: 1000;
        transition: all 0.3s ease;
    }

    .theme-toggle:hover {
        background: var(--text-color, #333);
        color: var(--background-color, #fff);
        transform: scale(1.1);
    }

    .theme-toggle i {
        position: absolute;
        transition: opacity 0.3s ease, transform 0.3s ease;
    }

    .theme-toggle .fa-moon {
        opacity: 0;
        transform: rotate(90deg);
    }

    [data-theme="dark"] .theme-toggle {
        border-color: #fff;
        color: #fff;
    }

    [data-theme="dark"] .theme-toggle:hover {
        background: #fff;
        color: #333;
    }

    [data-theme="dark"] .theme-toggle .fa-sun {
        opacity: 0;
        transform: rotate(90deg);
    }

    [data-theme="dark"] .theme-toggle .fa-moon {
        opacity: 1;
        transform: rotate(0deg);
    }
    
    /* Estilos para o formulário */
    form[name="form2"] {
        display: inline;
        margin: 0;
        padding: 0;
    }
    
    /* Estilos para mensagens de alerta */
    .alert {
        padding: 15px;
        margin: 20px auto;
        max-width: 600px;
        border-radius: 5px;
        text-align: center;
    }
    
    .alert-danger {
        background-color: #f8d7da;
        border: 1px solid #f5c6cb;
        color: #721c24;
    }
    
    .alert-success {
        background-color: #d4edda;
        border: 1px solid #c3e6cb;
        color: #155724;
    }
    
    /* Estilos para tema escuro */
    [data-theme="dark"] {
        background-color: #121212;
        color: #ffffff;
    }
    
    [data-theme="dark"] .alert-danger {
        background-color: #2c0b0e;
        border-color: #842029;
        color: #ea868f;
    }
    
    [data-theme="dark"] .alert-success {
        background-color: #0f5132;
        border-color: #0a3622;
        color: #75b798;
    }
    
    [data-theme="dark"] .main-content {
        background-color: #1e1e1e;
        color: #ffffff;
    }
  </style>
</head>
<body>
<!-- Botão de alternância de tema -->
<button class="theme-toggle" id="themeToggle" aria-label="Alternar tema">
  <i class="fas fa-sun"></i>
  <i class="fas fa-moon"></i>
</button>

<div class="main">
  <div class="main-content">
    <?php
    // Verificar se há mensagens de erro ou sucesso
    if (isset($_GET['error'])) {
        $error = htmlspecialchars($_GET['error']);
        echo '<div class="alert alert-danger">';
        if ($error === 'not_logged_in') {
            echo 'Você precisa estar logado para excluir sua conta.';
        } elseif ($error === 'delete_failed') {
            echo 'Falha ao excluir a conta. Tente novamente.';
        } else {
            echo 'Ocorreu um erro ao processar sua solicitação.';
        }
        echo '</div>';
    }
    
    if (isset($_GET['success'])) {
        echo '<div class="alert alert-success">';
        echo 'Conta excluída com sucesso!';
        echo '</div>';
    }
    ?>
    
    <h2 class="title-delete">⚠️ Deletar Conta</h2>
    <p><strong>Tem certeza que deseja deletar sua conta?</strong></p>
    <p>Esta ação <strong>NÃO</strong> pode ser desfeita e todos os seus dados serão perdidos permanentemente.</p>
    <p class="warning-text">❌ <strong>Atenção:</strong> Após a exclusão, você não terá mais acesso ao sistema.</p>
    
    <div class="delete-container-buttons">
      <a href="../profile.php">
        <button class="back-button">
          <i class="fas fa-arrow-left"></i> Não, voltar
        </button>
      </a>

      <button class="confirm-button" id="confirmDeleteBtn">
        <i class="fas fa-trash-alt"></i> Sim, deletar minha conta
      </button>
    </div>
  </div>
</div>

<script>
  // ===== SISTEMA DE TEMA CLARO/ESCURO =====
  const themeToggle = document.getElementById('themeToggle');
  const body = document.body;
  
  function getThemePreference() {
    const savedTheme = localStorage.getItem('theme');
    if (savedTheme) {
      return savedTheme;
    }
    
    // Verificar preferência do sistema
    if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
      return 'dark';
    }
    
    return 'light';
  }
  
  function applyTheme(theme) {
    if (theme === 'dark') {
      body.setAttribute('data-theme', 'dark');
      // Atualizar o ícone do botão
      themeToggle.querySelector('.fa-sun').style.opacity = '0';
      themeToggle.querySelector('.fa-moon').style.opacity = '1';
    } else {
      body.removeAttribute('data-theme');
      // Atualizar o ícone do botão
      themeToggle.querySelector('.fa-sun').style.opacity = '1';
      themeToggle.querySelector('.fa-moon').style.opacity = '0';
    }
  }
  
  function toggleTheme() {
    const currentTheme = body.getAttribute('data-theme');
    const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
    
    applyTheme(newTheme);
    localStorage.setItem('theme', newTheme);
  }
  
  function initTheme() {
    const theme = getThemePreference();
    applyTheme(theme);
    
    // Adicionar evento de clique ao botão
    themeToggle.addEventListener('click', toggleTheme);
    
    // Observar mudanças na preferência do sistema
    if (window.matchMedia) {
      const mediaQuery = window.matchMedia('(prefers-color-scheme: dark)');
      mediaQuery.addEventListener('change', (e) => {
        // Só aplicar se o usuário não tiver uma preferência salva
        if (!localStorage.getItem('theme')) {
          applyTheme(e.matches ? 'dark' : 'light');
        }
      });
    }
  }
  
  // ===== CONFIRMAÇÃO DE EXCLUSÃO =====
  document.addEventListener('DOMContentLoaded', function() {
    // Inicializar tema
    initTheme();
    
    // Configurar botão de confirmação
    const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');
    
    if (confirmDeleteBtn) {
      confirmDeleteBtn.addEventListener('click', function() {
        if (confirm('🚨 ATENÇÃO!\n\nVocê está prestes a excluir permanentemente sua conta.\n\n' +
                   '✅ Sua conta e todos os dados associados serão removidos do sistema.\n' +
                   '✅ Você perderá acesso a todos os recursos.\n' +
                   '✅ Esta ação NÃO pode ser desfeita.\n\n' +
                   'Tem certeza absoluta que deseja continuar?')) {
          
          // Criar um formulário dinâmico para envio
          const form = document.createElement('form');
          form.method = 'POST';
          form.action = '../../delete.php';
          
          // Adicionar campo CSRF token se necessário
          const csrfInput = document.createElement('input');
          csrfInput.type = 'hidden';
          csrfInput.name = 'csrf_token';
          csrfInput.value = '<?php echo isset($_SESSION["csrf_token"]) ? $_SESSION["csrf_token"] : ""; ?>';
          form.appendChild(csrfInput);
          
          // Desabilitar botão e mostrar carregamento
          this.disabled = true;
          this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processando...';
          
          // Adicionar formulário ao corpo e enviar
          document.body.appendChild(form);
          form.submit();
        }
      });
    }
    
    // Aplicar estilos CSS para as variáveis de tema
    function updateThemeVariables() {
      const isDark = body.getAttribute('data-theme') === 'dark';
      const root = document.documentElement;
      
      if (isDark) {
        root.style.setProperty('--background-color', '#121212');
        root.style.setProperty('--text-color', '#ffffff');
      } else {
        root.style.setProperty('--background-color', '#ffffff');
        root.style.setProperty('--text-color', '#333333');
      }
    }
    
    // Chamar a função para configurar as variáveis CSS
    updateThemeVariables();
    
    // Observar mudanças no atributo data-theme
    const observer = new MutationObserver(updateThemeVariables);
    observer.observe(body, { attributes: true, attributeFilter: ['data-theme'] });
  });
</script>
</body>
</html>