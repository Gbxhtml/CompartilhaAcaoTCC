<?php 

if ($_SESSION['tipo'] === 'user') {
    $tipoPerfil = 'do Usuário';
    $usuario = Usuario::obterUsuario($_SESSION['email']);
    $endereco = $usuario['endereco'];
    $usuario = $usuario['usuario'];
} else {
    $tipoPerfil = 'da Instituição';
    $usuario = Instituicao::obterInstituicao($_SESSION['email']);
    $endereco = $usuario['endereco'];
    $usuario = $usuario['usuario'];
}
?>
<div class="container">
    <div class="perfil">
        <!-- Saudação -->
        <section class="saudacao">
            <h2><i class="fa-solid fa-hands"></i> Olá, <?php 
                        if ($_SESSION['tipo'] == 'user') {
                               echo $usuario['nome'];
                        } else {
                                echo $usuario['nome_fantasia'];
                        }
                    ?>, o que você gostaria de fazer?</h2>
        </section>
        
        <!-- Editar Perfil -->
        <section class="profile-edit">
            <?php 
            if (isset($_POST['atualizar'])) {
                $senha = $_POST['senha'];
                if ($_SESSION['tipo'] == 'user') {
                    $correta = Usuario::verificarSenhaUsuario($usuario['id'], $senha);
                } else {
                    $correta = Instituicao::verificarSenhaInstituicao($usuario['id'], $senha);
                }
                $nome = $_POST['nome'];
                $telefone = $_POST['phone'];
                $senhaNova = $_POST['senhaNova'];
                
                $endereco = [
                    'estado' => $_POST['estado'],
                    'cidade' => $_POST['cidade'],
                    'bairro' => $_POST['bairro'],
                    'rua' => $_POST['rua'],
                    'id' => $endereco['id']
                ];

                if ($correta) {
            
                    if ($_SESSION['tipo'] == 'user') {
                        $atualizado = Usuario::atualizarUsuario($nome, $telefone, $senhaNova, $endereco);
                    } else {
                        $descricao = $_POST['descricao'];
                        $imagem = null;
                
                        if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] === UPLOAD_ERR_OK) {
                            $imagemTemp = $_FILES['imagem']['tmp_name'];
                            $imagemNome = uniqid() . '-' . $_FILES['imagem']['name'];
                            $caminhoDestino = 'uploads/' . $imagemNome;
                
                            if (move_uploaded_file($imagemTemp, $caminhoDestino)) {
                                $imagem = $caminhoDestino;
                            }
                        }
                
                        $atualizado = Instituicao::atualizarInstituicao($nome, $descricao, $telefone, $senhaNova, $imagem, $endereco);
                    }
                
                    if ($atualizado) {
                        header("Location: ".INCLUDE_PATH."perfil");
                        echo '<span class="success">Perfil atualizado com sucesso!</span>';
                    } else {
                        echo '<span class="error">ocorreu um erro!</span>';
                    }
                } else {
                    echo '<span class="error">Senha Errada!</span>';
                }
            } ?>
            <h2>Editar Perfil <?php echo $tipoPerfil; ?></h2>
            <form method="post" enctype="multipart/form-data">
                <div class="edit-profile">
                    <label for="nome">Nome do perfil:</label>
                    <?php 
                        if ($_SESSION['tipo'] == 'user') {
                            ?>
                                <input type="text" name="nome" value="<?php echo $usuario['nome']; ?>">
                            <?php
                        } else {
                            ?> 
                                <input type="text" name="nome" value="<?php echo $usuario['nome_fantasia']; ?>">
                                <label for="nome">Imagem da instituição:</label>
                                <input class="img-input"  type="file" name="imagem" value="<?php echo $usuario['img']; ?>" >

                                <label for="descricao">Descrição da instituição:</label>
                                <textarea name="descricao"></textarea>
                            <?php
                        }
                    ?>
                    
                    <label for="email">Email:</label>
                    <input type="email" name="email" value="<?php echo $usuario['email']; ?>" readonly>

                    <label for="senhaNova">Nova Senha:</label>
                    <input type="password" name="senhaNova" placeholder="Digite para alterar">
                    
                    <!-- Endereço -->
                    <h3>Endereço:</h3>
                    <div class="endereco">
                        <label for="estado">Estado:</label>
                        <select id="estado" name="estado">
                            <option value="<?php echo $endereco['estado']; ?>" selected><?php echo $endereco['estado']; ?></option>
                        </select>
                        <label for="cidade">Cidade:</label>
                        <select id="cidade" name="cidade">
                            <option value="<?php echo $endereco['cidade']; ?>" selected><?php echo $endereco['cidade']; ?></option>
                        </select>
                        <label for="bairro">Bairro:</label>
                        <input type="text" name="bairro" value="<?php echo $endereco['bairro']; ?>">
                        <label for="rua">Rua:</label>
                        <input type="text" name="rua" value="<?php echo $endereco['rua']; ?>">
                    </div>
                    
                    <!-- Informações Pessoais -->
                    <h3>Informações pessoais:</h3>
                    <?php 
                        if ($_SESSION['tipo'] == 'user') {
                            ?>
                                <label for="cpf">CPF:</label>
                                <input type="text" name="cpf" value="<?php echo $usuario['cpf']; ?>" readonly> 
                            <?php
                        } else {
                            ?> 
                                <label for="cnpj">CNPJ:</label>
                                <input type="text" name="cnpj" value="<?php echo $usuario['cnpj']; ?>" readonly> 
                            <?php
                        }
                    ?>
                    
                    <label for="phone">Telefone:</label>
                    <input type="tel" name="phone" value="<?php echo $usuario['fone']; ?>">

                    <!-- Botões -->
                    <div class="actions">
                        <a onclick="alterar()">Salvar Alterações</a>
                    </div>
                    <div id="alterar" class="confirmar">
                        <div class="card">
                            <div class="flex">
                                <h2>Deseja mesmo prosseguir?</h2>
                                <a class="fechar" onclick="fecharAlterar()"><span><i class="fa-solid fa-circle-xmark"></i></span> </a>
                            </div>
                            <label for="senha">Insira sua senha:</label>
                            <input type="senha" name="senha">
                            <input type="hidden" name="atualizar">
                            <input type="submit" value="Salvar Alterações">
                        </div>
                    </div>
                </div>
            </form>
        </section>

        <!-- Central de Conta -->
        <section class="user">
            <h2 style="text-align: center;">Central de Conta</h2>
            <div class="user-center">
                <a href="logout.php">Sair</a>
                <a onclick="excluir()">Excluir Conta</a>
            </div>
        </section>
        
        <!-- Modal Excluir -->
        <div id="excluir" class="confirmar">
            <div class="card">
                <div class="flex">
                    <h2>Deseja mesmo excluir?</h2>
                    <a class="fechar" onclick="fecharExcluir()"><span><i class="fa-solid fa-circle-xmark"></i></span> </a>
                </div>
                <p>Lembre-se, essa ação é irreversível!</p>
                <form method="post" action="excluir.php">
                    <input type="hidden" name="tipo" value="<?php echo $_SESSION['tipo']; ?>">
                    <input type="submit" value="Confirmar Exclusão">
                </form>
                
            </div>
        </div>
    </div>
</div>

<script src="<?php echo INCLUDE_PATH ?>public/js/buscaEstado.js"></script>
<script>
    function excluir() {
        document.getElementById('excluir').style.display = 'flex';
    }
    function alterar() {
        document.getElementById('alterar').style.display = 'flex';
    }
    function fecharAlterar() {
        document.getElementById('alterar').style.display = 'none';
    }
    function fecharExcluir() {
        document.getElementById('excluir').style.display = 'none';
    }
</script>
