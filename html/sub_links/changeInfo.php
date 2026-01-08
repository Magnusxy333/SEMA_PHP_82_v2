<?php
session_start();

// Incluir o arquivo de conexão
include '../../server.php';

// Verificar se a conexão foi estabelecida
if (!isset($conecta_db) || !$conecta_db) {
    die('Erro: Conexão com o banco de dados não estabelecida. Verifique o arquivo server.php');
}

// Verificar se o usuário está logado
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php');
    exit;
}

// Buscar informações apenas do usuário logado
$username = $_SESSION['username'];

// Usar prepared statement para maior segurança
$sql = "SELECT * FROM tb_register WHERE username = ?";
$stmt = mysqli_prepare($conecta_db, $sql);

if ($stmt) {
    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    // Verificar se encontrou o usuário
    if (mysqli_num_rows($result) > 0) {
        $linha = mysqli_fetch_assoc($result);
    } else {
        die('Usuário não encontrado.');
    }
    
    mysqli_stmt_close($stmt);
} else {
    die('Erro na preparação da consulta SQL: ' . mysqli_error($conecta_db));
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Mudar informações</title>
  <link rel="stylesheet" href="../../css/changeInfo.css">
  <link rel="icon" href="../../images/icon-site.png">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    
  <div class="header">
    <div class="left">
      <a href="../index.php">
        <img class="icon" src="../../images/sema.png" alt="icon">
      </a>
    </div>

    <div class="right">

        <button class="theme-toggle" id="themeToggle" aria-label="Alternar tema">
          <i class="fas fa-sun"></i>
          <i class="fas fa-moon"></i>
        </button>

        <a class="areas-text" href="../../index.php">                    
          <i class="fas fa-house-user"></i>                   
          HOME                     
        </a>

        <a class="areas-text" href="../location.php">
          <i class="fas fa-map-marker-alt"></i>
          LOCALIZAÇÃO
        </a>

        <a class="areas-text" href="../orientations.php">
          <i class="fas fa-book-open"></i>
          ORIENTAÇÕES
        </a>

        <a class="areas-text" href="../contacts.php">
          <i class="fas fa-phone-alt"></i>
          CONTATOS
        </a>

      <?php if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true): ?>
          <a class="areas-text" href="profile.php">
            <i class="fas fa-user-circle"></i>
            PERFIL
          </a>                                
        </div>
        <?php else: ?>
        <a class="areas-text" href="login.php">
          <i class='fas fa-sign-in-alt' id="login-size"></i>
        </a>
      <?php endif; ?>
    </div>
  </div> 

  <div class="main">

    <div class="container-grid">
    
      <h1 class="title_alternate">Alterar dados </h1>

      <form name="form2" method="POST" action="../../update.php">

        <div style="display: flex; flex-direction: row; align-items: center;">
          <div>
            <div class="info-item">
            <span class="info-label">Usuário:</span>
            <input class="info-input" type="text" value="<?php echo htmlspecialchars($linha['username']); ?>" 
            name="username">
            </div>
        
            <div class="info-item">
            <span class="info-label">Email:</span>
            <input class="info-input" type="text" value="<?php echo htmlspecialchars($linha['email']); ?>" 
            name="email">
            </div>
        
            <div class="info-item">
                <span class="info-label">Nome:</span>
                <input class="info-input" type="text" value="<?php echo htmlspecialchars($linha['nome']); ?>" 
                name="nome">
            </div>

            <div class="info-item">
                <span class="info-label">Sobrenome:</span>
                <input class="info-input" type="text" value="<?php echo htmlspecialchars($linha['sobrenome']); ?>" 
                name="sobrenome">
            </div>      
          </div>         
          
        <div class="image_profile">
          <div class="image_side">
            <div class="upload-instructions">
              <img class="icon_upload" src="../../images/icons/update_image.png" alt="">
            </div>
          </div>
        </div>     
        </div>    
        
        <div class="password-button">

          <div class="info-item">   
            <div>
              <span class="info-label">
              Senha:
              </span>             
              <input class="info-input" type="password" id="senha-input" value="<?php echo htmlspecialchars($linha['senha']); ?>" 
              name="senha">
            </div>
          </div>

          <div class="center-vision">
            <div>
              <div>
                <a href="#" id="mostrar-senha" style="margin-top: 10px; color: #007bff; text-decoration: none; cursor: pointer;">
                  <i class="fas fa-eye"></i> Mostrar senha
                </a>
              </div>
            </div>
          </div>
        </div>

        
                  
        <div class="buttons-container">
          
            <button class="delete-account-button" onclick="history.back()" type="button">
              Voltar
            </button>
          
            
          <button class="logout-button"
          type="submit" name="bt_incluir" 
          value="UPDATE" 
          onClick="document.form2.action='../../update.php'
          ">
            Alterar informações
          </button>

          </div>
        </div>    

      </form>
    </div>
  </div>

  <div class="footer">
              
      <div class="staff-information">
        <p class="staff-information2">Ainda não nos conhece?</p>
        <a class="central-link" href="about_us.php">sobre nós</a>
      </div>

      <div class="social_midias">
        <p class="staff-information">Nossas redes sociais</p>

        <div class="icons">
          
          <a href="https://www.instagram.com/elobos.acolhe?igsh=ZDE5N2F5ODVoY2pj">
            <img id="images" src="../../images/icons/INSTA.webp" alt="">
          </a>

          <a href="https://x.com/ellobos675443">
            <img id="images" src="../../images/icons/xTWT.avif" alt="">
          </a>

          <a href="https://www.youtube.com/@ellobos-n8n">
            <img id="images2" src="../../images/icons/YOUYOU2.png" alt="">      
          </a>
        </div>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.0.2/jquery.min.js"></script>

    <!-- SISTEMA DE DARK MODE -->
    <script>
      // ===== SISTEMA DE TEMA CLARO/ESCURO =====
      
      // Elementos DOM
      const themeToggle = document.getElementById('themeToggle');
      const body = document.body;
      
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
          body.setAttribute('data-theme', 'dark');
        } else {
          body.removeAttribute('data-theme');
        }
      }
      
      // Alternar tema
      function toggleTheme() {
        const currentTheme = body.getAttribute('data-theme');
        const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
        
        applyTheme(newTheme);
        localStorage.setItem('theme', newTheme);
      }
      
      // Inicializar tema
      function initTheme() {
        const theme = getThemePreference();
        applyTheme(theme);
        
        // Adicionar evento de clique no botão de alternância
        themeToggle.addEventListener('click', toggleTheme);
        
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
      const senhaInput = document.getElementById('senha-input');
      const mostrarSenha = document.getElementById('mostrar-senha');
      let senhaVisivel = true; // Começa como texto, pois seu input está como type="text"

      mostrarSenha.addEventListener('mousedown', function(e) {
        e.preventDefault();
        senhaInput.type = 'text';
        senhaVisivel = true;
        mostrarSenha.innerHTML = '<i class="fas fa-eye-slash"></i> Ocultar senha';
      });

      mostrarSenha.addEventListener('mouseup', function(e) {
        e.preventDefault();
        senhaInput.type = 'password';
        senhaVisivel = false;
        mostrarSenha.innerHTML = '<i class="fas fa-eye"></i> Mostrar senha';
      });

      mostrarSenha.addEventListener('mouseleave', function(e) {
        if (senhaVisivel) {
          senhaInput.type = 'password';
          senhaVisivel = false;
          mostrarSenha.innerHTML = '<i class="fas fa-eye"></i> Mostrar senha';
        }
      });
    </script>
</body>
</html>