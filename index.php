<?php
// 1. Inicie a sessão no topo absoluto do arquivo
session_start();

// 2. Inclua o arquivo de conexão
include 'server.php'; 
require_once 'auth.php';

// ----------------------------------------------------------------------
// ✅ LÓGICA DA FOTO DE PERFIL NO HEADER (COM PREPARED STATEMENTS)
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
            $profile_photo_url = $db_photo_url; 
        } 
    }
    $stmt->close();
}

// ----------------------------------------------------------------------
// ✅ BUSCAR RESULTADOS DOS FORMULÁRIOS (COM PREPARED STATEMENTS)
// ----------------------------------------------------------------------
$ultima_satisfacao = null;
$ultimo_quiz = null;

if (isLoggedIn() && isset($_SESSION['user_id'])) {
    // CORREÇÃO: Usar prepared statements
    $user_id = $_SESSION['user_id'];
    
    // Buscar último resultado do formulário de satisfação
    $stmt_satisfacao = $conecta_db->prepare("SELECT satisfacao_geral FROM tb_form_satisfacao WHERE user_id = ? ORDER BY data_preenchimento DESC LIMIT 1");
    $stmt_satisfacao->bind_param("i", $user_id);
    $stmt_satisfacao->execute();
    $result_satisfacao = $stmt_satisfacao->get_result();
    
    if ($result_satisfacao && $result_satisfacao->num_rows > 0) {
        $ultima_satisfacao = $result_satisfacao->fetch_assoc();
    }
    $stmt_satisfacao->close();
    
    // Buscar último resultado do quiz de segurança
    $stmt_quiz = $conecta_db->prepare("SELECT pontuacao, total_questoes, percentual FROM tb_quiz_seguranca WHERE user_id = ? ORDER BY data_realizacao DESC LIMIT 1");
    $stmt_quiz->bind_param("i", $user_id);
    $stmt_quiz->execute();
    $result_quiz = $stmt_quiz->get_result();
    
    if ($result_quiz && $result_quiz->num_rows > 0) {
        $ultimo_quiz = $result_quiz->fetch_assoc();
    }
    $stmt_quiz->close();
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SEMA</title>
    <link rel="icon" href="images/icon-site.png">
    <link rel="stylesheet" href="styles/mobile-styles/mobile.css">
    <link rel="stylesheet" href="css/index.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- TEMA INSTANTÂNEO - Carregadsa antes do CSS para evitar flicker -->
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
    
    <div class="header">
        <div class="left">
            <a href="index.php">
                <img class="icon" src="images/sema.png" alt="icon">
            </a>
        </div>

        <div class="right">
            <!-- Botão de alternância de tema -->
            <button class="theme-toggle" id="themeToggle" aria-label="Alternar tema">
                <i class="fas fa-sun"></i>
                <i class="fas fa-moon"></i>
            </button>
      
            <a class="areas-text" href="index.php">                    
                <i class="fas fa-house-user"></i>                   
                <span>HOME</span>                     
            </a>

            <a class="areas-text" href="html/location.php">
                <i class="fas fa-map-marker-alt"></i>
                <span>LOCALIZAÇÃO</span>
            </a>

            <a class="areas-text" href="html/orientations.php">
                <i class="fas fa-book-open"></i>
                <span>ORIENTAÇÕES</span>
            </a>

            <a class="areas-text" href="html/contacts.php">
                <i class="fas fa-phone-alt"></i>
                <span>CONTATOS</span>
            </a>

            <?php if (isLoggedIn()): ?>
                <a class="areas-text" href="html/profile.php">
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
                <a class="areas-text" href="html/login.php">
                    <i class='fas fa-sign-in-alt' id="login-size"></i>
                </a>
            <?php endif; ?>
        </div>
    </div>
  
    <div class="main">
        <img class="banner-test" src="images/banner-home.jpg" alt="banner">

        <?php if (isAdmin()): ?>
            <a href="html/listagemAdm.php">
                <img class="btn-listagem-teste" src="images/icons/planilha4.png"/>
            </a>
        <?php endif; ?>

        <div class="main-content-position">
            <h1 class="title-home">Nossos Objetivos</h1>
            <div class="text-main-div">
                <p class="text-main">Temos como missão fornecer assistências diversas em situações de risco.</p>
                <p class="text-main">Auxílio imediato através de nossa plataforma web.</p>
            </div>
        </div>

        <div class="formulario">
            <h1 class="title-home">O que você acha dos serviços públicos de Mauá?</h1>
            <div class="formulario-row">
                <div class="card1">
                    <p class="text-form">O que você acha dos serviços municipais?</p>
                    <ul class="text-list">
                        <li>qualidade e pontualidade do transporte público.</li>
                        <li>Iluminação pública.</li>
                        <li>Manutenção e conservação das ruas e calçadas.</li>
                        <li>Satisfação com os recursos públicos em Mauá (SUS, ônibus, etc...).</li>
                    </ul>

                    <?php if (isLoggedIn()): ?>
                        <a href="html/avaliantionForm.php">
                            <button class="botao-formulario">
                                <?php echo ($ultima_satisfacao) ? 'Refazer Pesquisa' : 'Responda aqui!'; ?>
                            </button>
                        </a>
                        <?php if ($ultima_satisfacao): ?>
                            <p class="text-form" style="margin-top:10px; background:lightblue; padding:8px; border-radius:5px; color:black;">
                                <strong>Último resultado:</strong> <?php echo htmlspecialchars((string)$ultima_satisfacao['satisfacao_geral']); ?>
                            </p>
                        <?php endif; ?>
                    <?php else: ?>
                        <a href="html/login.php"><button class="botao-formulario">Responda aqui!</button></a>
                        <p class="text-lock-form">( 🔒 Faça login para responder )</p>
                    <?php endif; ?>
                </div>

                <div class="card2">
                    <p class="text-form">Como você se sairia em uma situação de risco?</p>
                    <ul class="text-list">
                        <li>Identificação de perigos iminentes.</li>
                        <li>Primeiros socorros básicos.</li>
                        <li>Rotas de fuga e pontos de encontro.</li>
                        <li>Contato com serviços de emergência (SAMU, Bombeiros, Polícia).</li>
                    </ul>

                    <?php if (isLoggedIn()): ?>
                        <a href="html/challengeForm.php">
                            <button class="botao-formulario">
                                <?php echo ($ultimo_quiz) ? 'Refazer Teste' : 'Realize o teste'; ?>
                            </button>
                        </a>
                        <?php if ($ultimo_quiz): ?>
                            <p class="text-form" style="margin-top:10px; background:lightgreen; padding:8px; border-radius:5px; color:black;">
                                <strong>Acertos:</strong> <?php echo htmlspecialchars($ultimo_quiz['pontuacao']); ?>/<?php echo htmlspecialchars($ultimo_quiz['total_questoes']); ?> (<?php echo htmlspecialchars($ultimo_quiz['percentual']); ?>%)
                            </p>
                        <?php endif; ?>
                    <?php else: ?>
                        <a href="html/login.php"><button class="botao-formulario">Realize o teste</button></a>
                        <p class="text-lock-form">( 🔒 Faça login para responder )</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="donation">
        <h1 class="title-doacao">
          Quer ajudar o SEMA a continuar ativo?
        </h1>
      
        <a href="html/sub_links/help.php" class="buton">
          <button class="meu-botao">
            Faça sua doação !
          </button>
        </a>
        <div class="faca-equipe">
          <a href="html/sub_links/join_the_team.php" class="buton">
            <button class="meu-botao">
              &#10084; Nos ajude a melhorar &#10084;
            </button>
          </a>
        </div>

        <h1 class="title-conteudos">
          Conteúdos rápidos
        </h1>
    
      </div>

    <section class="videos"> 
        <button class="pre-btn"><img class="b" src="images/arrow-png.png" alt=""></button>
        <button class="nxt-btn"><img class="b" src="images/arrow-png.png" alt=""></button>
        <div class="videos-container">

          <div class="video-card" data-category="socorros">
            <div class="video-container">
              <iframe 
                width="460" 
                height="315" 
                src="https://www.youtube.com/embed/5MgBikgcWnY?si=bsdtDlR-XPZbMJ-T" 
                title="YouTube video player" 
                frameborder="0" 
                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" 
                referrerpolicy="strict-origin-when-cross-origin" 
                allowfullscreen>
              </iframe>
            </div>
            <div class="video-info">
                <h3 class="video-title">Como Fazer Reanimação Cardiopulmonar</h3>
                <p class="video-description">Passo a passo para realizar RCP corretamente em emergências.</p>
            </div>
          </div>

          <div class="video-card" data-category="socorros">
            <div class="video-container">
              <iframe 
                width="460" 
                height="315" 
                src="https://www.youtube-nocookie.com/embed/XIH8v579xDo?si=3IaMhjKn2UsGP-g2" 
                title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" 
                referrerpolicy="strict-origin-when-cross-origin" 
                allowfullscreen>
              </iframe>
            </div>
            <div class="video-info">
              <h3 class="video-title">Controle de Hemorragia a nível de Primeiros Socorros | O que fazer? (Aula prática)</h3>
              <p class="video-description">Passo a passo do que fazer em caso de uma hemorragia externa grave exsanguinante.</p>
            </div>
          </div>
          
          <div class="video-card" data-category="socorros">
            <div class="video-container">
              <iframe 
                width="460" 
                height="315" 
                src="https://www.youtube.com/embed/e2JIV58CppM?si=qX4nBEV-bdMqJ8DM" 
                title="YouTube video player" 
                frameborder="0" 
                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" 
                referrerpolicy="strict-origin-when-cross-origin" 
                allowfullscreen>
              </iframe>
            </div>
            <div class="video-info">
              <h3 class="video-title">⚠️ DEFESA PESSOAL - Simples Defesas que todos deveriam saber! 👊 💥 ➡️ 🙅</h3>
              <p class="video-description">Se alguém atacasse você ou sua família agora, você conseguiria defender?.</p>
            </div>
          </div>
          
          <div class="video-card" data-category="socorros">
            <div class="video-container">
              <iframe 
                width="460" 
                height="315" 
                src="https://www.youtube.com/embed/36P_5YtReAM?si=ZPXFHbrYQefvm_JR" 
                title="YouTube video player" 
                frameborder="0" 
                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" 
                referrerpolicy="strict-origin-when-cross-origin" 
                allowfullscreen>
              </iframe>
            </div>
            <div class="video-info">
              <h3 class="video-title">5 TÉCNICAS QUE TODA MULHER DEVE SABER | MUSA DO JIU JITSU GHI ENSINA A SE DEFENDER!</h3>
              <p class="video-description">Defesa pessoal feminida contra agressões.</p>
            </div>
          </div>
          
          <div class="video-card" data-category="socorros">
            <div class="video-container">
              <iframe 
                width="460" 
                height="315" 
                src="https://www.youtube.com/embed/ZtkwWQEiznY?si=oXgM7Rf6Q5r1hK2A" 
                title="YouTube video player" 
                frameborder="0" 
                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" 
                referrerpolicy="strict-origin-when-cross-origin" 
                allowfullscreen>
              </iframe>
            </div>
            <div class="video-info">
              <h3 class="video-title">Queimadura grave | Primeiros socorros</h3>
              <p class="video-description">PNeste vídeo, você vai conhecer os primeiros socorros a serem adotados em caso de uma queimadura grave.</p>
            </div>
          </div>
          
          <div class="video-card" data-category="socorros">
            <div class="video-container">
              <iframe 
                width="460"
                height="315" 
                src="https://www.youtube.com/embed/5kyyABzEy_k?si=9MmKK4uaLEZJN39V" 
                title="YouTube video player" 
                frameborder="0" 
                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" 
                referrerpolicy="strict-origin-when-cross-origin" 
                allowfullscreen>
              </iframe>
            </div>
            <div class="video-info">
              <h3 class="video-title">Adulto engasgado | Primeiros socorros</h3>
              <p class="video-description">Este vídeo explica o que fazer no caso de um adulto engasgado.</p>
            </div>
          </div>          
        </div>
          
        </div>
    </section>

    </div>

    <div class="footer">
           
      <div class="staff-information">
        <p>Ainda não nos conhece?</p>
        <a class="central-link" href="html/sub_links/about_us.php">sobre nós</a>
      </div>

      <div class="social_midias">
        <p class="staff-information">Nossas redes sociais</p>

        <div class="icons">
          
          <a href="https://www.instagram.com/elobos.acolhe?igsh=ZDE5N2F5ODVoY2pj">
            <img id="images" src="images/icons/INSTA.webp" alt="Instagram">
          </a>

          <a href="https://x.com/ellobos675443">
            <img id="images" src="images/icons/xTWT.avif" alt="Twitter">
          </a>

          <a href="https://www.youtube.com/@ellobos-n8n">
            <img id="images2" src="images/icons/YOUYOU2.png" alt="YouTube">      
          </a>

          </div>
        </div>

    </div>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.0.2/jquery.min.js"></script>
    <script src="js/index.js"></script>
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