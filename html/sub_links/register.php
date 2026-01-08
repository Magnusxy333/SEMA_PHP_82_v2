<!DOCTYPE html>
<html lang="pt-BR">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="../../css/register3.css" />
    <link rel="icon" href="../../images/icon-site.png" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>
    <title>SEMA - Cadastro</title>
    
  </head>
  <body>
    <?php
    // Inicie a sessão no início da página
    session_start();
    
    // Recuperar erros e dados anteriores da sessão
    $errors = $_SESSION['registration_errors'] ?? [];
    $old_data = $_SESSION['registration_data'] ?? [];
    
    // Recuperar mensagem de sucesso da URL
    $success = isset($_GET['success']) ? $_GET['success'] : '';
    $message = isset($_GET['message']) ? $_GET['message'] : '';
    
    // Limpar dados da sessão após recuperar
    if (isset($_SESSION['registration_errors'])) {
        unset($_SESSION['registration_errors']);
    }
    if (isset($_SESSION['registration_data'])) {
        unset($_SESSION['registration_data']);
    }
    ?>

    <!-- Header Padronizado -->
    <div class="header">
      <div class="left">
        <a href="../../index.php">
          <img class="icon" src="../../images/sema.png" alt="icon" />
        </a>
      </div>

      <div class="right">
        <!-- Botão de alternância de tema -->
        <button class="theme-toggle" id="themeToggle" aria-label="Alternar tema">
          <i class="fas fa-sun"></i>
          <i class="fas fa-moon"></i>
        </button>

        <a class="areas-text" href="../../index.php">
          <i class="fas fa-house-user"></i>
          HOME
        </a>

        <a class="areas-text" href="../location.php">
          <i class="fas fa-map-marker-alt"> </i>
          LOCALIZAÇÃO
        </a>

        <a class="areas-text" href="../orientations.php">
          <i class="fas fa-book-open"> </i>
          ORIENTAÇÕES
        </a>

        <a class="areas-text" href="../contacts.php">
          <i class="fas fa-phone-alt"></i>
          CONTATOS
        </a>

        <a class="areas-text" href="../login.php">
          <i class="fas fa-sign-in-alt" id="login-size"></i>
        </a>
      </div>
    </div>

    <!-- Conteúdo Principal -->
    <div class="main">
      
      <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
          <h4><i class="fas fa-exclamation-triangle"></i> Por favor, corrija os seguintes erros:</h4>
          <ul>
            <?php foreach ($errors as $error): ?>
              <li><?php echo htmlspecialchars($error); ?></li>
            <?php endforeach; ?>
          </ul>
        </div>
      <?php endif; ?>
      
      <?php if ($success === '1' && !empty($message)): ?>
        <div class="alert alert-success">
          <h4><i class="fas fa-check-circle"></i> Sucesso!</h4>
          <p><?php echo htmlspecialchars($message); ?></p>
          <p>Você pode fazer login <a href="../login.php" style="color: #155724; font-weight: bold;">clicando aqui</a>.</p>
        </div>
      <?php endif; ?>

      <form name="form1" method="post" class="register-form" action="../../registration.php" onsubmit="return validateForm()">
        <div class="register-container">
          <div class="register-header">
            <h1 class="register-main-title">
              <i class="fas fa-user-plus"></i>
              CADASTRO
            </h1>
            <p class="register-subtitle">Crie sua conta para acessar o SEMA</p>
          </div>

          <div class="form-content">
            <div class="form-group">
              <label for="username" class="form-label">
                <i class="fas fa-user"></i>
                Usuário
              </label>
              <input
                type="text"
                id="username"
                class="form-input"
                placeholder="Digite seu username (3-50 caracteres)"
                name="txt_username"
                value="<?php echo htmlspecialchars($old_data['username'] ?? ''); ?>"
                pattern="[A-Za-z0-9_]{3,50}"
                title="Username deve conter apenas letras, números e underscore (3-50 caracteres)"
                required
              />
              <small class="email-hint">Apenas letras, números e _ (underline)</small>
            </div>

            <div class="form-row">
              <div class="form-group">
                <label for="nome" class="form-label">
                  <i class="fas fa-signature"></i>
                  Nome
                </label>
                <input
                  type="text"
                  id="nome"
                  class="form-input"
                  placeholder="Digite o seu nome"
                  name="txt_nome"
                  value="<?php echo htmlspecialchars($old_data['nome'] ?? ''); ?>"
                  pattern="[A-Za-zÀ-ÿ\s]{2,100}"
                  title="Nome deve conter apenas letras (2-100 caracteres)"
                  required
                />
              </div>

              <div class="form-group">
                <label for="sobrenome" class="form-label">
                  <i class="fas fa-signature"></i>
                  Sobrenome
                </label>
                <input
                  type="text"
                  id="sobrenome"
                  class="form-input"
                  placeholder="Digite o seu sobrenome"
                  name="txt_sobrenome"
                  value="<?php echo htmlspecialchars($old_data['sobrenome'] ?? ''); ?>"
                  pattern="[A-Za-zÀ-ÿ\s]{2,100}"
                  title="Sobrenome deve conter apenas letras (2-100 caracteres)"
                  required
                />
              </div>
            </div>

            <div class="form-group">
              <label for="email" class="form-label">
                <i class="fas fa-envelope"></i>
                Email
              </label>
              <input
                type="email"
                id="email"
                class="form-input"
                placeholder="Digite o seu Email (ex: usuario@gmail.com)"
                name="txt_email"
                value="<?php echo htmlspecialchars($old_data['email'] ?? ''); ?>"
                pattern="[a-z0-9._%+-]+@(gmail\.com|outlook\.com|hotmail\.com|yahoo\.com|icloud\.com|protonmail\.com|live\.com|msn\.com)$"
                title="Use Gmail, Outlook, Hotmail, Yahoo, iCloud ou ProtonMail"
                required
              />
              <small class="email-hint">Aceitamos: Gmail, Outlook, Hotmail, Yahoo, iCloud, ProtonMail</small>
            </div>

            <div class="form-row">
              <div class="form-group">
                <label for="password" class="form-label">
                  <i class="fas fa-lock"></i>
                  Senha
                </label>
                
                <div class="password-container">
                  <input
                    type="password"
                    id="password"
                    class="form-input"
                    placeholder="Digite a sua senha"
                    name="txt_password"
                    minlength="6"
                    required
                    oninput="checkPasswordStrength()"
                  />
                  
                  <button type="button" class="password-toggle" id="passwordToggle" aria-label="Mostrar senha">
                    <i class="fas fa-eye"></i>
                    <i class="fas fa-eye-slash"></i>
                  </button>
      
                </div>
                <div id="passwordStrength" class="password-strength"></div>
                <small class="email-hint">No mínimo 6 caracteres</small>
              </div>

              <div class="form-group">
                <label for="confirmPassword" class="form-label">
                  <i class="fas fa-lock"></i>
                  Confirmar Senha
                </label>
                <div class="password-container">
                  <input
                    type="password"
                    id="confirmPassword"
                    class="form-input"
                    placeholder="Sua senha novamente"
                    name="txt_confirmPassword"
                    minlength="6"
                    required
                  />
                  <button type="button" class="password-toggle" id="confirmPasswordToggle" aria-label="Mostrar senha">
                    <i class="fas fa-eye"></i>
                    <i class="fas fa-eye-slash"></i>
                  </button>
                </div>
                <div id="passwordMatch" class="password-strength"></div>
              </div>
            </div>

            <div class="form-actions">
              <button type="button" class="btn-clean" onclick="clean();">
                <i class="fas fa-broom"></i>
                LIMPAR
              </button>

              <button type="submit" class="btn-submit" name="bt_incluir" value="CADASTRAR">
                <i class="fas fa-paper-plane"></i>
                ENVIAR
              </button>
            </div>
          </div>

          <div class="login-redirect">
            <p>Já possui uma conta?</p>
            <a href="../login.php" class="login-link">
              <i class="fas fa-sign-in-alt"></i>
              Fazer login
            </a>
          </div>
        </div>
      </form>
    </div>

    <!-- Footer Padronizado -->
    <div class="footer">
      <div class="staff-information">
        <p>Ainda não nos conhece?</p>
        <a class="central-link" href="../sub_links/about_us.php">sobre nós</a>
      </div>

      <div class="social_midias">
        <p class="staff-information">Nossas redes sociais</p>

        <div class="icons">
          <a href="https://www.instagram.com/elobos.acolhe?igsh=ZDE5N2F5ODVoY2pj">
            <img id="images" src="../../images/icons/INSTA.webp" alt="Instagram">
          </a>

          <a href="https://x.com/ellobos675443">
            <img id="images" src="../../images/icons/xTWT.avif" alt="Twitter">
          </a>

          <a href="https://www.youtube.com/@ellobos-n8n">
            <img id="images2" src="../../images/icons/YOUYOU2.png" alt="YouTube">      
          </a>
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
        
        if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
          return 'dark';
        }
        
        return 'light';
      }
      
      function applyTheme(theme) {
        if (theme === 'dark') {
          body.setAttribute('data-theme', 'dark');
        } else {
          body.removeAttribute('data-theme');
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

      // ===== SISTEMA DE MOSTRAR/OCULTAR SENHA =====
      function setupPasswordToggle(passwordInputId, toggleButtonId) {
        const passwordToggle = document.getElementById(toggleButtonId);
        const passwordInput = document.getElementById(passwordInputId);
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
        
        // Configurar ícones de senha inicialmente
        eyeIcon.style.display = 'inline-block';
        eyeSlashIcon.style.display = 'none';
      }
      
      // ===== VALIDAÇÃO DE FORÇA DA SENHA =====
      function checkPasswordStrength() {
        const password = document.getElementById('password').value;
        const strengthText = document.getElementById('passwordStrength');
        const confirmPassword = document.getElementById('confirmPassword').value;
        const matchText = document.getElementById('passwordMatch');
        
        if (password.length === 0) {
          strengthText.innerHTML = '';
          strengthText.className = 'password-strength';
          return;
        }
        
        let strength = 0;
        let tips = [];
        
        // Verificar comprimento
        if (password.length < 6) {
          tips.push('Mínimo 6 caracteres');
        } else if (password.length < 8) {
          strength += 1;
        } else {
          strength += 2;
        }
        
        // Verificar letras maiúsculas e minúsculas
        if (/[a-z]/.test(password)) strength += 1;
        if (/[A-Z]/.test(password)) strength += 1;
        
        // Verificar números
        if (/[0-9]/.test(password)) strength += 1;
        
        // Verificar caracteres especiais
        if (/[^A-Za-z0-9]/.test(password)) strength += 1;
        
        // Classificar força
        let strengthClass, strengthMessage;
        if (strength < 3) {
          strengthClass = 'strength-weak';
          strengthMessage = 'Senha fraca';
          tips.push('Adicione letras maiúsculas, números ou símbolos');
        } else if (strength < 5) {
          strengthClass = 'strength-medium';
          strengthMessage = 'Senha média';
        } else {
          strengthClass = 'strength-strong';
          strengthMessage = 'Senha forte';
        }
        
        // Atualizar exibição
        strengthText.innerHTML = `<span class="${strengthClass}">${strengthMessage}</span>`;
        strengthText.className = `password-strength ${strengthClass}`;
        
        // Verificar se as senhas coincidem
        if (confirmPassword.length > 0) {
          if (password === confirmPassword) {
            matchText.innerHTML = '<span class="strength-strong">✓ Senhas coincidem</span>';
            matchText.className = 'password-strength strength-strong';
          } else {
            matchText.innerHTML = '<span class="strength-weak">✗ Senhas não coincidem</span>';
            matchText.className = 'password-strength strength-weak';
          }
        }
      }
      
      // Verificar correspondência de senha em tempo real
      document.getElementById('confirmPassword').addEventListener('input', checkPasswordStrength);
      
      // ===== VALIDAÇÃO DO FORMULÁRIO =====
      function validateForm() {
        const username = document.getElementById('username').value;
        const nome = document.getElementById('nome').value;
        const sobrenome = document.getElementById('sobrenome').value;
        const email = document.getElementById('email').value;
        const password = document.getElementById('password').value;
        const confirmPassword = document.getElementById('confirmPassword').value;
        
        // Lista de domínios permitidos
        const allowedDomains = [
          'gmail.com', 'outlook.com', 'hotmail.com', 
          'yahoo.com', 'icloud.com', 'protonmail.com',
          'live.com', 'msn.com'
        ];
        
        // Validação do username
        if (username.length < 3 || username.length > 50) {
          alert('O username deve ter entre 3 e 50 caracteres');
          return false;
        }
        
        if (!/^[A-Za-z0-9_]+$/.test(username)) {
          alert('O username deve conter apenas letras, números e underscore (_)');
          return false;
        }
        
        // Validação do nome e sobrenome
        if (!/^[A-Za-zÀ-ÿ\s]{2,100}$/.test(nome)) {
          alert('O nome deve conter apenas letras (2-100 caracteres)');
          return false;
        }
        
        if (!/^[A-Za-zÀ-ÿ\s]{2,100}$/.test(sobrenome)) {
          alert('O sobrenome deve conter apenas letras (2-100 caracteres)');
          return false;
        }
        
        // Validação do email
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(email)) {
          alert('Por favor, insira um email válido');
          return false;
        }
        
        // Extrair domínio do email
        const emailParts = email.split('@');
        if (emailParts.length !== 2) {
          alert('Formato de email inválido');
          return false;
        }
        
        const domain = emailParts[1].toLowerCase();
        if (!allowedDomains.includes(domain)) {
          alert('Por favor, use um email dos seguintes provedores:\n\n' +
                '• Gmail (@gmail.com)\n' +
                '• Outlook (@outlook.com)\n' +
                '• Hotmail (@hotmail.com)\n' +
                '• Yahoo (@yahoo.com)\n' +
                '• iCloud (@icloud.com)\n' +
                '• ProtonMail (@protonmail.com)');
          return false;
        }
        
        // Verificar emails temporários
        const tempDomains = ['mailinator.com', 'tempmail.com', 'guerrillamail.com', '10minutemail.com'];
        if (tempDomains.includes(domain)) {
          alert('Emails temporários não são permitidos');
          return false;
        }
        
        // Validação da senha
        if (password.length < 6) {
          alert('A senha deve ter no mínimo 6 caracteres');
          return false;
        }
        
        if (password.length > 72) {
          alert('A senha deve ter no máximo 72 caracteres');
          return false;
        }
        
        // Verificar se as senhas coincidem
        if (password !== confirmPassword) {
          alert('As senhas não coincidem');
          return false;
        }
        
      }
      
      // ===== FUNÇÃO LIMPAR FORMULÁRIO =====
      function clean() {
        if (confirm('Tem certeza que deseja limpar todos os campos?')) {
          document.querySelector('.register-form').reset();
          document.getElementById('passwordStrength').innerHTML = '';
          document.getElementById('passwordMatch').innerHTML = '';
        }
      }
      
      // Inicializar quando o DOM estiver carregado
      document.addEventListener('DOMContentLoaded', function() {
        initTheme();
        setupPasswordToggle('password', 'passwordToggle');
        setupPasswordToggle('confirmPassword', 'confirmPasswordToggle');
        
        // Verificar força da senha inicialmente (se houver valor)
        if (document.getElementById('password').value) {
          checkPasswordStrength();
        }
      });
    </script>
  </body>
</html>