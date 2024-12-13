<?php
include("config.php");

$url = isset($_GET['url']) ? $_GET['url'] : 'index';
$url = basename($url);
$pagePath = "pages/" . $url . ".php";

$publicPages = ['login-instituicao','cadastro-instituicao', 'login', 'cadastro'];

if (isset($_SESSION['login']) || in_array($url, $publicPages)) {
    if (file_exists($pagePath)) {
        if (!in_array($url, $publicPages)) {
            include("components/sidebar.php");
        }
        include($pagePath);
        if (!in_array($url, $publicPages)) {
            include("components/footer.php");
        }
    } else {
        include("pages/404.php");
    }
} else {
    include("pages/login.php");
}
