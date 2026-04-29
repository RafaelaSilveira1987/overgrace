<?php

require_once 'api/config/database.php';

class StockService
{

    public static function create($data)
    {
        $pdo = Database::connect();

        try {
            $pdo->beginTransaction();

            $stmt = $pdo->prepare("
            INSERT INTO products_stock_movements 
            (produto_id, tamanho, tipo_movimento, quantidade, fornecedor, lote, data_movimento, custo_unitario, observacao, created_at)
            VALUES 
            (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
            ");

            foreach ($data['tamanhos'] as $item) {

                // 🔥 ignora quantidade 0
                if (empty($item['quantidade']) || $item['quantidade'] <= 0) {
                    continue;
                }

                $produtoId = $data['produto_id'];
                $tamanho   = $item['tamanho'];
                $quantidade = (int) $item['quantidade'];

                // 🔥 1. INSERE MOVIMENTO
                $stmt->execute([
                    $produtoId,
                    $tamanho,
                    $data['tipo_movimento'],
                    $quantidade,
                    $data['fornecedor'] ?? null,
                    $data['lote'] ?? null,
                    $data['data_movimento'],
                    $data['custo_unitario'] ?? null,
                    $data['observacao'] ?? null
                ]);

                // 🔥 2. DEFINE DELTA (impacto no estoque)
                $delta = 0;

                switch ($data['tipo_movimento']) {
                    case 'entrada':
                        $delta = $quantidade;
                        break;

                    case 'saida':
                        $delta = -$quantidade;
                        break;

                    case 'ajuste':
                        $delta = $quantidade; // pode ser negativo se quiser
                        break;
                }

                // 🔥 3. TENTA ATUALIZAR ESTOQUE
                $update = $pdo->prepare("
                UPDATE products_stock
                SET estoque = estoque + ?
                WHERE produto_id = ? AND tamanho = ?
            ");

                $update->execute([
                    $delta,
                    $produtoId,
                    $tamanho
                ]);

                // 🔥 4. SE NÃO EXISTE, CRIA
                if ($update->rowCount() === 0) {

                    $insert = $pdo->prepare("
                    INSERT INTO products_stock 
                    (produto_id, tamanho, estoque, estoque_inicial, created_at)
                    VALUES (?, ?, ?, 0, NOW())
                ");

                    $insert->execute([
                        $produtoId,
                        $tamanho,
                        $delta
                    ]);
                }

                // 🔥 5. VALIDA ESTOQUE NEGATIVO (importante)
                $check = $pdo->prepare("
                SELECT estoque 
                FROM products_stock
                WHERE produto_id = ? AND tamanho = ?
            ");

                $check->execute([$produtoId, $tamanho]);

                $estoqueAtual = (int) $check->fetchColumn();

                if ($estoqueAtual < 0) {
                    throw new Exception("Estoque negativo não permitido (Tamanho: {$tamanho})");
                }
            }

            $pdo->commit();

            return true;
        } catch (Exception $e) {
            $pdo->rollBack();
            throw $e;
        }
    }

    public static function movimentar($data, $pdo = null)
    {
        $interno = false;

        if (!$pdo) {
            $pdo = Database::connect();
            $pdo->beginTransaction();
            $interno = true;
        }

        try {

            foreach ($data['tamanhos'] as $item) {

                $produtoId = $data['produto_id'];
                $tamanho   = $item['tamanho'];
                $qtd       = (int) ($item['quantidade'] ?? 0);

                if ($qtd <= 0) continue;

                // 🔹 1. pega saldo atual
                $stmt = $pdo->prepare("
                    SELECT quantidade 
                    FROM products_stock 
                    WHERE produto_id = ? AND tamanho = ?
                    LIMIT 1
                ");
                $stmt->execute([$produtoId, $tamanho]);

                $atual = $stmt->fetchColumn();

                if ($atual === false) {
                    $atual = 0;

                    // cria registro se não existir
                    $pdo->prepare("
                        INSERT INTO products_stock (produto_id, tamanho, quantidade)
                        VALUES (?, ?, 0)
                    ")->execute([$produtoId, $tamanho]);
                }

                // 🔥 2. calcula novo saldo
                if ($data['tipo_movimento'] === 'saida') {
                    $novoSaldo = $atual - $qtd;
                } else {
                    $novoSaldo = $atual + $qtd;
                }

                // 🔥 (opcional) trava saldo negativo
                if ($novoSaldo < 0) {
                    throw new Exception("Estoque negativo para {$tamanho}");
                }

                // 🔹 3. atualiza saldo
                $pdo->prepare("
                    UPDATE products_stock
                    SET quantidade = ?
                    WHERE produto_id = ? AND tamanho = ?
                ")->execute([$novoSaldo, $produtoId, $tamanho]);

                // 🔹 4. grava movimento
                $pdo->prepare("
                    INSERT INTO products_stock_movements
                    (produto_id, tamanho, tipo_movimento, quantidade, data_movimento, observacao, created_at)
                    VALUES (?, ?, ?, ?, ?, ?, NOW())
                ")->execute([
                    $produtoId,
                    $tamanho,
                    $data['tipo_movimento'],
                    $qtd,
                    $data['data_movimento'] ?? date('Y-m-d'),
                    $data['observacao'] ?? null
                ]);
            }

            if ($interno) {
                $pdo->commit();
            }
        } catch (\Exception $e) {
            if ($interno) {
                $pdo->rollBack();
            }
            throw $e;
        }
    }

    public static function list($filters = [], $limit = 10, $offset = 0)
    {
        $pdo = Database::connect();

        $where = [];
        $params = [];

        // 🔎 Filtros dinâmicos
        if (!empty($filters['descricao'])) {
            $where[] = "descricao LIKE :descricao";
            $params[':descricao'] = "%" . $filters['descricao'] . "%";
        }

        if (!empty($filters['preco_min'])) {
            $where[] = "preco_atual >= :preco_min";
            $params[':preco_min'] = $filters['preco_min'];
        }

        if (!empty($filters['preco_max'])) {
            $where[] = "preco_atual <= :preco_max";
            $params[':preco_max'] = $filters['preco_max'];
        }

        if (!empty($filters['data_inicio'])) {
            $where[] = "inicio_exibicao >= :data_inicio";
            $params[':data_inicio'] = $filters['data_inicio'];
        }

        if (!empty($filters['data_fim'])) {
            $where[] = "inicio_exibicao <= :data_fim";
            $params[':data_fim'] = $filters['data_fim'];
        }

        if (!empty($filters['ativo'])) {
            $status = $filters['ativo'] == 'inativo' ? '0' : '1';
            $where[] = "ativo = $status";
        }

        if (!empty($filters['categoria'])) {
            $where[] = "LOWER(categoria) = LOWER(:categoria)";
            $params[':categoria'] = $filters['categoria'];
        }

        $where[] = "deleted_at IS NULL";

        // Monta WHERE
        $whereSql = "";
        if (!empty($where)) {
            $whereSql = "WHERE " . implode(" AND ", $where);
        }

        // 🔃 Ordenação dinâmica (segura)
        $orderBy = 'p.id'; // padrão
        $orderDir = 'DESC'; // padrão

        $allowedFields = [
            'id' => 'p.id',
            'descricao' => 'p.descricao',
            'preco' => 'p.preco_atual',
            'data' => 'p.inicio_exibicao'
        ];

        $allowedDirections = ['ASC', 'DESC'];

        if (!empty($filters['order_by']) && isset($allowedFields[$filters['order_by']])) {
            $orderBy = $allowedFields[$filters['order_by']];
        }

        if (!empty($filters['order_dir']) && in_array(strtoupper($filters['order_dir']), $allowedDirections)) {
            $orderDir = strtoupper($filters['order_dir']);
        }


        // Query final
        $sql = "
            SELECT 
                p.*,
                ps.tamanho,
                ps.minimo,
                ps.estoque,
                (
                    SELECT imagem 
                    FROM products_img pi 
                    WHERE pi.produto_id = p.id 
                    ORDER BY destaque DESC 
                    LIMIT 1
                ) as imagem_principal
            FROM products p
            JOIN products_stock ps on ps.produto_id = p.id
            $whereSql
            ORDER BY $orderBy $orderDir
            LIMIT :limit OFFSET :offset
        ";


        $stmt = $pdo->prepare($sql);

        // Bind dos filtros
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }

        // Bind paginação
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function totals()
    {
        $pdo = Database::connect();

        $sql = "
        SELECT 
            COUNT(*) as qt_products,
            sum(ps.estoque) as qt_stock,
            sum(case when ps.estoque < ps.minimo then 1 else 0 end) as qt_baixo,
            sum(case when ps.estoque = 0 then 1 else 0 end) as qt_zerados
        FROM products p
        JOIN products_stock ps on ps.produto_id = p.id
        where p.deleted_at IS NULL
        ";

        $stmt = $pdo->prepare($sql);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function count($filters = [])
    {
        $pdo = Database::connect();

        $where = [];
        $params = [];

        if (!empty($filters['descricao'])) {
            $where[] = "descricao LIKE :descricao";
            $params[':descricao'] = "%" . $filters['descricao'] . "%";
        }

        if (!empty($filters['preco_min'])) {
            $where[] = "preco_atual >= :preco_min";
            $params[':preco_min'] = $filters['preco_min'];
        }

        if (!empty($filters['preco_max'])) {
            $where[] = "preco_atual <= :preco_max";
            $params[':preco_max'] = $filters['preco_max'];
        }

        if (!empty($filters['data_inicio'])) {
            $where[] = "inicio_exibicao >= :data_inicio";
            $params[':data_inicio'] = $filters['data_inicio'];
        }

        if (!empty($filters['data_fim'])) {
            $where[] = "inicio_exibicao <= :data_fim";
            $params[':data_fim'] = $filters['data_fim'];
        }

        $whereSql = "";
        if (!empty($where)) {
            $whereSql = "WHERE " . implode(" AND ", $where);
        }

        $sql = "SELECT COUNT(*) as total FROM products
                JOIN products_stock on produto_id = products.id
                 $whereSql";

        $stmt = $pdo->prepare($sql);

        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }

        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }

    public static function findById($id)
    {
        $pdo = Database::connect();

        // 🔹 produto
        $stmt = $pdo->prepare("
        SELECT * FROM products WHERE id = ?
    ");
        $stmt->execute([$id]);

        $produto = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$produto) return null;

        // 🔹 imagens
        $stmt = $pdo->prepare("
            SELECT imagem AS nome, destaque 
            FROM products_img 
            WHERE produto_id = ?
            ORDER BY destaque DESC
        ");
        $stmt->execute([$id]);

        $produto['imagens'] = $stmt->fetchAll(PDO::FETCH_ASSOC);


        // 🔹 tamanhos + estoque
        $stmt = $pdo->prepare("
        SELECT tamanho, estoque_inicial, minimo FROM products_stock WHERE produto_id = ?
    ");
        $stmt->execute([$id]);
        $produto['tamanhos'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // 🔹 tags
        $stmt = $pdo->prepare("
        SELECT tag FROM products_tags WHERE produto_id = ?
    ");
        $stmt->execute([$id]);
        $produto['tags'] = $stmt->fetchAll(PDO::FETCH_COLUMN);

        return $produto;
    }

    public static function getSizesByProducts($ids)
    {
        if (empty($ids)) return [];

        $pdo = Database::connect();

        $in = implode(',', array_fill(0, count($ids), '?'));

        $stmt = $pdo->prepare("
        SELECT produto_id, tamanho
        FROM products_stock
        WHERE produto_id IN ($in)
    ");

        $stmt->execute($ids);

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // 🔹 agrupar por produto
        $grouped = [];

        foreach ($rows as $row) {
            $grouped[$row['produto_id']][] = [
                'tamanho' => $row['tamanho'],
                'estoque' => "0"
            ];
        }

        return $grouped;
    }

    public static function update($id, $data)
    {
        $pdo = Database::connect();

        try {
            $pdo->beginTransaction();

            // 🔹 produto principal
            $stmt = $pdo->prepare("
            UPDATE products SET
                descricao = ?,
                desc_slug = ?,
                categoria = ?,
                preco_atual = ?,
                preco_antigo = ?,
                badge = ?,
                posicao = ?,
                inicio_exibicao = ?,
                fim_exibicao = ?,
                descricao_completa = ?,
                peso = ?,
                ativo = ?,
                estoque_inicial = ?
            WHERE id = ?
        ");

            $ativo = isset($data['ativo'])
                ? filter_var($data['ativo'], FILTER_VALIDATE_BOOLEAN)
                : false;

            $stmt->execute([
                $data['nome'],
                $data['slug'],
                $data['categoria'],
                $data['preco'],
                $data['preco_antigo'],
                $data['badge'],
                $data['posicao'],
                $data['data_inicio'],
                $data['data_fim'],
                $data['descricao'],
                $data['peso'],
                $ativo ? 1 : 0,
                $data['estoque_inicial'],
                $id
            ]);

            // =============================
            // 🔥 IMAGENS (CORREÇÃO AQUI)
            // =============================

            // 🔹 imagens atuais no banco
            $stmt = $pdo->prepare("SELECT imagem FROM products_img WHERE produto_id = ?");
            $stmt->execute([$id]);
            $imagensBanco = $stmt->fetchAll(PDO::FETCH_COLUMN);

            // 🔹 imagens que o frontend quer manter
            $imagensManter = isset($data['imagens_existentes'])
                ? json_decode($data['imagens_existentes'], true)
                : [];

            if (!is_array($imagensManter)) {
                $imagensManter = [];
            }

            // 🔹 descobrir quais deletar (arquivo físico)
            $imagensParaExcluir = array_diff($imagensBanco, $imagensManter);

            foreach ($imagensParaExcluir as $img) {
                $caminho = __DIR__ . '/../../uploads/products/' . $img;

                if (file_exists($caminho)) {
                    unlink($caminho);
                }
            }

            // 🔥 agora pode limpar tabela
            $pdo->prepare("DELETE FROM products_img WHERE produto_id = ?")->execute([$id]);

            $stmtImg = $pdo->prepare("
                INSERT INTO products_img (produto_id, imagem, destaque)
                VALUES (?, ?, ?)
            ");

            // 🔹 junta todas (mantidas + novas)
            $imagensFinais = [];

            // antigas primeiro
            foreach ($imagensManter as $img) {
                $imagensFinais[] = $img;
            }

            // novas depois
            if (!empty($data['imagens'])) {
                foreach ($data['imagens'] as $img) {
                    $imagensFinais[] = $img;
                }
            }

            // 🔹 índice da principal
            $principalIndex = isset($data['imagem_principal'])
                ? (int)$data['imagem_principal']
                : 0;

            // 🔹 salva com destaque
            foreach ($imagensFinais as $index => $img) {
                $destaque = ($index === $principalIndex) ? 1 : 0;

                $stmtImg->execute([$id, $img, $destaque]);
            }


            // =============================
            // 🔥 RESTO (igual ao seu)
            // =============================

            $pdo->prepare("DELETE FROM products_stock WHERE produto_id = ?")->execute([$id]);
            $pdo->prepare("DELETE FROM products_tags WHERE produto_id = ?")->execute([$id]);

            // 🔹 tamanhos
            $stmtSize = $pdo->prepare("
                INSERT INTO products_stock 
                (produto_id, tamanho, estoque_inicial, minimo)
                VALUES (?, ?, ?, ?)
            ");

            foreach ($data['tamanhos'] as $item) {

                $stmtSize->execute([
                    $id,
                    $item['tamanho'],
                    $item['estoque_inicial'] ?? 0,
                    $item['estoque_minimo'] ?? 0
                ]);
            }


            // 🔹 tags
            if (!empty($data['tags'])) {
                $stmtTag = $pdo->prepare("
                INSERT INTO products_tags (produto_id, tag) VALUES (?, ?)
            ");

                foreach ($data['tags'] as $tag) {
                    $stmtTag->execute([$id, $tag]);
                }
            }

            $pdo->commit();

            return true;
        } catch (Exception $e) {
            $pdo->rollBack();
            throw $e;
        }
    }

    public static function delete($id)
    {
        $pdo = Database::connect();

        try {
            $pdo->beginTransaction();

            $stmt = $pdo->prepare("
            UPDATE products 
            SET 
                deleted_at = NOW()
            WHERE id = ?
            AND deleted_at IS NULL
        ");

            $stmt->execute([$id]);

            if ($stmt->rowCount() === 0) {
                throw new Exception("Produto não encontrado ou já deletado");
            }

            $pdo->commit();

            return true;
        } catch (Exception $e) {
            $pdo->rollBack();
            throw $e;
        }
    }

    public static function restore($id)
    {
        $pdo = Database::connect();

        $stmt = $pdo->prepare("
        UPDATE products 
        SET 
            deleted_at = NULL
        WHERE id = ?
        ");

        return $stmt->execute([$id]);
    }
}
