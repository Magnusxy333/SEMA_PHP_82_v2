<?php
session_start();
include '../../server.php'; 
require_once '../../auth.php';  // ADICIONADO

// 1. VERIFICAR AUTENTICAÇÃO E PERMISSÃO DE ADM (ATUALIZADO)
if (!isLoggedIn() || !isAdmin()) {
    header('Location: ../../index.php?error=access_denied');
    exit;
}

// 2. OBTER O ID DO USUÁRIO A SER EDITADO
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: ../listagemAdm.php?error=no_id');
    exit;
}

$id_usuario_edicao = (int) $_GET['id']; 
$page_redirect = isset($_GET['page']) ? max(1, (int) $_GET['page']) : 1;

// 3. CONSULTAR OS DADOS DO USUÁRIO COM PREPARED STATEMENT
$query = "SELECT id, username, nome, sobrenome, email FROM tb_register WHERE id = ?";
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
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Editar Usuário ID: <?php echo htmlspecialchars($linha['id']); ?></title>
    <link rel="stylesheet" href="../../css/editUser1.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="icon" href="../../images/icon-site.png">
</head>
<body>
    <div class="main">

        <button class="theme-toggle" id="themeToggle" aria-label="Alternar tema">
          <i class="fas fa-sun"></i>
          <i class="fas fa-moon"></i>
        </button>

        <div class="container-grid">
            <center>
                <h1 class="title">Editando Usuário: <?php echo htmlspecialchars($linha['username']); ?></h1>
            </center>
            <center>
                <p class="id">ID: <?php echo htmlspecialchars($linha['id']); ?></p>
            </center>
            
            <form name="form_edicao" method="POST" action="editUserUpdate.php" onsubmit="return validateForm()"> 
                <input type="hidden" name="id" value="<?php echo htmlspecialchars($linha['id']); ?>">
                <input type="hidden" name="list_page" value="<?php echo htmlspecialchars($page_redirect); ?>">

                <div class="form-group">
                    <label for="username">Username:</label>
                    <input class="input-info" type="text" name="username" id="username" 
                           value="<?php echo htmlspecialchars($linha['username']); ?>" 
                           required pattern="[A-Za-z0-9_]{3,50}"
                           title="Username deve conter apenas letras, números e underscore (3-50 caracteres)">
                </div>

                <div class="form-group">
                    <label for="email">E-mail:</label>
                    <input class="input-info" type="email" name="email" id="email" 
                           value="<?php echo htmlspecialchars($linha['email']); ?>" required>
                </div>

                <div class="form-group">
                    <label for="nome">Nome:</label>
                    <input class="input-info" type="text" name="nome" id="nome" 
                           value="<?php echo htmlspecialchars($linha['nome']); ?>" required>
                </div>

                <div class="form-group">
                    <label for="sobrenome">Sobrenome:</label>
                    <input class="input-info" type="text" name="sobrenome" id="sobrenome" 
                           value="<?php echo htmlspecialchars($linha['sobrenome']); ?>" required>
                </div>

                <div class="form-group">
                    <label for="senha">Nova Senha (opcional):</label>
                    <input class="input-info" type="password" name="senha" id="senha" 
                           placeholder="Deixe em branco para manter a senha atual"
                           pattern=".{6,}"
                           title="A senha deve ter no mínimo 6 caracteres">
                    <small>Deixe em branco para manter a senha atual</small>
                </div>

                <div class="buttons-container">
                    <a href="../listagemAdm.php?page=<?php echo htmlspecialchars($page_redirect); ?>">
                        <button class="back-button" type="button">
                            Voltar à Listagem
                        </button>
                    </a>  
                    <button class="logout-button" type="submit" name="bt_incluir" value="UPDATE">
                        Salvar Alterações
                    </button>
                </div>
            </form>
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
        function validateForm() {
            const username = document.getElementById('username').value;
            const email = document.getElementById('email').value;
            const nome = document.getElementById('nome').value;
            const sobrenome = document.getElementById('sobrenome').value;
            const senha = document.getElementById('senha').value;
            
            // Validação básica
            if (username.length < 3 || username.length > 50) {
                alert('Username deve ter entre 3 e 50 caracteres');
                return false;
            }
            
            if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
                alert('Por favor, insira um email válido');
                return false;
            }
            
            if (nome.trim() === '' || sobrenome.trim() === '') {
                alert('Nome e sobrenome são obrigatórios');
                return false;
            }
            
            if (senha !== '' && senha.length < 6) {
                alert('A senha deve ter no mínimo 6 caracteres');
                return false;
            }
            
            return confirm('Tem certeza que deseja salvar as alterações?');
        }
    </script>
</body>
</html>