<!DOCTYPE html>
<html lang="pt-BR">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SEMA - Orientações</title>
    <link rel="stylesheet" href="../css/orientation.css">
    <link rel="icon" href="../images/icon-site.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
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
      include '../server.php';
      require_once '../auth.php';

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
                  // Adicionar "../" porque orientations.php está na pasta html
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
      <div class="content-orientarion">
        <div class="title-countainer">          
           <h1 class="align-orientaion-tltle">ORIENTAÇÕES</h1>          
        </div>
        
        <!-- Quadro branco centralizado -->
        <div class="white-frame">
          <!-- Sumário -->
          <div class="summary-container">
            <h2 class="summary-title">Sumário</h2>
            <div class="summary-list">
              <div class="summary-item">
                <a href="#sobre" class="summary-link">1. Do que se trata?</a>
              </div>
              <div class="summary-item">
                <a href="#lidar-situacao" class="summary-link">2. Como lidar com a situação?</a>
              </div>
              <div class="summary-item">
                <a href="#situacoes-especificas" class="summary-link">3. Situações específicas</a>
              </div>
              <div class="summary-item">
                <a href="#prevencao-incendio" class="summary-link">4. Prevenção doméstica contra incêndios</a>
              </div>
              <div class="summary-item">
                <a href="#medidas-essenciais" class="summary-link">5. Medidas essenciais de controle de risco</a>
              </div>
              <div class="summary-item">
                <a href="#prevencao-ambiente" class="summary-link">6. Prevenção por ambiente</a>
              </div>
              <div class="summary-item">
                <a href="#hierarquia-riscos" class="summary-link">7. Hierarquia de Controle de Riscos</a>
              </div>
            </div>
          </div>
          
          <!-- Conteúdo das orientações -->
          <div class="divede-content">
            <div class="text-orientation-content">
              <section id="sobre">
                <p id="little-title" class="paragraph-two">
                  Do que se trata?
                </p>

                <p class="paragraph-two">
                  Saber sobre o local e como agir em certas situações é crucial para sua segurança e aos
                  demais. Por isso criamos a área de "orientações", onde
                  temos como objetivo orientar como reagir a situações de risco
                  ao usuário e aos demais.
                </p>
              </section>
              
              <section id="lidar-situacao">
                <p id="little-title" class="paragraph-two">
                  Como lidar com a situação?
                </p>

                <p class="paragraph-two">
                  Isso depende de situação para situação, mas em todos os casos
                  certas medidas são sempre eficazes, alguns exemplos são:
                </p>

                <p id="micro-text" class="paragraph-two">
                  1. Manter a calma
                </p>

                <p id="micro-text" class="paragraph-two">
                  2. Avaliar a situação
                </p>
                
                <p id="micro-text" class="paragraph-two">
                  3. Proteger-se
                </p>
               
                <p id="micro-text" class="paragraph-two">
                  4. Buscar auxiliar os proxímos
                </p>
                
                <p id="micro-text" class="paragraph-two">
                  5. Avisar as autoridades
                </p>
               
                <p id="micro-text" class="paragraph-two">
                  6. Primeiros socorros
                </p>
              
                <p id="micro-text" class="paragraph-two">
                  7. Se manter o foco
                </p>

                <p class="paragraph-two">
                  Seguir esses passos podem ser ser a chave para sua segurança
                  e aos demais em sua volta.
                </p>
              </section>
              
              <section id="situacoes-especificas">
                <p id="little-title2">
                  Situações específicas, ações específicas
                </p>

                <p class="paragraph-two">
                  Cada situação requer certos procedimentos a serem seguidos
                  para maximizar as chances de sucesso, tendo isso em mente,
                  seguem abaixo algumas orientações para situações de
                  risco específicas.
                </p>

                <p class="paragraph-two">
                  <span class="events">Incêndio:</span> tentar sair do local a todo custo; usar escadas em
                  vez de elevadores; colocar um pano húmido sobre o nariz;
                  derramar água sobre as roupas para o fogo não pegar nelas;
                  se manter abaixo da fumaça; manter o controle da respiração
                  pelo nariz em rapida inalações.
                </p>

                <p class="paragraph-two">
                  <span class="events">Terremoto:</span> siga o lema "abaixar, cobrir e segurar"; se manter
                  agachado para diminuir sua área de exposição, você deve
                  se agachar de forma a permitir sua própria movimentação;
                  procurar uma estrutura resistente para ficar em baixo, lugares
                  como debaixo de portas e em baixo de mesas são boas opções;
                  sempre se manter longe de janelas e sacadas.   
                </p>

                <p class="paragraph-two">
                  <span class="events">Deslizamentos:</span> se afastar imediatamente da área de risco;
                  Se afastar de estruturas com fendas e rachaduras; esteja em
                  alerta sobre os boletins meteorológicos; Afastar-se do local;
                  Informe a Defesa Civil e ao Corpo de Bombeiros sobre o
                  deslizamento; Ajude a alertar outras pessoas que possam
                  estar em risco.
                </p>

                <p class="paragraph-two">
                  <span class="events">Inundações:</span> se estiver em lugar instável, o abandone o mais
                   rapido possível; proteja seus pertences colocando-os em lugares
                   eleveados; evite atravessar áreas alagadas, espere a água abaixar.
                </p>
              </section>
            </div>
            
            <div class="coloum-img">             
              <img class="image-content" src="../images/orientacoes_teste_2.jpg" alt="Imagem de orientações">
              <img class="image-content2" src="../images/teste de barra de pesquisa 3.jpeg" alt="Imagem de pesquisa">            
            </div>          
          </div>
          
          <div class="section-divider"></div>
          
          <div class="divede-content2">
            <div class="text-orientation-content2">
              <section id="prevencao-incendio">
                <p id="little-title" class="paragraph-two">
                  Prevenção doméstica contra incêndios
                </p>

                <p class="paragraph-two">
                  A prevenção de incêndios em residências é fundamental para proteger
                  vidas e patrimônio. Com medidas simples e vigilância constante,
                  grande parte dos acidentes pode ser evitada. Conhecer práticas
                  preventivas transforma moradores em agentes ativos da segurança.
                </p>
              </section>
              
              <section id="medidas-essenciais">
                <p id="little-title" class="paragraph-two">
                  Medidas essenciais de controle de risco
                </p>

                <p class="paragraph-two">
                  Estas ações reduzem significativamente as chances de incêndio: instale detectores de fumaça em todos os cômodos;
                  Nunca sobrecarregue tomadas com benjamins;
                  mantenha extintores tipo ABC em pontos estratégicos; Verifique fiações elétricas regularmente
                  desligue aparelhos eletrônicos ao dormir e sempre verifique fiações elétricas regularmente
                </p>

                <p class="paragraph-two">
                  Adotar essas práticas diariamente cria um ambiente
                  mais seguro para toda a família.
                </p>
              </section>
              
              <section id="prevencao-ambiente">
                <p id="little-title2">
                  Prevenção por ambiente
                </p>

                <p class="paragraph-two">
                  Cada área da casa exige cuidados específicos:
                </p>

                <p class="paragraph-two">
                  <span class="events">Cozinha:</span> Nunca deixe panelas no fogão sem supervisão;
                  Mantenha cortinas e panos longe do fogão; Limpe regularmente
                  o exaustor para evitar acúmulo de gordura.
                </p>

                <p class="paragraph-two">
                  <span class="events">Área de serviço:</span> Verifique vedações de botijões de gás
                  com água e sabão; Não armazene produtos químicos próximos
                  ao aquecedor; Mantenha ventilação adequada.
                </p>

                <p class="paragraph-two">
                  <span class="events">Sala e quartos:</span> Evite velas próximas a cortinas ou móveis;
                  Não cubra lâmpadas com tecidos; Desligue aquecedores portáteis
                  antes de dormir; Utilize reguladores de voltagem em equipamentos.
                </p>

                <p class="paragraph-two">
                  <span class="events">Garagem/Depósito:</span> Armazene combustíveis em recipientes
                  adequados e longe de fontes de calor; Mantenha ferramentas
                  elétricas desligadas da tomada; Organize materiais evitando
                  pilhas próximas a painéis elétricos.
                </p>
              </section>
              
              <section id="hierarquia-riscos">
                <p id="little-title2_2">
                  Hierarquia de Controle de Riscos
                </p>

                <p class="paragraph-two">
                  A hierarquia de controle de riscos estabelece uma ordem
                  prioritária de medidas para eliminar ou reduzir perigos.
                  Esta abordagem sistemática deve ser aplicada sequencialmente
                  para máxima eficácia na prevenção de acidentes.
                </p>

                <p id="little-title2_3">
                  Hierarquia a Ser Seguida
                </p>

                <p class="paragraph-two">
                  <span class="events">1. Eliminação:</span> Remover completamente o perigo do ambiente.
                  Exemplo: Eliminar produtos inflamáveis desnecessários da residência.
                </p>

                <p class="paragraph-two">
                  <span class="events">2. Substituição:</span> Trocar materiais perigosos por alternativas seguras.
                  Exemplo: Substituir produtos químicos tóxicos por versões ecológicas.
                </p>

                <p class="paragraph-two">
                  <span class="events">3. Controles de Engenharia:</span> Implementar barreiras físicas de proteção.
                  Exemplo: Instalar dispositivos automáticos de corte de energia em circuitos.
                </p>

                <p class="paragraph-two">
                  <span class="events">4. Sinalização e Bloqueio:</span> Identificar perigos e restringir acesso.
                  Exemplo: Sinalizar áreas de risco e utilizar bloqueios em painéis elétricos.
                </p>

                <p class="paragraph-two">
                  <span class="events">5. EPI (Equipamento de Proteção Individual):</span> Última linha de defesa quando
                  os riscos não podem ser totalmente eliminados. Exemplo: Utilizar luvas térmicas
                  e óculos de proteção ao manusear materiais perigosos.
                </p>
              </section>
            </div> 

            <div class="coloum-img">             
              <img class="image-content" src="../images/incendio.jpg" alt="Imagem de incêndio">
              <img class="image-content2" src="../images/importancia.jpg" alt="Imagem de importância">            
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