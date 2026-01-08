<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Conta Deletada - SEMA</title>
  <link rel="stylesheet" href="css/deletedAccount3.css">
  <link rel="icon" href="images/icon-site.png">
</head>
<body>
  <?php
    // Inicie a sessão no início da página
    session_start();  
    
    // Garantir que não há cache da página anterior
    header("Cache-Control: no-cache, no-store, must-revalidate");
    header("Pragma: no-cache");
    header("Expires: 0");
  ?>

  <div class="main">
    <div class="main-content">
      <h2 class="title-delete">CONTA DELETADA</h2>
      <p class="message">Sua conta foi deletada com sucesso.</p>
              
      <a href="index.php">
        <button class="back-button">Voltar para o início</button>
      </a>      
    </div>
  </div>
</body>
</html>