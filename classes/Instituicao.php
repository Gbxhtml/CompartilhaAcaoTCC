<?php

class Instituicao {
    public static function excluirInstituicao($idInstituicao) {
        try {
            $pdo = MySql::conectar();
            $pdo->beginTransaction();
    
            // Obtém o ID do endereço associado à instituição
            $sql = $pdo->prepare("SELECT endereco FROM `tb_instituicao` WHERE id = ?");
            $sql->execute([$idInstituicao]);
            $enderecoId = $sql->fetchColumn();
    
            // Exclui o endereço associado se existir
            if ($enderecoId) {
                $sql = $pdo->prepare("DELETE FROM `tb_endereco` WHERE id = ?");
                $sql->execute([$enderecoId]);
            }
    
            // Exclui a instituição
            $sql = $pdo->prepare("DELETE FROM `tb_instituicao` WHERE id = ?");
            $sql->execute([$idInstituicao]);
    
            $pdo->commit();
            return true;
        } catch (PDOException $e) {
            $pdo->rollBack();
            error_log("Erro ao excluir instituição: " . $e->getMessage());
            return false;
        }
    }
   
    public static function atualizarInstituicao($nomeFantasia, $descricao, $telefone, $senhaNova, $imagem, $endereco) {
        try {
            $pdo = MySql::conectar();

            $query = "UPDATE `tb_instituicao` SET nome_fantasia = ?, descricao = ?, fone = ?";
            $params = [$nomeFantasia, $descricao, $telefone];

            if ($senhaNova) {
                $query .= ", senha = ?";
                $params[] = $senhaNova;
            }

            if ($imagem) {
                $query .= ", img = ?";
                $params[] = $imagem;
            }

            $query .= " WHERE email = ?";
            $params[] = $_SESSION['email'];

            $sql = $pdo->prepare($query);
            $sql->execute($params);

            // Atualizar o endereço
            $sql = $pdo->prepare("UPDATE `tb_endereco` SET estado = ?, cidade = ?, bairro = ?, rua = ? WHERE id = ?");
            $sql->execute([
                $endereco['estado'],
                $endereco['cidade'],
                $endereco['bairro'],
                $endereco['rua'],
                $endereco['id']
            ]);

            $_SESSION['login'] = true;
            $_SESSION['nome'] = $nomeFantasia;
            $_SESSION['img'] = $imagem;
            $_SESSION['endereco'] = $endereco;

            return true;
        } catch (PDOException $e) {
            error_log("Erro ao atualizar instituição: " . $e->getMessage());
            return false;
        }
    }


    public static function verificarSenhaInstituicao($idInstituicao, $senha) {
        try {
            $pdo = MySql::conectar();
            $sql = $pdo->prepare("SELECT senha FROM `tb_instituicao` WHERE id = ?");
            $sql->execute([$idInstituicao]);
            $senhaAtual = $sql->fetchColumn();
    
            return $senhaAtual == $senha;
        } catch (PDOException $e) {
            error_log("Erro ao verificar senha da instituição: " . $e->getMessage());
            return false;
        }
    }
    
    

    public static function instituicaoExists($email) {
        try {
            $pdo = MySql::conectar();
            $sql = $pdo->prepare("SELECT id FROM tb_instituicao WHERE email = ?");
            $sql->execute([$email]);
            return $sql->rowCount() > 0;
        } catch (PDOException $e) {
            error_log("Erro ao verificar instituição: " . $e->getMessage());
            return false;
        }
    }

