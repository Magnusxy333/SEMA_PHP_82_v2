<?php
$servidor = "localhost";
$usuario  = "root";
$senha    = "";
$banco    = "sema";

// No PHP 8.2 usamos mysqli
$conecta_db = new mysqli($servidor, $usuario, $senha, $banco);

if ($conecta_db->connect_error) {
    die("Falha na conexão: " . $conecta_db->connect_error);
}
?>