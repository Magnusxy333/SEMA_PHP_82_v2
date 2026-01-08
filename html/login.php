<!DOCTYPE html>
<html lang="pt-BR">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SEMA - Login</title>
    <link rel="stylesheet" href="../css/login1.css">
    <link rel="icon" href="../images/icon-site.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  </head>
  <body>
    <?php
    // Inicie a sessão com configurações recomendadas para PHP 8.2
    if (session_status() === PHP_SESSION_NONE) {
        session_start([
            'cookie_httponly' => true,
            'cookie_samesite' => 'Lax',
        ]);
    }
    ?>

    <div class="header">
      <div class="left">
        <a href="../index.php">
          <img class="icon" src="../images/sema.png" alt="icon">
        </a>
      </div>

      <div class="right">
        <button class="theme-toggle" id="themeToggle" aria-label="Alternar tema">
          <i class="fas fa-sun"></i>
          <i class="fas fa-moon"></i>
        </button>

        <a class="areas-text" href="../index.php">                    
          <i class="fas fa-house-user"></i>                   
          <span>HOME</span>                     
        </a>

        <a class="areas-text" href="location.php">
          <i class="fas fa-map-marker-alt"></i>
          <span>LOCALIZAÇÃO</span>
        </a>

        <a class="areas-text" href="orientations.php">
          <i class="fas fa-book-open"></i>
          <span>ORIENTAÇÕES</span>
        </a>

        <a class="areas-text" href="contacts.php">
          <i class="fas fa-phone-alt"></i>
          <span>CONTATOS</span>
        </a>

        <a class="areas-text" href="login.php">
          <i class='fas fa-sign-in-alt' id="login-size"></i>
        </a>
      </div>
    </div>

    <div class="main">
      <div class="login-container">
        <div class="login-header">
          <h1 class="login-main-title">
            <i class="fas fa-shield-alt"></i>
            LOGIN
          </h1>
          <p class="login-subtitle">Entre com suas credenciais para acessar o SEMA</p>
        </div>

        <form name="form2" method="post" class="login-form">
          <div class="form-group">
            <label for="username" class="form-label">
              <i class="fas fa-user"></i>
              Usuário
            </label>
            <input 
              type="text" 
              id="username" 
              name="txt_username" 
              class="form-input"
              placeholder="Digite seu nome de usuário"
              required
            >
          </div>

          <div class="form-group">
            <label for="password" class="form-label">
              <i class="fas fa-lock"></i>
              Senha
            </label>
            <div class="password-container">
              <input 
                type="password" 
                id="password" 
                name="txt_password" 
                class="form-input"
                placeholder="Digite sua senha"
                required
              >
              <button type="button" class="password-toggle" id="passwordToggle" aria-label="Mostrar senha">
                <i class="fas fa-eye"></i>
                <i class="fas fa-eye-slash"></i>
              </button>
            </div>
          </div>

          <div class="form-options">
            <a href="forgot_email_form.php" class="forgot-password">
              <i class="fas fa-key"></i>
              Esqueceu sua senha?
            </a>
          </div>

          <button type="submit" name="bt_incluir" value="LOGAR" class="login-btn" onclick="document.form2.action='../signIn.php'">
            <i class="fas fa-sign-in-alt"></i>
            ENTRAR
          </button>
        </form>

        <div class="register-section">

          <a class="register-link2" href="sub_links/register.php">
            <div class="register-div">
              <p class="register-text">Ainda não é cadastrado?</p>
              <label class="register-label">
                <i class="fas fa-user-plus"></i>
                Cadastre-se aqui
              </label>
            </div>
          </a>
        </div>

        <div class="login-info">
          <p class="info-text">
            *Com o cadastro você recebe atualizações imediatas do nosso projeto!
          </p>
        </div>
      </div>
    </div>

    <div class="footer">
      <div class="staff-information">
        <p>Ainda não nos conhece?</p>
        <a class="central-link" href="sub_links/about_us.php">sobre nós</a>
      </div>

      <div class="social_midias">
        <p class="staff-information">Nossas redes sociais</p>

        <div class="icons">
          <a href="https://www.instagram.com/elobos.acolhe?igsh=ZDE5N2F5ODVoY2pj">
            <img id="images" src="../images/icons/INSTA.webp" alt="Instagram">
          </a>

          <a href="https://x.com/ellobos675443">
            <img id="images" src="../images/icons/xTWT.avif" alt="Twitter">
          </a>

          <a href="https://www.youtube.com/@ellobos-n8n">
            <img id="images2" src="../images/icons/YOUYOU2.png" alt="YouTube">      
          </a>
        </div>
      </div>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.0.2/jquery.min.js"></script>
    <script>
      // Alerta para página de login
  if (!localStorage.getItem('login_warning_seen')) {
      setTimeout(function() {
          alert("🔓 LOGIN EXPERIMENTAL\n\n" +
                "Este sistema é para fins educacionais.\n\n" +
                "Dados de teste disponíveis:\n" +
                "• Usuário: demo / Senha: demo123\n" +
                "• Usuário: teste / Senha: teste@2024\n" +
                "• Usuário: admin / Senha: Admin#2024\n\n" +
                "Não use credenciais reais!");
          localStorage.setItem('login_warning_seen', 'true');
      }, 800);
  }

      // Seu JavaScript original mantido integralmente...
      const themeToggle = document.getElementById('themeToggle');
      const body = document.body;
      
      function getThemePreference() {
        const savedTheme = localStorage.getItem('theme');
        if (savedTheme) {
          return savedTheme;
        }
        if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
          return 'dark';
        }
        return 'light';
      }
      
      function applyTheme(theme) {
        if (theme === 'dark') {
          body.setAttribute('data-theme', 'dark');
        } else {
          body.setAttribute('data-theme', 'light');
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
        themeToggle.addEventListener('click', toggleTheme);
        if (window.matchMedia) {
          window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', e => {
            if (!localStorage.getItem('theme')) {
              applyTheme(e.matches ? 'dark' : 'light');
            }
          });
        }
      }
      
      const passwordToggle = document.getElementById('passwordToggle');
      const passwordInput = document.getElementById('password');
      const eyeIcon = passwordToggle.querySelector('.fa-eye');
      const eyeSlashIcon = passwordToggle.querySelector('.fa-eye-slash');
      
      passwordToggle.addEventListener('click', function() {
        const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordInput.setAttribute('type', type);
        if (type === 'text') {
          eyeIcon.style.display = 'none';
          eyeSlashIcon.style.display = 'inline-block';
          passwordToggle.setAttribute('aria-label', 'Ocultar senha');
        } else {
          eyeIcon.style.display = 'inline-block';
          eyeSlashIcon.style.display = 'none';
          passwordToggle.setAttribute('aria-label', 'Mostrar senha');
        }
      });
      
      document.addEventListener('DOMContentLoaded', function() {
        initTheme();
        eyeIcon.style.display = 'inline-block';
        eyeSlashIcon.style.display = 'none';
      });
    </script>
  </body>
</html>