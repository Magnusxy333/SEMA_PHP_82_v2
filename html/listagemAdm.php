<?php
session_start();

// Inclui o servidor (que define a variável $conecta_db)
include '../server.php';
require_once '../auth.php';

// --- CONTROLO DE ACESSO ---
if (!isLoggedIn() || !isAdmin()) {
    header('Location: ../index.php');
    exit;
}

// Validação da conexão com banco de dados
if (!isset($conecta_db) || !($conecta_db instanceof mysqli)) {
    die("Erro de conexão com o banco de dados.");
}

// --- LÓGICA DO HEADER (FOTO DO ADM) ---
$profile_photo_url = null; // Inicializa como null igual ao index.php
$img_style_header = "style='width: 38px; height: 38px; border-radius: 50%; object-fit: cover; margin-right: 5px; vertical-align: middle;'";

if (isLoggedIn() && isset($_SESSION['username'])) {
    // Usar prepared statement para prevenir SQL injection
    $stmt_header = $conecta_db->prepare("SELECT profile_picture_url FROM tb_register WHERE username = ?");
    $stmt_header->bind_param("s", $_SESSION['username']);
    $stmt_header->execute();
    $result_header = $stmt_header->get_result();
    
    if ($result_header && $row_h = $result_header->fetch_assoc()) {
        $db_photo_url = $row_h['profile_picture_url'];
        
        if (!empty($db_photo_url) && str_contains((string)$db_photo_url, 'uploads/')) {
            // Adicionar "../" porque listagemAdm.php está na pasta html/
            $profile_photo_url = '../' . $db_photo_url; 
        } 
    }
    $stmt_header->close();
}
  
// --- PAGINAÇÃO E BUSCA ---
$limit = 10;
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1; 
$page_interval = 2;
$offset = ($page - 1) * $limit;
  
// Termo de busca sanitizado
$termo_busca = '';
if (isset($_POST['busca_nome'])) {
    $termo_busca = trim($_POST['busca_nome']);
} elseif (isset($_GET['busca_nome'])) {
    $termo_busca = trim($_GET['busca_nome']);
}

// Preparar consulta com prepared statements
$where = "";
$params = [];
$types = "";

if (!empty($termo_busca)) {
    $where = " WHERE username LIKE ? OR nome LIKE ? OR sobrenome LIKE ? OR email LIKE ?";
    $search_term = "%" . $termo_busca . "%";
    $params = [$search_term, $search_term, $search_term, $search_term];
    $types = "ssss";
}

// Contagem Total com prepared statement
$sql_count = "SELECT COUNT(*) AS total FROM tb_register" . $where;
$stmt_count = $conecta_db->prepare($sql_count);

if ($stmt_count) {
    if (!empty($params)) {
        $stmt_count->bind_param($types, ...$params);
    }
    $stmt_count->execute();
    $res_count = $stmt_count->get_result();
    $row_count = $res_count->fetch_assoc();
    $registers_total = $row_count['total'];
    $stmt_count->close();
} else {
    $registers_total = 0;
}

$page_numbers = ceil($registers_total / $limit);
if($page_numbers < 1) $page_numbers = 1;

// Ajustar página atual se necessário
if ($page > $page_numbers && $page_numbers > 0) {
    $page = $page_numbers;
}

// Consulta Principal com prepared statement
$sql_query = "SELECT id, username, nome, sobrenome, email, profile_picture_url 
              FROM tb_register" . $where . " 
              ORDER BY id ASC 
              LIMIT ? OFFSET ?";

$stmt = $conecta_db->prepare($sql_query);

