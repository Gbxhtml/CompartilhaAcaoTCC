<?php
$instituicoes = Instituicao::obterTodasInstituicoes();

if (!empty($instituicoes)): ?>
<div class="container">
    <h2>Instituições do CompartilhaAção</h2>
    <br>
    <div class="scroll-container">
        <table class="instituicoes" style="width: 100%; border-collapse: collapse; text-align: left;">
            <thead>
                <tr>
                    <th>Instituição</th>
                    <th>Endereço</th>
                    <th>Contato</th>
                    <th>Descrição</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($instituicoes as $instituicao): ?>
                    <tr data-estado="<?php echo $instituicao['estado']; ?>"
                        data-cidade="<?php echo $instituicao['cidade']; ?>" data-rua="<?php echo $instituicao['rua']; ?>"
                        data-numero="<?php echo $instituicao['numero']; ?>"
                        data-bairro="<?php echo $instituicao['bairro']; ?>" data-cep="<?php echo $instituicao['cep']; ?>">

                        <td>
                            <?php if (!empty($instituicao['img'])): ?>
                                <div style="text-align: center;">
                                    <img src="<?php echo $instituicao['img']; ?>" alt="Imagem da Instituição"
                                        style="width: 100px; height: auto;">
                                    <p style="margin-top: 8px; font-weight: bold;"><?php echo $instituicao['nome_fantasia']; ?>
                                    </p>
                                    <p><?php echo $instituicao['cnpj']; ?></p>
                                </div>
                            <?php else: ?>
                                Sem imagem
                            <?php endif; ?>
                        </td>
                        <td class="endereco"></td> <!-- Será preenchido pelo JS -->
                        <td>
                            <i class="fa-solid fa-envelope"></i> <?php echo $instituicao['email']; ?><br>
                            <i class="fa-solid fa-phone"></i> <?php echo $instituicao['fone']; ?>
                        </td>
                        <td><?php echo $instituicao['descricao']; ?></td>
                    </tr>

                    <!-- Mostrar Necessidades -->
                    <?php if (!empty(Instituicao::listarNecessidades($instituicao['id']))): ?>
                        <tr>
                            <td colspan="4">
                                <h4>Necessidades:</h4>
                                <table style="width: 100%; border-collapse: collapse; text-align: left;">
                                    <tbody>
                                        <?php foreach (Instituicao::listarNecessidades($instituicao['id']) as $necessidade): ?>
                                            <tr>
                                                <td>
                                                    <?php if (!empty($necessidade['img'])): ?>
                                                        <img src="<?php echo $necessidade['img']; ?>" alt="Imagem do item"
                                                            style="width: 100px; height: auto;">
                                                    <?php else: ?>
                                                        Sem imagem
                                                    <?php endif; ?>
                                                </td>
                                                <td><?php echo $necessidade['item']; ?></td>
                                                <td>R$ <?php echo number_format($necessidade['valor'], 2, ',', '.'); ?></td>
                                                <td>
                                                    <a 
                                                        style="background-color: rgb(30, 155, 94)"
                                                        target="_blank"
                                                        href="https://wa.me/<?php echo $instituicao['fone']; ?>?text=Olá, gostaria de ajudar a sua instituição com o item: <?php echo $necessidade['item']; ?>. Como podemos proceder?">
                                                        <i class="fa-brands fa-whatsapp"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                    <?php else: ?>
                        <tr>
                            <td colspan="4">Esta instituição não possui necessidades cadastradas.</td>
                        </tr>
                    <?php endif; ?>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php else: ?>
    <p>Nenhuma instituição encontrada.</p>
<?php endif; ?>




<script>
    document.addEventListener('DOMContentLoaded', function () {
        const rows = document.querySelectorAll('tr[data-estado]');

        function buscarEstados() {
            return fetch('https://servicodados.ibge.gov.br/api/v1/localidades/estados')
                .then(response => response.json());
        }

        buscarEstados().then(estados => {
            rows.forEach(row => {
                const estadoId = row.dataset.estado;
                const cidade = row.dataset.cidade;
                const estadoNome = estados.find(estado => estado.id == estadoId)?.nome || 'Estado desconhecido';

                const endereco = `
                ${row.dataset.rua || ''}, ${row.dataset.numero || ''} <br>
                ${row.dataset.bairro || ''}, ${cidade || ''}/${estadoNome} <br>
                CEP: ${row.dataset.cep || ''}`;

                row.querySelector('.endereco').innerHTML = endereco;
            });
        });
    });

</script>