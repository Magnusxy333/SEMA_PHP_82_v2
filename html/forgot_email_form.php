<?php
// Inclui o cabeçalho, se necessário. Como new_password.php, este é primariamente HTML.
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperação de Senha - SEMA</title>
	<link rel="stylesheet" href="../css/mobile.css">
    <link rel="stylesheet" href="../css/forgot_email_form.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="icon" href="../images/icon-site.png">
    <link href="fonts/Genova.otf" rel="stylesheet" type="text/css">
  </head>
  <body>
           
    <div class="header">
        <div class="left">
            <a href="../index.php">
                <img class="icon" src="../images/sema.png" alt="icon">
            </a>
        </div>
        <div class="right">
            <a class="areas-text" href="../index.php">
                <i class="fas fa-house-user" ></i> HOME
            </a>
            <a class="areas-text" href="location.php">
                <i class="fas fa-map-marker-alt" > </i> LOCALIZAÇÃO
            </a>
            <a class="areas-text" href="orientations.php">
                <i class="fas fa-book-open" > </i> ORIENTAÇÕES
            </a>
            <a class="areas-text" href="contacts.php">
                <i class="fas fa-phone-alt" ></i> CONTATOS
            </a>
            <a class="areas-text" href="login.php">
                <i class='fas fa-sign-in-alt' id="login-size"></i>
            </a>
        </div>
    </div> 
  
    <div class="main">
        <div class="new-passoword-countainer">
            
            <div class="new-passoword-box">
            
            <p1 class="text">Esqueceu sua senha?</p1>

            <form action="../forgetPassword.php" method="POST">
                
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
            <img id="images" src="../images/icons/INSTA.webp" alt="">
          </a>

          <a href="https://x.com/ellobos675443">
            <img id="images" src="../images/icons/xTWT.avif" alt="">
          </a>

          <a href="https://www.youtube.com/@ellobos-n8n">
            <img id="images2" src="../images/icons/YOUYOU2.png" alt="">      
          </a>

        </div>
      </div>

    </div>
    
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.0.2/jquery.min.js"></script>
  </body>
</html>