<?php
include("config.php");
echo $_SESSION['login'];

$tipo = $_SESSION['tipo'];
echo "<script>console.log(".$tipo.")</script>";

if ($tipo === 'instituicao') {
    $idInstituicao = $_SESSION['instituicao_id'];
    $excluido = Instituicao::excluirInstituicao($idInstituicao);
} elseif ($tipo === 'user') {
    $email = $_SESSION['email'];
    $usuario = Usuario::obterUsuario($email);
    $idUsuario = $usuario['usuario']['id'];
    $excluido = Usuario::excluirUsuario($idUsuario);
}

if ($excluido) {
    session_destroy();
    header("Location: login.php");
    exit;
} else {
    echo "Erro ao excluir a conta.";
}
