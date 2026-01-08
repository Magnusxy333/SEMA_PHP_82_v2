<?php
session_start();
include '../server.php';

// VERIFICAÇÃO DE LOGIN - DEVE VIR PRIMEIRO
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php?login_required=1');
    exit();
}

// ✅ ADICIONAR: Obter user_id
$user_id = $_SESSION['user_id'];

$questoes = [
  1 => [
    'pergunta' => 'O que fazer caso a panela com óleo pegue fogo?',
    'opcoes' => [
      'A' => 'Jogar água',
      'B' => 'Abafar com um pano ou tampa',
      'C' => 'Sair correndo',
      'D' => 'Esperar esfriar'
    ],
    'correta' => 'B'
  ],
  2 => [
    'pergunta' => 'O que fazer caso sinta cheiro de gás em casa?',
    'opcoes' => [
      'A' => 'Abrir janelas e verificar o botijão',
      'B' => 'Acender um fósforo para verificar o vazamento',
      'C' => 'Ligar o exaustor para ventilar rapidamente',
      'D' => 'Esperar o cheiro passar sozinho'
    ],
    'correta' => 'A'
  ],
  3 => [
    'pergunta' => 'O que fazer se alguém levar um choque elétrico?',
    'opcoes' => [
      'A' => 'Tocar na pessoa para puxá-la imediatamente',
      'B' => 'Desligar a fonte de energia antes de ajudar',
      'C' => 'Jogar um balde de água na pessoa',
      'D' => 'Usar um objeto metálico para afastá-la'
    ],
    'correta' => 'B'
  ],
  4 => [
    'pergunta' => 'O que fazer em caso de incêndio em casa?',
    'opcoes' => [
      'A' => 'Tentar apagar tudo sozinho com baldes de água',
      'B' => 'Sair imediatamente e chamar os bombeiros (193)',
      'C' => 'Pegar todos os objetos de valor antes de sair',
      'D' => 'Abrir todas as janelas para ventilar o fogo'
    ],
    'correta' => 'B'
  ],
  5 => [
    'pergunta' => 'O que fazer se uma pessoa se engasgar e não conseguir respirar?',
    'opcoes' => [
      'A' => 'Dar tapas nas costas e realizar a manobra de Heimlich',
      'B' => 'Oferecer água para tentar desengasgar',
      'C' => 'Levantar os braços da pessoa acima da cabeça',
      'D' => 'Colocar o dedo na garganta para forçar o objeto a sair'
    ],
    'correta' => 'A'
  ],
  6 => [
    'pergunta' => 'O que fazer se houver alagamento na rua?',
    'opcoes' => [
      'A' => 'Entrar na água para atravessar rapidamente',
      'B' => 'Evitar áreas alagadas e buscar abrigo seguro em local alto',
      'C' => 'Seguir outros carros que estão atravessando',
      'D' => 'Parar o carro e esperar a água baixar dentro dele'
    ],
    'correta' => 'B'
  ],
  7 => [
    'pergunta' => 'O que fazer em caso de queimadura leve?',
    'opcoes' => [
      'A' => 'Passar manteiga ou pasta de dente',
      'B' => 'Lavar com água corrente fria e cobrir levemente com gaze estéril',
      'C' => 'Furar as bolhas que aparecerem na pele',
      'D' => 'Cobrir a área imediatamente com algodão'
    ],
    'correta' => 'B'
  ],
  8 => [
    'pergunta' => 'O que fazer durante um terremoto (se estiver em um prédio)?',
    'opcoes' => [
      'A' => 'Correr para a rua imediatamente',
      'B' => 'Se abrigar debaixo de uma mesa resistente ou junto a uma parede interna',
      'C' => 'Ficar perto de janelas e objetos de vidro',
      'D' => 'Usar o elevador para descer rapidamente'
    ],
    'correta' => 'B'
  ],
  9 => [
    'pergunta' => 'O que fazer se uma pessoa desmaiar?',
    'opcoes' => [
      'A' => 'Deitar a pessoa e elevar suas pernas acima do nível do coração',
      'B' => 'Dar tapas no rosto ou chacoalhar para que ela acorde',
      'C' => 'Fazer a pessoa beber café ou água açucarada imediatamente',
      'D' => 'Levantar a pessoa e fazê-la caminhar'
    ],
    'correta' => 'A'
  ],
  10 => [
    'pergunta' => 'O que fazer se encontrar um fio elétrico caído na rua?',
    'opcoes' => [
      'A' => 'Tentar afastar o fio com um pedaço de pau seco',
      'B' => 'Manter distância e avisar imediatamente as autoridades competentes',
      'C' => 'Jogar água para dissipar a eletricidade',
      'D' => 'Avisar as pessoas que passarem perto para pisarem devagar'
    ],
    'correta' => 'B'
  ],
];

$resultado = null;
$pontuacao = 0;
$quiz_salvo = false;

