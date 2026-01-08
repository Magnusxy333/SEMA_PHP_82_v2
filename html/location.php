<!DOCTYPE html>
<html lang="pt-BR">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SEMA - Localização</title>
    <link rel="icon" href="../images/icon-site.png">
    <link rel="stylesheet" href="../css/location3.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- TEMA INSTANTÂNEO - Igual ao index.php -->
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
      include '../server.php';
      require_once '../auth.php';

      // ----------------------------------------------------------------------
      // ✅ LÓGICA DA FOTO DE PERFIL NO HEADER (IGUAL AO INDEX.PHP)
      // ----------------------------------------------------------------------
      $profile_photo_url = null; 
      $img_style_header = "style='width: 38px; height: 38px; border-radius: 50%; object-fit: cover; margin-right: 5px; vertical-align: middle;'";

      if (isLoggedIn() && isset($_SESSION['username'])) {
          // CORREÇÃO: Usar prepared statement
          $stmt = $conecta_db->prepare("SELECT profile_picture_url FROM tb_register WHERE username = ?");
          $stmt->bind_param("s", $_SESSION['username']);
          $stmt->execute();
          $result = $stmt->get_result();
          
          if ($result && $linha = $result->fetch_assoc()) {
              $db_photo_url = $linha['profile_picture_url'];
              
              if (!empty($db_photo_url) && str_contains((string)$db_photo_url, 'uploads/')) {
                  // Adicionar "../" porque location.php está na pasta html
                  $profile_photo_url = '../' . $db_photo_url; 
              } 
          }
          $stmt->close();
      }
    ?>
           
    <div class="header">
      <div class="left">
        <a href="../index.php">
          <img class="icon" src="../images/sema.png" alt="icon">
        </a>
      </div>

      <div class="right">
        <!-- Botão de alternância de tema -->
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

        <?php if (isLoggedIn()): ?>
          <a class="areas-text" href="profile.php">
            <?php if ($profile_photo_url): ?>
              <img 
                src="<?php echo htmlspecialchars((string)$profile_photo_url); ?>" 
                alt="Foto de Perfil" 
                style="width: 38px; height: 38px; border-radius: 50%; object-fit: cover; vertical-align: middle;"
              >
            <?php else: ?>
              <i class="fas fa-user-circle"></i>
            <?php endif; ?>
            <span>PERFIL</span>
          </a> 
        <?php else: ?>
          <a class="areas-text" href="login.php">
            <i class='fas fa-sign-in-alt' id="login-size"></i>
          </a>
        <?php endif; ?>
      </div>
    </div>  
  
    <!---------------------------------->

    <div class="main">
      <div class="center">
        <div class="container">
          <header>
            <h1><i class="fas fa-map-marked-alt"></i> Localização e Navegação</h1>
            <p class="subtitle">Encontre sua localização atual e busque endereços com facilidade</p>
          </header>
            
          <div class="content">
            <!-- Seção de Localização Atual -->
            <div class="section">
              <h2><i class="fas fa-location-crosshairs"></i> Sua Localização Atual</h2>
              <p>Clique no botão abaixo para obter suas coordenadas geográficas atuais. Esta funcionalidade é essencial para situações de emergência.</p>
              
              <div class="location-controls">
                <button id="getLocation" class="btn">
                  <i class="fas fa-map-marker-alt"></i> Obter Minha Localização
                </button>
                
                <div id="locationStatus" class="status">
                  <i class="fas fa-info-circle"></i> Clique no botão para obter sua localização
                </div>
              </div>
              
              <div id="locationData" class="location-data" style="display: none;">
                <div class="data-grid">
                  <div class="data-item">
                    <div class="data-label">Latitude:</div>
                    <div id="latitude" class="data-value">-</div>
                  </div>
                  <div class="data-item">
                    <div class="data-label">Longitude:</div>
                    <div id="longitude" class="data-value">-</div>
                  </div>
                  <div class="data-item">
                    <div class="data-label">Precisão:</div>
                    <div id="accuracy" class="data-value">-</div>
                  </div>
                </div>
              </div>
            </div>
            
            <!-- Seção do Mapa -->
            <div class="section">
              <h2><i class="fas fa-map"></i> Visualização do Mapa</h2>
              <p>Visualize sua localização no mapa. Em situações de emergência, compartilhar estas coordenadas pode ser crucial.</p>
              <div id="map" class="map-container">
                <div class="map-placeholder">
                  <i class="fas fa-map-location-dot"></i>
                  <p>O mapa será exibido aqui após obter a localização</p>
                </div>
              </div>
            </div>
            
            <!-- Seção de Busca de Endereço -->
            <div class="section">
              <h2><i class="fas fa-search-location"></i> Buscar Endereço</h2>
              <p>Encontre a localização de qualquer endereço. Digite o endereço completo para obter as coordenadas.</p>
              
              <div class="search-container">
                <div class="search-box">
                  <input type="text" id="addressInput" placeholder="Digite um endereço completo (ex: Avenida Paulista, 1000 - São Paulo, SP)">
                  <button id="searchAddress" class="btn search-btn">
                    <i class="fas fa-search"></i> Buscar
                  </button>
                </div>
                
                <div id="addressData" class="location-data" style="display: none;">
                  <div class="data-grid">
                    <div class="data-item">
                      <div class="data-label">Endereço:</div>
                      <div id="formattedAddress" class="data-value">-</div>
                    </div>
                    <div class="data-item">
                      <div class="data-label">Coordenadas:</div>
                      <div id="searchedCoords" class="data-value">-</div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

   <!---------------------------------->
  
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
    <script src="scripts/script.js"></script>
    <script src="../js/location.js"></script>
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
  </body>
</html>
