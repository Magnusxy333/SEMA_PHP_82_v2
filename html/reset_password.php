Este é um sistema completo de redefinição de senha, dividido em três arquivos para seguir o padrão da sua aplicação e garantir a funcionalidade.

**⚠️ Aviso de Segurança:** Os arquivos a seguir utilizam as funções **`mysql_*` obsoletas** e a criptografia **`md5()`** para manter a compatibilidade com o seu código (`server.php`, `update.php`, `registration.php`). Estes métodos são **inseguros** para produção. Recomenda-se migrar para `mysqli` ou `PDO` com `password_hash()` o mais breve possível.

-----

## 1\. Página de Solicitação (O Formulário)

A página de solicitação é onde o usuário insere o e-mail. Vamos chamá-la de `html/forgot_email_form.php` (assumindo que suas páginas HTML, como o `login.php` referenciado, estão na pasta `html/`).

**Crie o arquivo:** `html/forgot_email_form.php`

```php
<?php
// Inclui o cabeçalho, se necessário. Como new_password.php, este é primariamente HTML.
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperação de Senha - SEMA</title>
    <link rel="stylesheet" href="../../styles/join_the_team-style/join_the_team.css">
    <link rel="stylesheet" href="../../css/new_password2.css"> 
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="icon" href="../../images/icon-site.png">
    <link href="fonts/Genova.otf" rel="stylesheet" type="text/css">
  </head>
  <body>
           
    <div class="header">
        <div class="left">
            <a href="../../index.php">
                <img class="icon" src="../../images/sema.png" alt="icon">
            </a>
        </div>
        <div class="right">
            <a class="areas-text" href="../..index.php">
                <i class="fas fa-house-user" ></i> HOME
            </a>
            <a class="areas-text" href="../location.php">
                <i class="fas fa-map-marker-alt" > </i> LOCALIZAÇÃO
            </a>
            <a class="areas-text" href="../orientations.php">
                <i class="fas fa-book-open" > </i> ORIENTAÇÕES
            </a>
            <a class="areas-text" href="../contacts.php">
                <i class="fas fa-phone-alt" ></i> CONTATOS
            </a>
            <a class="areas-text" href="../login.php">
                <i class='fas fa-sign-in-alt' id="login-size"></i>
            </a>
        </div>
    </div> 
  
    <div class="main">
        <div class="new-passoword-countainer">
            
            <div class="new-passoword-box">
            
            <p1 class="text">Esqueceu sua senha?</p1>

            <form action="../../forgetPassword.php" method="POST">
                
                <p class="text2" style="margin-top: 20px;">
                    Informe seu e-mail de cadastro
                </p>
                <input class="input" type="email" name="email" placeholder="Seu Email" required>
                
                <button type="submit" class="send-button" style="margin-top: 20px;">
                    ENVIAR LINK DE REDEFINIÇÃO
                </button>
            </form>

            <p style="margin-top: 15px; color: #333; font-size: 14px;">
                <?php
                // Exibe mensagens de status após o redirecionamento
                if (isset($_GET['status'])) {
                    if ($_GET['status'] == 'success') {
                        echo "<span style='color: green;'>✅ Link enviado! Verifique sua caixa de entrada.</span>";
                    } elseif ($_GET['status'] == 'error') {
                        echo "<span style='color: red;'>❌ Erro: Email não encontrado ou falha no sistema.</span>";
                    }
                }
                ?>
            </p>
            
            </div>
        </div>
    </div>


   <div class="footer">
           
      <div class="staff-information">
        <p>Ainda não nos conhece?</p>
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

    </div>
    
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.0.2/jquery.min.js"></script>
  </body>
</html>
```

-----

## 2\. Página de Processamento: `forgetPassword.php`

Este arquivo deve estar na **raiz** do seu projeto, onde está o `server.php` e `singIn.php`.

**Crie o arquivo:** `forgetPassword.php`

```php
<?php
session_start();
include 'server.php'; // Inclui a conexão mysql_connect()

// Redireciona se não for um POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['email'])) {
    header('Location: html/forgot_email_form.php');
    exit;
}

$email = $_POST['email'];
// Proteção básica para o email antes de usar na query
$email_seguro = mysql_real_escape_string($email); 

// 1. Verificar se o email existe
$sql_check = mysql_query("SELECT id FROM tb_register WHERE email = '$email_seguro'");

if ($sql_check && mysql_num_rows($sql_check) > 0) {
    // 2. Gerar Token Único e Data de Expiração (Ex: 1 hora)
    // Usando random_bytes para gerar um token criptograficamente seguro
    $token = bin2hex(openssl_random_pseudo_bytes(32)); 
    $expira = date("Y-m-d H:i:s", strtotime('+1 hour'));

    // 3. Salvar o token e a expiração no banco de dados
    $sql_update = "UPDATE tb_register 
                   SET reset_token = '$token', token_expiry = '$expira' 
                   WHERE email = '$email_seguro'";
    
    if (mysql_query($sql_update)) {
        
        // 4. CONSTRUÇÃO DO LINK DE REDEFINIÇÃO
        // Este link aponta para o arquivo de reset que criaremos (em html/reset_password.php)
        $reset_link = "http://" . $_SERVER['HTTP_HOST'] . "/html/reset_password.php?token=" . $token;

        // 5. SIMULAÇÃO DE ENVIO DE EMAIL
        // Na prática, você usaria mail() ou uma biblioteca SMTP aqui.
        // Para fins de teste, vamos exibir o link e interromper o redirecionamento.
        
        echo "<h1>SIMULAÇÃO: Email Enviado com Sucesso!</h1>";
        echo "<p>Na aplicação real, o link abaixo seria enviado para o email: **$email**</p>";
        echo "<p>Clique aqui para redefinir a senha: <a href=\"$reset_link\">$reset_link</a></p>";
        echo "<p>⚠️ **LEMBRETE:** Este é um link de teste! Após testar, remova esta parte de 'echo' e descomente o 'header' abaixo para produção.</p>";
        
        // Descomente o bloco abaixo para o ambiente de produção:
        /*
        header('Location: html/forgot_email_form.php?status=success');
        exit;
        */

    } else {
        // Erro ao atualizar o banco
        header('Location: html/forgot_email_form.php?status=error');
        exit;
    }

} else {
    // Email não encontrado (retorna erro, mas pode ser genérico por segurança)
    header('Location: html/forgot_email_form.php?status=error');
    exit;
}
?>
```

