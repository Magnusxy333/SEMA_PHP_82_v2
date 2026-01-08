<!DOCTYPE html>
<html lang="pt-br">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>SEMA - Doações</title>
    <link rel="stylesheet" href="styles/mobile.css" />
    <link rel="stylesheet" href="../../css/help-styles.css" />
    <link rel="icon" href="../../images/icon-site.png" />
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
    />
    
    <!-- TEMA INSTANTÂNEO - Carrega antes do CSS para evitar flicker -->
    <script>
        (function() {
            try {
                const savedTheme = localStorage.getItem('theme');
                const prefersDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
                
                let theme = 'light';
                if (savedTheme) {
                    theme = savedTheme;
                } else if (prefersDark) {
                    theme = 'dark';
                }
                
                document.documentElement.setAttribute('data-theme', theme);
            } catch(e) {
                document.documentElement.setAttribute('data-theme', 'light');
            }
        })();
    </script>
  </head>
  <body>

  <?php
      session_start();
      include '../../server.php';
      require_once '../../auth.php';

      // ----------------------------------------------------------------------
      // ✅ LÓGICA DA FOTO DE PERFIL NO HEADER (IGUAL AO INDEX.PHP)
      // ----------------------------------------------------------------------
      $profile_photo_url = null; 

      if (isLoggedIn() && isset($_SESSION['username'])) {
          // CORREÇÃO: Usar prepared statement
          $stmt = $conecta_db->prepare("SELECT profile_picture_url FROM tb_register WHERE username = ?");
          $stmt->bind_param("s", $_SESSION['username']);
          $stmt->execute();
          $result = $stmt->get_result();
          
          if ($result && $linha = $result->fetch_assoc()) {
              $db_photo_url = $linha['profile_picture_url'];
              
              if (!empty($db_photo_url) && str_contains((string)$db_photo_url, 'uploads/')) {
                  // Adicionar "../../" porque help.php está na pasta html/sub_links
                  $profile_photo_url = '../../' . $db_photo_url; 
              } 
          }
          $stmt->close();
      }
    ?>

    <div class="header">
      <div class="left">
        <a href="../../index.php">
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
          <span>HOME</span>                     
        </a>

        <a class="areas-text" href="../location.php">
          <i class="fas fa-map-marker-alt"></i>
          <span>LOCALIZAÇÃO</span>
        </a>

        <a class="areas-text" href="../orientations.php">
          <i class="fas fa-book-open"></i>
          <span>ORIENTAÇÕES</span>
        </a>

        <a class="areas-text" href="../contacts.php">
          <i class="fas fa-phone-alt"></i>
          <span>CONTATOS</span>
        </a>

        <?php if (isLoggedIn()): ?>
          <a class="areas-text" href="../profile.php">
            <?php if ($profile_photo_url): ?>
              <img 
                src="<?php echo htmlspecialchars((string)$profile_photo_url); ?>" 
                alt="Foto de Perfil" 
                style="width: 38px; height: 38px; border-radius: 50%; object-fit: cover; margin-right: 5px; vertical-align: middle;"
              >
            <?php else: ?>
              <i class="fas fa-user-circle"></i>
            <?php endif; ?>
            <span>PERFIL</span>
          </a> 
        <?php else: ?>
          <a class="areas-text" href="../login.php">
            <i class='fas fa-sign-in-alt' id="login-size"></i>
          </a>
        <?php endif; ?>
      </div>
    </div>  

    <!---------------------------------->

    <div class="main-help">
      <div class="top-bottom">
        <div class="align-center">
          <h1 class="main-title">Por que Doar para o SEMA?</h1>

          <p>
            O <b>SEMA (Sistema de Emergencial Mauá)</b> é mais do que um site é
            uma ponte vital entre pessoas em situação de risco e os recursos
          </p>

          <p>
            que podem salvar vidas. Ao doar, você se torna parte dessa rede de
            proteção.
          </p>

          <h2 class="main-title">Como Doar?</h2>
          <p>
            Copie o código pix ou escaneie o QR code abaixo. Qualquer valor
            transforma realidades.
          </p>

          <div class="pix-code">
            <br />
            <img id="pix-qrcode" src="../../images/pix.png" alt="Qr code PIX" />
            <br />
          </div>
          <div class="content-pix">
            <button class="botao-pix">
              <span id="code"> 0ff5feeb-ba87-48de-b21a-954cb9357272 </span>
            </button>
          </div>
          <p class="epigrafe-help">
            <i>
              "Sua generosidade não é um gasto — é um legado de segurança e
              esperança."
            </i>
          </p>
        </div>
      </div>
    </div>

    <!---------------------------------->

    <div class="footer">
      <div class="staff-information">
        <p>Ainda não nos conhece?</p>
        <a class="central-link" href="about_us.php">sobre nós</a>
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

    <!---------------------------->

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

      // Copiar código PIX
      document.getElementById("code").addEventListener("click", async () => {
        const text = "0ff5feeb-ba87-48de-b21a-954cb9357272";
        try {
          await navigator.clipboard.writeText(text);
          alert("Código pix copiado!");
        } catch (err) {
          console.error("Falha ao copiar: ", err);
        }
      });
    </script>
  </body>
</html>