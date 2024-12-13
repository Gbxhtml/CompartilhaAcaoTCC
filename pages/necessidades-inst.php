<div class="container">
    <h2>Necessidades da sua Instituição.</h2>
    <?php
        if (isset($_GET['excluir_id']) && !isset($_POST['adicionar_necessidade'])) {
            $id = $_GET['excluir_id'];
            $resultado = Instituicao::removerNecessidade($id);
            echo "<p class='{$resultado['method']}'>{$resultado['message']}</p>";
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['adicionar_necessidade'])) {
            $item = $_POST['item'];
            $valor = $_POST['valor'];
            $img = null;

            // Verificar se um arquivo foi enviado
            if (isset($_FILES['img']) && $_FILES['img']['error'] === UPLOAD_ERR_OK) {
                $imgTemp = $_FILES['img']['tmp_name'];
                $imgNome = uniqid() . '-' . $_FILES['img']['name'];
                $caminhoDestino = 'uploads/' . $imgNome;

                // Tenta mover o arquivo enviado para o diretório de uploads
                if (move_uploaded_file($imgTemp, $caminhoDestino)) {
                    $img = $caminhoDestino;
                } else {
                    echo "<p class='error'>Erro ao fazer upload da imagem!</p>";
                }
            }

            // Adiciona a necessidade no banco de dados
            $resultado = Instituicao::adicionarNecessidade($_SESSION['instituicao_id'], $item, $valor, $img);
            echo "<p class='{$resultado['method']}'>{$resultado['message']}</p>";
        }
    ?>

    <form method="POST" enctype="multipart/form-data" class="item-necs">
        <label for="item">O item necessário:</label>
        <input type="text" name="item" required>

        <label for="valor">O valor do item:</label>
        <input type="number" step="0.01" name="valor" required>

        <label for="img">Imagem do item:</label>
        <input type="file" name="img">

        <button class="confirm" type="submit" name="adicionar_necessidade">Adicionar</button>
    </form>

    <section class="items">
        <table class="instituicoes">
            <thead>
                <tr>
                    <th>Imagem</th>
                    <th>Item</th>
                    <th>Valor</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    $necessidades = Instituicao::listarNecessidades($_SESSION['instituicao_id']);
                    foreach ($necessidades as $item) {
                        echo "
                            <tr>
                                <td><img src='{$item['img']}' alt='{$item['item']}' class='item-img' /></td>
                                <td>{$item['item']}</td>
                                <td>R$ {$item['valor']}</td>
                                <td>
                                    <a href='?excluir_id={$item['id']}' class='excluir'>Excluir</a>
                                </td>
                            </tr>
                        ";
                    }
                ?>
            </tbody>
        </table>
    </section>
</div>
