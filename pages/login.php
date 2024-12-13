<!DOCTYPE html>
<html lang="pt_BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CompartilhaAção - Login</title>
    <link rel="stylesheet" href="<?php echo INCLUDE_PATH; ?>public/styles/style.css">
    <link rel="stylesheet" href="<?php echo INCLUDE_PATH; ?>public/styles/login.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;700&display=swap" rel="stylesheet">
</head>
<body>
    <form method="POST">
        <div class="container">
            <img src="<?php echo INCLUDE_PATH; ?>public/images/logo.png" alt="Logo do sistema">    
            <h2>Faça seu Login de Doador</h2>
            <br>
            <label for="email">Insira seu Email:</label>
            <input id="email" name="email" type="email" placeholder="Email" required>
            
            <label for="password">Insira sua Senha:</label>
            <input id="password" name="password" type="password" placeholder="Senha" required>
            
            <input type="submit" value="Login">
            <br>
            
            <?php
                if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                    $email = $_POST['email'];
                    $password = $_POST['password'];
                    
                    // Verificação de login
                    if (Usuario::login($email, $password)) {
                        header('Location: ' . INCLUDE_PATH);
                        exit();
                    } else {
                        echo '<p class="error">Nome de usuário ou senha incorretos.</p>';
                    }
                }
            ?>
        
            <hr>
            <br>
            <h3>Não possui uma conta? <a href="cadastro">Cadastre-se</a></h3>
            <h3>É uma instituição? <a href="login-instituicao">Faça Login</a></h3>
        </div>
    </form>
</body>
</html>