if ($stmt) {
    if (!empty($params)) {
        // Adicionar parâmetros de LIMIT e OFFSET
        $params[] = $limit;
        $params[] = $offset;
        $types .= "ii";
        $stmt->bind_param($types, ...$params);
    } else {
        $stmt->bind_param("ii", $limit, $offset);
    }
    
    $stmt->execute();
    $sql = $stmt->get_result();
} else {
    $sql = false;
    die("Erro na consulta ao banco de dados.");
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Área Administrativa - Usuários</title>
  <link rel="icon" href="../images/icon-site.png">
  <link rel="stylesheet" href="../styles/mobile-styles/mobile2.css">
  <link rel="stylesheet" href="../css/listagem2.css?v=<?php echo time(); ?>">
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
  
  <style>
    .profile-icon {
      width: 40px; 
      height: 40px; 
      border-radius: 50%; 
      object-fit: cover; 
      vertical-align: middle;
      margin-right: 8px;
    }
    .profile-icon-table {
      width: 40px; 
      height: 40px; 
      border-radius: 50%; 
      object-fit: cover; 
    }
    .current-page {
      background-color: #007bff;
      color: white;
      border: 1px solid #007bff;
      padding: 5px 10px;
      margin: 0 2px;
    }
    .page-button {
      background-color: #f8f9fa;
      border: 1px solid #dee2e6;
      padding: 5px 10px;
      margin: 0 2px;
    }
    .edit-button, .delete-button {
      padding: 5px 10px;
      border: none;
      border-radius: 4px;
      cursor: pointer;
    }
    .edit-button {
      background-color: #28a745;
      color: white;
    }
    .delete-button {
      background-color: #dc3545;
      color: white;
    }
    .edit-button:hover {
      background-color: #218838;
    }
    .delete-button:hover {
      background-color: #c82333;
    }
  </style>
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
  
  <div class="main">
    <h1>Área Administrativa: Listagem de Usuários</h1>

    <div style="text-align: center; margin: 20px 0;">
      <form method="GET" action="listagemAdm.php">
        <label for="busca_nome">Busca por usuário:</label>
        <input class="input-search" type="text" name="busca_nome" id="busca_nome" value="<?php echo htmlspecialchars($termo_busca); ?>" placeholder="Nome, usuário ou email">
        <input class="search-button" type="submit" value="FILTRAR">
        <?php if (!empty($termo_busca)): ?>
          <a href="listagemAdm.php" style="margin-left: 10px;">Limpar filtro</a>
        <?php endif; ?>
      </form>
    </div>
    
    <div class="table-container">
      <table class="table-listagem">
        <thead>
          <tr>
            <th colspan="8">LISTAGEM DE USUÁRIOS (Total: <?php echo htmlspecialchars($registers_total); ?>)</th>
          </tr>
          <tr>
            <th>ID</th>
            <th>Username</th>
            <th>Nome</th>
            <th>Sobrenome</th>
            <th>Email</th>
            <th>Foto</th>
            <th>EDITAR</th>
            <th>APAGAR</th>
          </tr>
        </thead>
        <tbody>
          <?php 
          if ($sql && $sql->num_rows > 0) {
            while($linha = $sql->fetch_assoc()) {
              $user_photo = null;
              $db_user_photo = $linha['profile_picture_url'];
              
              if (!empty($db_user_photo) && str_contains((string)$db_user_photo, 'uploads/')) {
                  // Adicionar "../" porque está na pasta html/
                  $user_photo = '../' . $db_user_photo; 
              }
          ?>
            <tr>
              <td><center><?php echo htmlspecialchars($linha['id']); ?></center></td>
              <td><center><?php echo htmlspecialchars($linha['username']); ?></center></td>
              <td><center><?php echo htmlspecialchars($linha['nome']); ?></center></td>
              <td><center><?php echo htmlspecialchars($linha['sobrenome']); ?></center></td>
              <td><center><?php echo htmlspecialchars($linha['email']); ?></center></td>
              <td>
                  <center>
                      <?php if ($user_photo): ?>
                        <img class="profile-icon-table" src="<?php echo htmlspecialchars($user_photo); ?>" alt="Foto do usuário">
                      <?php else: ?>
                        <i class="fas fa-user-circle" style="font-size: 40px; color: #666;"></i>
                      <?php endif; ?>
                  </center>
              </td>
              <td>
                <center>
                  <a href="sub_links/editUser.php?id=<?php echo urlencode($linha['id']); ?>">
                    <button class="edit-button" type="button" title="Editar usuário"><i class='fa-solid fa-pen-to-square'></i></button>
                  </a>
                </center>
              </td>
              <td>
                <center>
                  <form action="sub_links/deleteUserConfirm.php" method="GET">
                    <input type="hidden" name="id" value="<?php echo htmlspecialchars($linha['id']); ?>">
                    <button class="delete-button" type="submit" title="Excluir usuário"><i class='fa-solid fa-trash'></i></button> 
                  </form>
                </center>
              </td>
            </tr>
          <?php
            }
          } else {
            echo "<tr><td colspan='8'><center>Nenhum resultado encontrado.</center></td></tr>";
          }
          
          // Fechar statement
          if (isset($stmt)) {
            $stmt->close();
          }
          ?>
        </tbody>
      </table>
    </div>

    <?php if ($page_numbers > 1): ?>
    <center style="margin-top: 20px;">
      <div class="pagination-info">
        Página atual: <?php echo htmlspecialchars($page); ?> de <?php echo htmlspecialchars($page_numbers); ?>
      </div>
    </center>

    <center style="margin-top: 20px;">
      <div class="pagination">
        <a class="link" href="?page=1&busca_nome=<?php echo urlencode($termo_busca); ?>">
          <button class="first-button" <?php echo ($page == 1) ? 'disabled' : ''; ?>>&laquo;</button>
        </a>
        
        <a class="link" href="?page=<?php echo max(1, $page - 1); ?>&busca_nome=<?php echo urlencode($termo_busca); ?>">
          <button class="left-button" <?php echo ($page == 1) ? 'disabled' : ''; ?>>&lt;</button>
        </a>
        
        <?php 
        $first_page = max($page - $page_interval, 1);
        $last_page = min($page_numbers, $page + $page_interval);
        
        if ($first_page > 1) {
          echo "<span>...</span>";
        }
        
        for ($i = $first_page; $i <= $last_page; $i++) {
          if ($i == $page) {
            echo "<button class='current-page'>{$i}</button>";
          } else {
            echo "<a href='?page={$i}&busca_nome=" . urlencode($termo_busca) . "'><button class='page-button'>{$i}</button></a>";
          }
        }
        
        if ($last_page < $page_numbers) {
          echo "<span>...</span>";
        }
        ?>
        
        <a class="link" href="?page=<?php echo min($page_numbers, $page + 1); ?>&busca_nome=<?php echo urlencode($termo_busca); ?>">
          <button class="right-button" <?php echo ($page == $page_numbers) ? 'disabled' : ''; ?>>&gt;</button>
        </a>
        
        <a class="link" href="?page=<?php echo $page_numbers; ?>&busca_nome=<?php echo urlencode($termo_busca); ?>">
          <button class="last_button" <?php echo ($page == $page_numbers) ? 'disabled' : ''; ?>>&raquo;</button>
        </a>
      </div>
    </center>
    <?php endif; ?>

    <br>
    <center>
      <a href="../index.php" class="return-link">RETORNAR À PÁGINA INICIAL</a>
    </center>
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
[file content end]