<?php
if ($_SESSION['tipo'] === 'user') {
    $tipoPerfil = 'do Usuário';
    $usuarioData = Usuario::obterUsuario($_SESSION['email']);
    $endereco = $usuarioData['endereco'];
    $usuario = $usuarioData['usuario'];
} else {
    $tipoPerfil = 'da Instituição';
    $usuarioData = Instituicao::obterInstituicao($_SESSION['email']);
    $endereco = $usuarioData['endereco'];
    $usuario = $usuarioData['usuario'];
}

function obterEstados()
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://servicodados.ibge.gov.br/api/v1/localidades/estados');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);
    return json_decode($response, true);
}

function obterNomeEstado($idEstado)
{
    $estados = obterEstados();
    foreach ($estados as $estado) {
        if ($estado['id'] == $idEstado) {
            return $estado['nome'];
        }
    }
    return 'Estado não encontrado';
}

$nomeEstado = obterNomeEstado($endereco['estado']);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['atualizar'])) {
    $senha = $_POST['senha'];
    $senhaNova = $_POST['senhaNova'];
    $nome = $_POST['nome'];
    $telefone = $_POST['phone'];

    $enderecoAtualizado = [
        'estado' => $_POST['estado'],
        'cidade' => $_POST['cidade'],
        'bairro' => $_POST['bairro'],
        'rua' => $_POST['rua'],
        'id' => $endereco['id'],
    ];

    if ($_SESSION['tipo'] == 'user') {
        $senhaCorreta = Usuario::verificarSenhaUsuario($usuario['id'], $senha);
        if ($senhaCorreta) {
            $atualizado = Usuario::atualizarUsuario($nome, $telefone, $senhaNova, $enderecoAtualizado);
        }
    } else {
        $descricao = $_POST['descricao'];
        $imagem = $usuario['img'];

        if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] === UPLOAD_ERR_OK) {
            $imagemTemp = $_FILES['imagem']['tmp_name'];
            $imagemNome = uniqid() . '-' . $_FILES['imagem']['name'];
            $caminhoDestino = 'uploads/' . $imagemNome;

            if (move_uploaded_file($imagemTemp, $caminhoDestino)) {
                $imagem = $caminhoDestino;
            }
        }

        $senhaCorreta = Instituicao::verificarSenhaInstituicao($usuario['id'], $senha);
        if ($senhaCorreta) {
            $atualizado = Instituicao::atualizarInstituicao($nome, $descricao, $telefone, $senhaNova, $imagem, $enderecoAtualizado);
        }
    }

    if (isset($atualizado) && $atualizado) {
        header("Location: " . INCLUDE_PATH . "perfil");
        exit;
    } else {
        $erroMensagem = 'Erro ao atualizar o perfil. Verifique os dados e tente novamente.';
    }
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
            <h2>Editar Perfil <?php echo $tipoPerfil; ?></h2>
            <form method="post" enctype="multipart/form-data">
                <div class="edit-profile">
                    <label for="nome">Nome do perfil:</label>
                    <input type="text" name="nome"
                        value="<?php echo $_SESSION['tipo'] === 'user' ? $usuario['nome'] : $usuario['nome_fantasia']; ?>">

                    <?php if ($_SESSION['tipo'] !== 'user'): ?>
                        <label for="imagem">Imagem da instituição:</label>
                        <input class="img-input" type="file" name="imagem">
                        <label for="descricao">Descrição da instituição:</label>
                        <textarea name="descricao"><?php echo $usuario['descricao'] ?? ''; ?></textarea>
                    <?php endif; ?>

                    <label for="email">Email:</label>
                    <input type="email" name="email" value="<?php echo $usuario['email']; ?>" readonly>

                    <label for="senhaNova">Nova Senha:</label>
                    <input type="password" name="senhaNova" placeholder="Digite para alterar">

                    <h3>Endereço:</h3>
                    <label for="estado">Estado:</label>
                    <select id="estado" name="estado">
                        <option value="<?php echo $endereco['estado']; ?>" selected><?php echo $nomeEstado; ?></option>
                    </select>

                    <label for="cidade">Cidade:</label>
                    <select id="cidade" name="cidade">
                        <option value="<?php echo $endereco['cidade']; ?>" selected><?php echo $endereco['cidade']; ?>
                        </option>
                    </select>

                    <label for="bairro">Bairro:</label>
                    <input type="text" name="bairro" value="<?php echo $endereco['bairro']; ?>">

                    <label for="rua">Rua:</label>
                    <input type="text" name="rua" value="<?php echo $endereco['rua']; ?>">

                    <?php if ($_SESSION['tipo'] === 'user'): ?>
                        <label for="cpf">CPF:</label>
                        <input type="text" name="cpf" value="<?php echo $usuario['cpf']; ?>" readonly>
                    <?php else: ?>
                        <label for="cnpj">CNPJ:</label>
                        <input type="text" name="cnpj" value="<?php echo $usuario['cnpj']; ?>" readonly>
                    <?php endif; ?>

                    <label for="phone">Telefone:</label>
                    <input type="tel" name="phone" value="<?php echo $usuario['fone']; ?>">

                    <input type="hidden" name="atualizar" value="1">
                    <!-- Central de Conta -->

                    <!-- Modal Atualizar -->
                    <div id="alterar" class="confirmar">
                        <div class="card">
                            <div class="flex space-between">
                                <h2>Deseja mesmo prosseguir?</h2>
                                <a class="fechar" onclick="fecharAlterar()"><span><i
                                            class="fa-solid fa-circle-xmark"></i></span>
                                </a>
                            </div>
                            <label for="senha">Insira sua senha:</label>
                            <input type="senha" name="senha" class="input">
                            <input type="hidden" name="atualizar">
                            <input type="submit" value="Salvar Alterações">
                        </div>
                    </div>
                </div>
            </form>

        </section>

        <section class="user">
            <div class="user-center">
                <a style="background-color:rgb(18, 165, 94)" onclick="alterar()">Salvar Alterações</a>
                <a style="background-color:rgb(196, 24, 24)" onclick="excluir()">Excluir Conta</a>
            </div>
        </section>



        <!-- Modal Excluir -->
        <div id="excluir" class="confirmar">
            <div class="card">
                <div class="flex space-between">
                    <h2>Deseja mesmo excluir?</h2>
                    <a class="fechar" onclick="fecharExcluir()"><span><i
                                class="fa-solid fa-circle-xmark"></i></span></a>
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