-----

## 3\. Página de Redefinição: `html/reset_password.php`

Este arquivo recebe o token, valida-o e processa a nova senha.

**Crie o arquivo:** `html/reset_password.php`

```php
<?php
session_start();
// O caminho do include para server.php deve ser ajustado, já que este arquivo está em 'html/'
include '../server.php'; 

$token = isset($_GET['token']) ? $_GET['token'] : (isset($_POST['token']) ? $_POST['token'] : null);
$token_seguro = mysql_real_escape_string($token);
$mensagem = "";
$exibir_form = false;

// ---------------------------------------------
// 1. Processamento da Nova Senha (POST do formulário)
// ---------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nova_senha']) && isset($_POST['token'])) {
    
    $nova_senha = $_POST['nova_senha'];
    $confirmar_senha = $_POST['confirmar_senha'];
    
    if ($nova_senha !== $confirmar_senha) {
        $mensagem = "As senhas não coincidem. Tente novamente.";
        $exibir_form = true; // Mantém o formulário aberto
    } elseif (empty($nova_senha) || strlen($nova_senha) < 4) { 
        $mensagem = "A senha deve ter pelo menos 4 caracteres.";
        $exibir_form = true;
    } else {
        // Criptografia usando MD5 para compatibilidade com o seu update.php
        $senha_hash = md5($nova_senha); 
        
        // Atualiza a senha e limpa o token/expiração
        $sql_update_password = "UPDATE tb_register 
                                SET senha = '$senha_hash', reset_token = NULL, token_expiry = NULL 
                                WHERE reset_token = '$token_seguro'";
        
        if (mysql_query($sql_update_password)) {
            $mensagem = "Senha redefinida com sucesso! Redirecionando para o login...";
            // Redireciona para o login após 3 segundos
            header('Refresh: 3; URL=login.php'); 
        } else {
            $mensagem = "Erro ao redefinir a senha no banco de dados.";
            $exibir_form = true;
        }
    }
}

// ---------------------------------------------
// 2. Validação do Token (GET do link do e-mail)
// ---------------------------------------------
if (!empty($token) && !isset($_POST['nova_senha'])) {
    $agora = date("Y-m-d H:i:s");
    
    // Consulta o banco, verifica o token e se ele não expirou
    $sql_validate = mysql_query("SELECT id FROM tb_register 
                                 WHERE reset_token = '$token_seguro' 
                                 AND token_expiry > '$agora'");

    if ($sql_validate && mysql_num_rows($sql_validate) > 0) {
        $exibir_form = true;
    } else {
        $mensagem = "O link de redefinição é inválido ou expirou. Por favor, solicite um novo.";
    }
} elseif (empty($token) && !isset($_POST['nova_senha'])) {
     $mensagem = "Acesso negado. Token de redefinição não fornecido.";
}
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Definir Nova Senha - SEMA</title>
    <link rel="stylesheet" href="../../styles/join_the_team-style/join_the_team.css">
    <link rel="stylesheet" href="../../css/new_password2.css"> 
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="icon" href="../../images/icon-site.png">
    <link href="fonts/Genova.otf" rel="stylesheet" type="text/css">
  </head>
  <body>
    <div class="main">
        <div class="new-passoword-countainer">
            
            <div class="new-passoword-box">
            
                <?php if (!empty($mensagem)): ?>
                    <p style="color: red; margin-bottom: 20px;"><?php echo $mensagem; ?></p>
                <?php endif; ?>

                <?php if ($exibir_form): ?>
                    <p1 class="text">Defina sua nova senha</p1>

                    <form action="reset_password.php" method="POST">
                        <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>"> 

                        <div>
                            <p class="text2">Faça uma senha forte</p>
                            <input class="input" type="password" name="nova_senha" placeholder="Nova senha" required>
                        </div>

                        <div>
                            <p class="text2">Repita a nova senha</p>
                            <input class="input" type="password" name="confirmar_senha" placeholder="Confirmar senha" required>
                        </div>

                        <button type="submit" class="send-button">ENVIAR</button>
                    </form>
                <?php endif; ?>
            
            </div>
        </div>
    </div>
  </body>
</html>
```