// Verifica se o formulário foi enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

  // LINHA 21 CORRIGIDA: Usa isset() e operador ternário (PHP 5.4 compatível)
  $respostas_usuario = isset($_POST['resposta']) ? $_POST['resposta'] : array();

  $total_questoes = count($questoes);

  foreach ($questoes as $num => $dados) {
    // LINHA 36 CORRIGIDA: Usa isset() e operador ternário
    $resposta_usuario = isset($respostas_usuario[$num]) ? $respostas_usuario[$num] : null;

    if ($resposta_usuario === $dados['correta']) {
      $pontuacao++;
    }
  }

  $resultado = "Você acertou ({$pontuacao}) de ({$total_questoes}) questões! (" . round(($pontuacao / $total_questoes) * 100) . "%)";
  
  // ✅ ADICIONAR: Salvar resultado do quiz
  $percentual = round(($pontuacao / $total_questoes) * 100);
  
  // Usando a conexão mysqli do server.php
  if (isset($conecta_db)) {
    $user_id = mysqli_real_escape_string($conecta_db, $user_id);
    $pontuacao = mysqli_real_escape_string($conecta_db, $pontuacao);
    $total_questoes = mysqli_real_escape_string($conecta_db, $total_questoes);
    $percentual = mysqli_real_escape_string($conecta_db, $percentual);
    
    $sql_quiz = "INSERT INTO tb_quiz_seguranca (user_id, pontuacao, total_questoes, percentual) 
                 VALUES ('$user_id', '$pontuacao', '$total_questoes', '$percentual')";
    $result_quiz = mysqli_query($conecta_db, $sql_quiz);
    
    if ($result_quiz) {
        $quiz_salvo = true;
    }
  }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Quiz de Segurança e Primeiros Socorros</title>
  <link rel="stylesheet" href="styles/mobile.css">
  <link rel="stylesheet" href="../css/challengeForm2.css">
  <link rel="icon" href="../images/icon-site.png">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body>
  <?php
  // Variáveis default
  $profile_photo_url = null;
  $img_style_header = "style='width: 38px; height: 38px; border-radius: 50%; object-fit: cover; vertical-align: middle;'";

  if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
    $username = $_SESSION['username'];
    
    // Usando a conexão mysqli do server.php
    if (isset($conecta_db)) {
      $username = mysqli_real_escape_string($conecta_db, $username);
      $sql = "SELECT profile_picture_url FROM tb_register WHERE username = '$username'";
      $result = mysqli_query($conecta_db, $sql);

      if ($result && $linha = mysqli_fetch_assoc($result)) {
        $db_photo_url = $linha['profile_picture_url'];

        // Verifica se há um caminho válido
        if (isset($db_photo_url) && !empty($db_photo_url) && strpos($db_photo_url, 'uploads/') !== false) {
          // AQUI O CAMINHO DEVE SER AJUSTADO PARA SAIR DA PASTA 'html/'
          $profile_photo_url = '../' . $db_photo_url;
        }
      }
    }
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
        HOME
      </a>

      <a class="areas-text" href="location.php">
        <i class="fas fa-map-marker-alt"></i>
        LOCALIZAÇÃO
      </a>

      <a class="areas-text" href="orientations.php">
        <i class="fas fa-book-open"></i>
        ORIENTAÇÕES
      </a>

      <a class="areas-text" href="contacts.php">
        <i class="fas fa-phone-alt"></i>
        CONTATOS
      </a>

      <?php if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true): ?>
        <a class="areas-text" href="profile.php">
          <?php if ($profile_photo_url !== null): ?>
            <img
              src="<?php echo htmlspecialchars($profile_photo_url); ?>"
              alt="Foto de Perfil"
              <?php echo $img_style_header; ?>>
          <?php else: ?>
            <i class="fas fa-user-circle"></i>
          <?php endif; ?>
          PERFIL
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
    <div class="container">
      <h1>🚨 Quiz de Segurança e Primeiros Socorros</h1>
      <p>Marque a alternativa correta para cada situação.</p>

      <?php if ($resultado): ?>
        <div class="resultado">
          <h2>Resultado:</h2>
          <p><?php echo $resultado; ?></p>
          <?php if ($quiz_salvo): ?>
            <p style="color: #27ae60; font-weight: bold;">✅ Resultado salvo com sucesso!</p>
          <?php endif; ?>
        </div>
        <a href="challengeForm.php" class="botao-refazer">Refazer Quiz</a>
        <a href="../index.php" class="botao-refazer">Voltar para a Home</a>
        <hr>
      <?php endif; ?>

      <?php if (!$resultado): ?>
      <form method="POST" action="challengeForm.php">
        <?php foreach ($questoes as $num => $dados): ?>
          <div class="questao">
            <p class="pergunta"><strong><?php echo $num; ?>)</strong> <?php echo $dados['pergunta']; ?></p>

            <div class="opcoes">
              <?php foreach ($dados['opcoes'] as $letra => $texto): ?>
                <label>
                  <input
                    type="radio"
                    name="resposta[<?php echo $num; ?>]"
                    value="<?php echo $letra; ?>"
                    required>
                  <?php echo $letra; ?>) <?php echo $texto; ?>
                </label>
              <?php endforeach; ?>
            </div>
          </div>
        <?php endforeach; ?>

        <button type="submit" class="botao-enviar">Verificar Respostas</button>
      </form>
      <?php endif; ?>
    </div>
  </div>

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

</body>

</html>