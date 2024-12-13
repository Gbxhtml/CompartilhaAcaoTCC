<!DOCTYPE html>
<html lang="pt_BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CompartilhaAção - Cadastro</title>
    <link rel="stylesheet" href="<?php echo INCLUDE_PATH; ?>public/styles/style.css">
    <link rel="stylesheet" href="<?php echo INCLUDE_PATH; ?>public/styles/login.css">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <form method="post"> 
        <div class="container">
            <img src="<?php echo INCLUDE_PATH; ?>public/images/logo.png" alt="Logo do sistema">    
            <h2>Faça seu Cadastro</h2>
            <br>
            <?php
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $nome = $_POST['nome'];
                $email = $_POST['email'];
                $senha = $_POST['senha'];
                $estado = $_POST['estado'];
                $cidade = $_POST['cidade'];
                $numero = $_POST['numero'];
                $rua = $_POST['rua'];
                $bairro = $_POST['bairro'];
                $cep = $_POST['cep'];
                $cpf = $_POST['cpf'];
                $phone = $_POST['phone'];
            
                $result = Usuario::cadastrarUsuario($nome, $email, $senha, $estado, $cidade, $numero, $cep, $cpf, $phone, $rua, $bairro);
            
                if ($result['success']) {
                    echo '<p class="success">' . $result['message'] . ' <a href="login">Faça login</a>.</p>';
                } else {
                    echo '<p class="error">' . $result['message'] . '</p>';
                }
            }
            ?>
            <label for="nome">Insira seu Nome:</label>
            <input id="nome" name="nome" type="text" placeholder="Seu nome aqui.">
            <label for="email">Insira seu Email:</label>
            <input id="email" name="email" type="text" placeholder="Email">
            <label for="senha">Insira sua Senha:</label>
            <input id="senha" name="senha" type="password" placeholder="Senha">
            <br>
            <hr>
            <br>
            <h3>Endereço:</h3>
            <div class="endereco">
                <label for="estado">Selecione seu Estado:</label>
                <select id="estado" name="estado">
                    <option value="">Selecione o Estado</option>
                </select>

                <label for="cidade">Selecione sua Cidade:</label>
                <select id="cidade" name="cidade" disabled>
                    <option value="">Selecione o Estado primeiro</option>
                </select>

                <div class="flex">
                    <div class="w-50">
                        <label for="bairro">Escreva seu Bairro:</label>
                        <input id="bairro" name="bairro" type="text" placeholder="Bairro">
                    </div>

                    <div class="w-50">
                        <label for="rua">Escreva sua Rua:</label>
                        <input id="rua" name="rua" type="text" placeholder="Rua">
                    </div>
                </div>
                <div class="flex">
                    <div class="w-50">
                        <label for="numero">Escreva seu Numero de residencia:</label>
                        <input id="numero" name="numero" type="number" placeholder="Numero">
                    </div>
                    <div class="w-50">
                        <label for="cep">Escreva seu CEP:</label>
                        <input id="cep" name="cep" type="cep" placeholder="Cep">
                    </div>
                </div>
            </div>
            <br>
            <hr>
            <br>
            <h3>Informações pessoais:</h3>
            <div class="info">
                <label for="cpf">Escreva seu CPF:</label>
                <input id="cpf" name="cpf" type="cpf" placeholder="CPF">

                <label for="phone">Escreva seu Telefone:</label>
                <input id="phone" name="phone" type="tel" placeholder="Telefone">
            </div>

            <input type="submit" value="Cadastrar"> 
            
            <br>
            <hr>
            <br>

            <h3>Possui uma conta? <a href="login">Faça login</a></h3>
        </div>
    </form>
</body>
</html>

<script src="<?php echo INCLUDE_PATH ?>public/js/buscaEstado.js"></script>