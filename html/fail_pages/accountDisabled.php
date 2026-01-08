<?php
// html/fail_pages/accountDisabled.php
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Conta Desativada</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            padding: 50px;
            background-color: #f8f9fa;
        }
        .error-container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            max-width: 500px;
            margin: 0 auto;
        }
        h1 {
            color: #dc3545;
        }
        .btn {
            display: inline-block;
            background: #007bff;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="error-container">
        <h1>⚠️ Conta Desativada</h1>
        <p>Sua conta foi desativada pelo administrador do sistema.</p>
        <p>Para mais informações, entre em contato com o suporte.</p>
        <a href="../../index.php" class="btn">Voltar à página inicial</a>
    </div>
</body>
</html>