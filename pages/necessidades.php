<?php
if (isset($_GET['id'])) {
    $instituicao_id = $_GET['id'];
    $instituicao = Instituicao::obterInstituicaoPorId($instituicao_id); // Supondo que você tenha esse método
    $necessidades = Instituicao::listarNecessidades($instituicao_id);
} else {
    echo "Instituição não encontrada.";
    exit;
}
?>

<div class="container">
    <a class="voltar" href="instituicao"> <i class="fa-solid fa-arrow-left-long"></i> Voltar para a lista de Instituições</a>
    <h2>Necessidades da Instituição: <?php echo $instituicao['instituicao']['nome_fantasia']; ?></h2>
    
    <br><br>

    <?php if (!empty($necessidades)): ?>
        <table class="instituicoes" style="width: 100%; border-collapse: collapse; text-align: left;">
            <thead>
                <tr>
                    <th>Imagem</th>
                    <th>Item</th>
                    <th>Valor</th>
                    <th>Entrar em contato</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($necessidades as $necessidade): ?>
                    <tr>
                        <td>
                            <?php if (!empty($necessidade['img'])): ?>
                                <img src="<?php echo $necessidade['img']; ?>" alt="Imagem do item" style="width: 100px; height: auto;">
                            <?php else: ?>
                                Sem imagem
                            <?php endif; ?>
                        </td>
                        <td><?php echo $necessidade['item']; ?></td>
                        <td>R$ <?php echo number_format($necessidade['valor'], 2, ',', '.'); ?></td>
                        <td><a href="https://wa.me/<?php echo $instituicao['instituicao']['fone']?>?text=Gostaria de ajudar com o item: <?php echo $necessidade['item']?>"><i class="fa-brands fa-whatsapp"></i></a></td>
                        
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>Esta instituição não possui necessidades cadastradas.</p>
    <?php endif; ?>
</div>
