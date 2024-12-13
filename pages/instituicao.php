<div class="container">
    <h2>Instituições do CompartilhaAção</h2>
    <br>
    <table class="instituicoes" style="width: 100%; border-collapse: collapse; text-align: left;">
        <thead>
            <tr>
                <th>Instituição</th>
                <th>Endereço</th>
                <th>Contato</th>
                <th>Descrição</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php 
                $instituicoes = Instituicao::obterTodasInstituicoes();

                if (!empty($instituicoes)) {
                    foreach ($instituicoes as $instituicao) {
                        echo "<tr 
                            data-estado='{$instituicao['estado']}'
                            data-cidade='{$instituicao['cidade']}'
                            data-rua='{$instituicao['rua']}'
                            data-numero='{$instituicao['numero']}'
                            data-bairro='{$instituicao['bairro']}'
                            data-cep='{$instituicao['cep']}'>";
                        
                        echo "<td>";
                        if (!empty($instituicao['img'])) {
                            echo "<div style='text-align: center;'>";
                            echo "<img src='{$instituicao['img']}' alt='Imagem da Instituição' style='width: 100px; height: auto;'>";
                            echo "<p style='margin-top: 8px; font-weight: bold;'>{$instituicao['nome_fantasia']}</p>";
                            echo "<p>{$instituicao['cnpj']}</p>";
                            echo "</div>";
                        } else {
                            echo "Sem imagem";
                        }
                        echo "</td>";
                        echo "<td class='endereco'></td>"; // Será preenchido pelo JS
                        echo "<td><i class=\"fa-solid fa-envelope\"></i> {$instituicao['email']}<br>
                             <i class=\"fa-solid fa-phone\"></i> {$instituicao['fone']}</td>";
                        echo "<td>{$instituicao['descricao']}</td>";
                        echo '<td><a href="necessidades?id='.$instituicao["id"].'"> Ver Necessidades</a></td>';
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='7'>Nenhuma instituição encontrada.</td></tr>";
                }
            ?>
        </tbody>
    </table>
</div>


<script>
    document.addEventListener('DOMContentLoaded', function () {
    const rows = document.querySelectorAll('tr[data-estado]');

    // Função para buscar estados
    function buscarEstados() {
        return fetch('https://servicodados.ibge.gov.br/api/v1/localidades/estados')
            .then(response => response.json());
    }

    // Atualizar tabela com os dados formatados
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