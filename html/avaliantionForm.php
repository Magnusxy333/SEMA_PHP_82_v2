<?php
session_start();
include '../server.php';

// Verificar login
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php?login_required=1');
    exit();
}

$user_id = $_SESSION['user_id'];

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
            
            if (isset($db_photo_url) && !empty($db_photo_url) && strpos($db_photo_url, 'uploads/') !== false) {
                $profile_photo_url = '../' . $db_photo_url; 
            } 
        }
    }
}

// ✅ ADICIONAR: Opções de satisfação em PHP
$opcoesSatisfacao = [
    'MR' => ['texto' => 'Muito Ruim', 'peso' => 1],
    'R'  => ['texto' => 'Ruim',       'peso' => 2],
    'B'  => ['texto' => 'Bom',        'peso' => 3],
    'O'  => ['texto' => 'Ótimo',      'peso' => 4],
    'E'  => ['texto' => 'Excelente',  'peso' => 5],
];

// ✅ ADICIONAR: Processar envio do formulário
$resultado_salvo = false;
$pontuacaoTotal = 0;
$satisfacaoGeral = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pontuacaoTotal = 0;
    $respostas_usuario = [];
    
    // Calcular pontuação
    for ($i = 1; $i <= 8; $i++) {
        if (isset($_POST["questao$i"])) {
            $resposta = $_POST["questao$i"];
            $peso = $opcoesSatisfacao[$resposta]['peso'];
            $pontuacaoTotal += $peso;
            $respostas_usuario[$i] = $resposta;
        }
    }
    
    $media = $pontuacaoTotal / 8;
    
    // Determinar satisfação geral
    if ($media >= 4.5) $satisfacaoGeral = 'Excelente';
    elseif ($media >= 3.5) $satisfacaoGeral = 'Ótima';
    elseif ($media >= 2.5) $satisfacaoGeral = 'Boa';
    elseif ($media >= 1.5) $satisfacaoGeral = 'Ruim';
    else $satisfacaoGeral = 'Muito Ruim';
    
    // ✅ ADICIONAR: Salvar no banco de dados
    if (isset($conecta_db)) {
        $user_id = mysqli_real_escape_string($conecta_db, $user_id);
        $pontuacaoTotal = mysqli_real_escape_string($conecta_db, $pontuacaoTotal);
        $media = mysqli_real_escape_string($conecta_db, $media);
        $satisfacaoGeral = mysqli_real_escape_string($conecta_db, $satisfacaoGeral);
        
        $sql_form = "INSERT INTO tb_form_satisfacao (user_id, pontuacao_total, media, satisfacao_geral) 
                     VALUES ('$user_id', '$pontuacaoTotal', '$media', '$satisfacaoGeral')";
        $result_form = mysqli_query($conecta_db, $sql_form);
        
        if ($result_form) {
            $form_id = mysqli_insert_id($conecta_db);
            
            // Salvar respostas individuais
            foreach ($respostas_usuario as $questao_num => $resposta) {
                $peso = $opcoesSatisfacao[$resposta]['peso'];
                $questao_num = mysqli_real_escape_string($conecta_db, $questao_num);
                $resposta = mysqli_real_escape_string($conecta_db, $resposta);
                $peso = mysqli_real_escape_string($conecta_db, $peso);
                
                $sql_resposta = "INSERT INTO tb_respostas_satisfacao (form_id, questao_num, resposta, peso) 
                                VALUES ('$form_id', '$questao_num', '$resposta', '$peso')";
                mysqli_query($conecta_db, $sql_resposta);
            }
            
            $resultado_salvo = true;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pesquisa de Satisfação - Serviços de Mauá</title>
    <link rel="stylesheet" href="../css/challengeForm2.css">
    <link rel="icon" href="../images/icon-site.png">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
           
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
                <?php echo $img_style_header; ?>
              >
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
    <main class="main">
        <div class="container">
            <h1>Pesquisa de Satisfação - Serviços de Mauá</h1>
            
            <?php if (!$resultado_salvo): ?>
            <form method="POST" action="avaliantionForm.php" id="form-satisfacao">
                <!-- Questão 1 -->
                <div class="questao">
                    <p class="pergunta">1. Qual a sua satisfação com o atendimento e serviços nas Unidades de Saúde (SUS) de Mauá?</p>
                    <div class="opcoes">
                        <label>
                            <input type="radio" name="questao1" value="MR" required> Muito Ruim
                        </label>
                        <label>
                            <input type="radio" name="questao1" value="R"> Ruim
                        </label>
                        <label>
                            <input type="radio" name="questao1" value="B"> Bom
                        </label>
                        <label>
                            <input type="radio" name="questao1" value="O"> Ótimo
                        </label>
                        <label>
                            <input type="radio" name="questao1" value="E"> Excelente
                        </label>
                    </div>
                </div>

                <!-- Questão 2 -->
                <div class="questao">
                    <p class="pergunta">2. Qual a sua satisfação com a qualidade e pontualidade do transporte público (ônibus municipal)?</p>
                    <div class="opcoes">
                        <label>
                            <input type="radio" name="questao2" value="MR" required> Muito Ruim
                        </label>
                        <label>
                            <input type="radio" name="questao2" value="R"> Ruim
                        </label>
                        <label>
                            <input type="radio" name="questao2" value="B"> Bom
                        </label>
                        <label>
                            <input type="radio" name="questao2" value="O"> Ótimo
                        </label>
                        <label>
                            <input type="radio" name="questao2" value="E"> Excelente
                        </label>
                    </div>
                </div>

                <!-- Questão 3 -->
                <div class="questao">
                    <p class="pergunta">3. Qual a sua satisfação com os serviços de coleta de lixo e limpeza urbana?</p>
                    <div class="opcoes">
                        <label>
                            <input type="radio" name="questao3" value="MR" required> Muito Ruim
                        </label>
                        <label>
                            <input type="radio" name="questao3" value="R"> Ruim
                        </label>
                        <label>
                            <input type="radio" name="questao3" value="B"> Bom
                        </label>
                        <label>
                            <input type="radio" name="questao3" value="O"> Ótimo
                        </label>
                        <label>
                            <input type="radio" name="questao3" value="E"> Excelente
                        </label>
                    </div>
                </div>

                <!-- Questão 4 -->
                <div class="questao">
                    <p class="pergunta">4. Qual a sua satisfação com a segurança e iluminação pública nos bairros?</p>
                    <div class="opcoes">
                        <label>
                            <input type="radio" name="questao4" value="MR" required> Muito Ruim
                        </label>
                        <label>
                            <input type="radio" name="questao4" value="R"> Ruim
                        </label>
                        <label>
                            <input type="radio" name="questao4" value="B"> Bom
                        </label>
                        <label>
                            <input type="radio" name="questao4" value="O"> Ótimo
                        </label>
                        <label>
                            <input type="radio" name="questao4" value="E"> Excelente
                        </label>
                    </div>
                </div>

                <!-- Questão 5 -->
                <div class="questao">
                    <p class="pergunta">5. Qual a sua satisfação geral com a manutenção e conservação das ruas e calçadas (infraestrutura)?</p>
                    <div class="opcoes">
                        <label>
                            <input type="radio" name="questao5" value="MR" required> Muito Ruim
                        </label>
                        <label>
                            <input type="radio" name="questao5" value="R"> Ruim
                        </label>
                        <label>
                            <input type="radio" name="questao5" value="B"> Bom
                        </label>
                        <label>
                            <input type="radio" name="questao5" value="O"> Ótimo
                        </label>
                        <label>
                            <input type="radio" name="questao5" value="E"> Excelente
                        </label>
                    </div>
                </div>

                <!-- Questão 6 -->
                <div class="questao">
                    <p class="pergunta">6. Qual a sua satisfação com os serviços oferecidos pelas escolas públicas municipais?</p>
                    <div class="opcoes">
                        <label>
                            <input type="radio" name="questao6" value="MR" required> Muito Ruim
                        </label>
                        <label>
                            <input type="radio" name="questao6" value="R"> Ruim
                        </label>
                        <label>
                            <input type="radio" name="questao6" value="B"> Bom
                        </label>
                        <label>
                            <input type="radio" name="questao6" value="O"> Ótimo
                        </label>
                        <label>
                            <input type="radio" name="questao6" value="E"> Excelente
                        </label>
                    </div>
                </div>

                <!-- Questão 7 -->
                <div class="questao">
                    <p class="pergunta">7. Qual a sua satisfação com os serviços de atendimento e suporte oferecidos pela Prefeitura de Mauá (atendimento ao cidadão)?</p>
                    <div class="opcoes">
                        <label>
                            <input type="radio" name="questao7" value="MR" required> Muito Ruim
                        </label>
                        <label>
                            <input type="radio" name="questao7" value="R"> Ruim
                        </label>
                        <label>
                            <input type="radio" name="questao7" value="B"> Bom
                        </label>
                        <label>
                            <input type="radio" name="questao7" value="O"> Ótimo
                        </label>
                        <label>
                            <input type="radio" name="questao7" value="E"> Excelente
                        </label>
                    </div>
                </div>

                <!-- Questão 8 -->
                <div class="questao">
                    <p class="pergunta">8. Qual a sua satisfação com os serviços de lazer e áreas verdes (parques, praças) disponíveis na cidade?</p>
                    <div class="opcoes">
                        <label>
                            <input type="radio" name="questao8" value="MR" required> Muito Ruim
                        </label>
                        <label>
                            <input type="radio" name="questao8" value="R"> Ruim
                        </label>
                        <label>
                            <input type="radio" name="questao8" value="B"> Bom
                        </label>
                        <label>
                            <input type="radio" name="questao8" value="O"> Ótimo
                        </label>
                        <label>
                            <input type="radio" name="questao8" value="E"> Excelente
                        </label>
                    </div>
                </div>

                <button type="submit" class="botao-enviar">Enviar Pesquisa</button>
            </form>
            <?php endif; ?>

            <!-- ✅ ADICIONAR: Mensagem de sucesso -->
            <?php if ($resultado_salvo): ?>
                <div class="resultado">
                    <h2>Pesquisa Enviada com Sucesso!</h2>
                    <p>Pontuação: <?php echo $pontuacaoTotal; ?>/40</p>
                    <p>Satisfação: <?php echo $satisfacaoGeral; ?></p>
                    <a href="../index.php" class="botao-refazer">Voltar para Home</a>
                </div>
            <?php endif; ?>
        </div>
    </main>

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
        // Header sticky effect
        window.addEventListener('scroll', function() {
            const header = document.getElementById('header');
            if (window.scrollY > 50) {
                header.classList.add('sticking');
            } else {
                header.classList.remove('sticking');
            }
        });
    </script>
</body>
</html>