    public static function cadastrarInstituicao($estado, $cidade, $bairro, $rua, $numero, $cep, $email, $senha, $nome_fantasia, $descricao, $cnpj, $fone, $obs) {
        try {
            $imagem = "https://i.imgur.com/Fjq6LpJ.jpeg";
            $pdo = MySql::conectar();
            if (self::instituicaoExists($email)) {
                return ['success' => false, 'message' => 'O email já está em uso.'];
            }
            $pdo->beginTransaction();

            $sql = $pdo->prepare("INSERT INTO tb_endereco (estado, cidade, bairro, rua, numero, cep) VALUES (?, ?, ?, ?, ?, ?)");
            $sql->execute([$estado, $cidade, $bairro, $rua, $numero, $cep]);
            $enderecoId = $pdo->lastInsertId();

            $sql = $pdo->prepare("INSERT INTO tb_instituicao (endereco, email, senha, nome_fantasia, descricao, cnpj, fone, obs, img) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $sql->execute([$enderecoId, $email, $senha, $nome_fantasia, $descricao, $cnpj, $fone, $obs, $imagem]);

            $pdo->commit();
            return ['success' => true, 'message' => 'Cadastro de instituição realizado com sucesso!'];
        } catch (PDOException $e) {
            $pdo->rollBack();
            error_log("Erro ao cadastrar instituição: " . $e->getMessage());
            return ['success' => false, 'message' => 'Erro no cadastro de instituição.'];
        }
    }

    public static function login($email, $senha) {
        try {
            $sql = MySql::conectar()->prepare("SELECT * FROM `tb_instituicao` WHERE email = ?");
            $sql->execute(array($email));

            if ($sql->rowCount() == 1) {
                $info = $sql->fetch();
                if ($senha == $info['senha']) {
                    $_SESSION['login'] = true;
                    $_SESSION['email'] = $email;
                    $_SESSION['nome'] = $info['nome_fantasia'];
                    $_SESSION['senha'] = $info['senha'];
                    $_SESSION['img'] = $info['img'];
                    $_SESSION['tipo'] = 'instituicao';
                    $_SESSION['instituicao_id'] = $info['id'];
                    $_SESSION['endereco'] = $info['endereco'];
                    return true;
                } else {
                    error_log("Senha incorreta para o email: " . $email);
                    return false;
                }
            } else {
                error_log("Email não encontrado: " . $email);
                return false;
            }
        } catch (PDOException $e) {
            error_log("Erro ao realizar login: " . $e->getMessage());
            return false;
        }
    }

    public static function cnpjExists($cnpj) {
        try {
            $sql = MySql::conectar()->prepare("SELECT `id` FROM `tb_instituicao` WHERE cnpj = ?");
            $sql->execute(array($cnpj));
            return $sql->rowCount() > 0;
        } catch (PDOException $e) {
            error_log("Erro ao verificar existência do CNPJ: " . $e->getMessage());
            return false;
        }
    }

    public static function obterTodasInstituicoes() {
        try {
            $pdo = MySql::conectar();
            $sql = $pdo->query("
                SELECT 
                    i.id,
                    i.email,
                    i.descricao,
                    i.cnpj,
                    i.nome_fantasia,
                    i.fone,
                    i.img,
                    e.estado,
                    e.cidade,
                    e.bairro,
                    e.rua,
                    e.numero,
                    e.cep
                FROM `tb_instituicao` i
                LEFT JOIN `tb_endereco` e ON i.endereco = e.id
            ");
            return $sql->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erro ao obter instituições: " . $e->getMessage());
            return [];
        }
    }
    
    public static function adicionarNecessidade($instituicaoId, $item, $valor, $img = null) {
        try {
            $pdo = MySql::conectar();
            $sql = $pdo->prepare("INSERT INTO tb_instituicao_necessidades (instituicao_id, item, valor, img) VALUES (?, ?, ?, ?)");
            $sql->execute([$instituicaoId, $item, $valor, $img]);
            return ['method' => 'success', 'message' => 'Item adicionado com sucesso!'];
        } catch (PDOException $e) {
            error_log("Erro ao adicionar necessidade: " . $e->getMessage());
            return ['method' => 'error', 'message' => 'Erro ao adicionar item.'];
        }
    }

    public static function listarNecessidades($instituicaoId) {
        try {
            $pdo = MySql::conectar();
            $sql = $pdo->prepare("SELECT * FROM tb_instituicao_necessidades WHERE instituicao_id = ?");
            $sql->execute([$instituicaoId]);
            return $sql->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erro ao listar necessidades: " . $e->getMessage());
            return [];
        }
    }

    public static function obterInstituicao($email) {
        try {
            $pdo = MySql::conectar();
            $sql = $pdo->prepare("SELECT * FROM `tb_instituicao` WHERE email = ?");
            $sql->execute(array($email));
            $instituicao = $sql->fetch(PDO::FETCH_ASSOC);
    
            if (!$instituicao) {
                return [];
            }
    
            $sql = $pdo->prepare("SELECT * FROM `tb_endereco` WHERE id = ?");
            $sql->execute(array($instituicao['endereco']));
            $endereco = $sql->fetch(PDO::FETCH_ASSOC);
    
            return ['usuario' => $instituicao, 'endereco' => $endereco];
        } catch (PDOException $e) {
            error_log("Erro ao obter informações da instituição: " . $e->getMessage());
            return [];
        }
    }    

    public static function cadastrarNecessidade($instituicaoId, $descricao) {
        try {
            $pdo = MySql::conectar();
            $sql = $pdo->prepare("INSERT INTO tb_instituicao_necessidades (instituicao_id, descricao) VALUES (?, ?)");
            $sql->execute([$instituicaoId, $descricao]);
            return ['success' => true, 'message' => 'Necessidade cadastrada com sucesso!'];
        } catch (PDOException $e) {
            error_log("Erro ao cadastrar necessidade: " . $e->getMessage());
            return ['success' => false, 'message' => 'Erro ao cadastrar necessidade.'];
        }
    }
    
    public static function removerNecessidade($idNecessidade) {
        try {
            $pdo = MySql::conectar();
            $sql = $pdo->prepare("DELETE FROM tb_instituicao_necessidades WHERE id = ?");
            $sql->execute([$idNecessidade]);
    
            if ($sql->rowCount() > 0) {
                return ['method' => 'success', 'message' => 'Necessidade removida com sucesso!'];
            } else {
                return ['method' => 'error', 'message' => 'Necessidade não encontrada.'];
            }
        } catch (PDOException $e) {
            error_log("Erro ao remover necessidade: " . $e->getMessage());
            return ['method' => 'error', 'message' => 'Erro ao remover necessidade.'];
        }
    }

    public static function obterInstituicaoPorId($instituicao_id) {
        try {
            $pdo = MySql::conectar();
            
            // Buscar os dados da instituição
            $sql = $pdo->prepare("SELECT * FROM `tb_instituicao` WHERE id = ?");
            $sql->execute([$instituicao_id]);
            $instituicao = $sql->fetch(PDO::FETCH_ASSOC);
    
            if (!$instituicao) {
                return [];
            }
    
            // Buscar o endereço associado à instituição
            $sql = $pdo->prepare("SELECT * FROM `tb_endereco` WHERE id = ?");
            $sql->execute([$instituicao['endereco']]);
            $endereco = $sql->fetch(PDO::FETCH_ASSOC);
    
            return ['instituicao' => $instituicao, 'endereco' => $endereco];
        } catch (PDOException $e) {
            error_log("Erro ao obter instituição por ID: " . $e->getMessage());
            return [];
        }
    }
    

    public static function obterNecessidade($idNecessidade) {
        try {
            $pdo = MySql::conectar();
            $sql = $pdo->prepare("SELECT * FROM tb_instituicao_necessidades WHERE id = ?");
            $sql->execute([$idNecessidade]);
            
            $necessidade = $sql->fetch(PDO::FETCH_ASSOC);
            
            if (!$necessidade) {
                return ['method' => 'error', 'message' => 'Necessidade não encontrada.'];
            }
            
            return ['method' => 'success', 'data' => $necessidade];
        } catch (PDOException $e) {
            error_log("Erro ao obter necessidade: " . $e->getMessage());
            return ['method' => 'error', 'message' => 'Erro ao obter necessidade.'];
        }
    }
    
    
    public static function pegarNecessidades($instituicaoId) {
        try {
            $pdo = MySql::conectar();
            $sql = $pdo->prepare("SELECT * FROM tb_instituicao_necessidades WHERE instituicao_id = ?");
            $sql->execute([$instituicaoId]);
            $necessidades = $sql->fetchAll(PDO::FETCH_ASSOC);
            return $necessidades;
        } catch (PDOException $e) {
            error_log("Erro ao buscar necessidades: " . $e->getMessage());
            return [];
        }
    }
    
}
