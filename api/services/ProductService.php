<?php

require_once 'api/config/database.php';

class ProductService
{

    public static function create($data)
    {
        $pdo = Database::connect();

        try {
            $pdo->beginTransaction();

            // 🔹 1. produto
            $stmt = $pdo->prepare("
            INSERT INTO products 
            (uuid, descricao, desc_slug, categoria, preco_atual, preco_antigo, badge, posicao, inicio_exibicao, fim_exibicao, descricao_completa, peso, ativo, estoque_inicial)
            VALUES 
            (UUID(), ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");

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
                $data['ativo'] ? 1 : 0,
                $data['estoque_inicial'],
            ]);

            $produtoId = $pdo->lastInsertId();

            // 🔹 2. imagens (igual ao seu)
            $stmtImg = $pdo->prepare("
            INSERT INTO products_img (produto_id, imagem, destaque) 
            VALUES (?, ?, ?)
        ");

            $imagens = $data['imagens'] ?? [];
            $principalIndex = isset($data['imagem_principal']) ? (int)$data['imagem_principal'] : 0;

            if (count($imagens) === 1) {
                $principalIndex = 0;
            }

            foreach ($imagens as $index => $img) {
                $destaque = ($index === $principalIndex) ? 1 : 0;
                $stmtImg->execute([$produtoId, $img, $destaque]);
            }

            // 🔥 3. CRIA STOCK (SEM SOMAR)
            $stmtStock = $pdo->prepare("
            INSERT INTO products_stock 
            (produto_id, tamanho, estoque_inicial, minimo, estoque)
            VALUES (?, ?, ?, ?, ?)
        ");

            $tamanhosMov = [];

            foreach ($data['tamanhos'] as $item) {

                $qtd = (int) ($item['estoque_inicial'] ?? 0);

                // 🔹 grava snapshot inicial
                $stmtStock->execute([
                    $produtoId,
                    $item['tamanho'],
                    $qtd,
                    $item['estoque_minimo'] ?? 0,
                    $qtd,
                ]);

                // 🔹 prepara para movement
                if ($qtd > 0) {
                    $tamanhosMov[] = [
                        'tamanho'   => $item['tamanho'],
                        'quantidade' => $qtd
                    ];
                }
            }

            // 🔥 4. MOVEMENT (SEM AFETAR SALDO)
            if (!empty($tamanhosMov)) {

                // 🔴 IMPORTANTE: aqui NÃO pode atualizar estoque de novo
                // então criamos modo "somente histórico"

                foreach ($tamanhosMov as $item) {

                    $pdo->prepare("
                    INSERT INTO products_stock_movements
                    (produto_id, tamanho, tipo_movimento, quantidade, data_movimento, observacao, created_at)
                    VALUES (?, ?, 'saldo_inicial', ?, ?, ?, NOW())
                ")->execute([
                        $produtoId,
                        $item['tamanho'],
                        $item['quantidade'],
                        date('Y-m-d'),
                        'Estoque inicial do produto'
                    ]);
                }
            }

            // 🔹 tags (corrigi bug seu aqui 👇)
            if (!empty($data['tags'])) {
                $stmtTag = $pdo->prepare("
                INSERT INTO products_tags (produto_id, tag) VALUES (?, ?)
            ");

                foreach ($data['tags'] as $tag) {
                    $stmtTag->execute([$produtoId, $tag]);
                }
            }

            $pdo->commit();

            return $produtoId;
        } catch (Exception $e) {
            $pdo->rollBack();
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
                (
                    SELECT imagem 
                    FROM products_img pi 
                    WHERE pi.produto_id = p.id 
                    ORDER BY destaque DESC 
                    LIMIT 1
                ) as imagem_principal
            FROM products p
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
            sum(case when p.ativo = 1 then 1 else 0 end) as active,
            sum(case when p.ativo = 0 then 1 else 0 end) as inactive,
            COALESCE(MIN(p.preco_atual), 0) as min_price,
            COALESCE(MAX(p.preco_atual), 0) as max_price,
            COALESCE(AVG(p.preco_atual), 0) as med_price
        FROM products p
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

        $sql = "SELECT COUNT(*) as total FROM products $whereSql";

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

    public static function findByUuid($uuid)
    {
        $pdo = Database::connect();

        // 🔹 produto
        $stmt = $pdo->prepare("
        SELECT * FROM products WHERE uuid = ?
    ");
        $stmt->execute([$uuid]);

        $produto = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$produto) return null;

        // 👉 pega o ID real
        $produtoId = $produto['id'];

        // 🔹 imagens
        $stmt = $pdo->prepare("
        SELECT imagem AS nome, destaque 
        FROM products_img 
        WHERE produto_id = ?
        ORDER BY destaque DESC
    ");
        $stmt->execute([$produtoId]);
        $produto['imagens'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // 🔹 tamanhos + estoque
        $stmt = $pdo->prepare("
        SELECT tamanho, estoque_inicial, minimo 
        FROM products_stock 
        WHERE produto_id = ?
    ");
        $stmt->execute([$produtoId]);
        $produto['tamanhos'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // 🔹 tags
        $stmt = $pdo->prepare("
        SELECT tag 
        FROM products_tags 
        WHERE produto_id = ?
    ");
        $stmt->execute([$produtoId]);
        $produto['tags'] = $stmt->fetchAll(PDO::FETCH_COLUMN);

        return $produto;
    }


    public static function getSizesByProducts($ids)
    {
        if (empty($ids)) return [];

        $pdo = Database::connect();

        $in = implode(',', array_fill(0, count($ids), '?'));

        $stmt = $pdo->prepare("
        SELECT produto_id, tamanho, estoque
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
                'estoque' => $row['estoque']
            ];
        }

        return $grouped;
    }

    public static function update($id, $data)
    {
        $pdo = Database::connect();

        try {
            $pdo->beginTransaction();

            // =============================
            // 🔹 PRODUTO
            // =============================
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
            // 🔥 IMAGENS
            // =============================
            $stmt = $pdo->prepare("SELECT imagem FROM products_img WHERE produto_id = ?");
            $stmt->execute([$id]);
            $imagensBanco = $stmt->fetchAll(PDO::FETCH_COLUMN);

            $imagensManter = isset($data['imagens_existentes'])
                ? json_decode($data['imagens_existentes'], true)
                : [];

            if (!is_array($imagensManter)) {
                $imagensManter = [];
            }

            $imagensParaExcluir = array_diff($imagensBanco, $imagensManter);

            foreach ($imagensParaExcluir as $img) {
                $caminho = __DIR__ . '/../../uploads/products/' . $img;
                if (file_exists($caminho)) {
                    unlink($caminho);
                }
            }

            $pdo->prepare("DELETE FROM products_img WHERE produto_id = ?")->execute([$id]);

            $stmtImg = $pdo->prepare("
            INSERT INTO products_img (produto_id, imagem, destaque)
            VALUES (?, ?, ?)
        ");

            $imagensFinais = [];

            foreach ($imagensManter as $img) {
                $imagensFinais[] = $img;
            }

            if (!empty($data['imagens'])) {
                foreach ($data['imagens'] as $img) {
                    $imagensFinais[] = $img;
                }
            }

            $principalIndex = isset($data['imagem_principal'])
                ? (int)$data['imagem_principal']
                : 0;

            foreach ($imagensFinais as $index => $img) {
                $destaque = ($index === $principalIndex) ? 1 : 0;
                $stmtImg->execute([$id, $img, $destaque]);
            }

            // =============================
            // 🔥 ESTOQUE (SEU MODELO)
            // =============================

            $tamanhosFront = $data['tamanhos'] ?? [];

            foreach ($tamanhosFront as $item) {

                $tamanho     = $item['tamanho'];
                $qtdInicial  = (int)($item['estoque_inicial'] ?? 0);
                $minimo      = $item['estoque_minimo'] ?? 0;

                // 🔹 garante registro em stock
                $pdo->prepare("
                INSERT INTO products_stock (produto_id, tamanho, estoque_inicial, minimo)
                VALUES (?, ?, ?, ?)
                ON DUPLICATE KEY UPDATE 
                    estoque_inicial = VALUES(estoque_inicial),
                    minimo = VALUES(minimo)
            ")->execute([
                    $id,
                    $tamanho,
                    $qtdInicial,
                    $minimo
                ]);

                // 🔹 busca movimento inicial
                $stmt = $pdo->prepare("
                SELECT id 
                FROM products_stock_movements
                WHERE produto_id = ?
                AND tamanho = ?
                AND tipo_movimento = 'saldo_inicial'
                LIMIT 1
            ");
                $stmt->execute([$id, $tamanho]);

                $movId = $stmt->fetchColumn();

                if ($movId) {

                    // 🔥 atualiza movimento existente
                    $pdo->prepare("
                    UPDATE products_stock_movements
                    SET quantidade = ?
                    WHERE id = ?
                ")->execute([$qtdInicial, $movId]);
                } else {

                    // 🔥 cria se não existir
                    $pdo->prepare("
                    INSERT INTO products_stock_movements
                    (produto_id, tamanho, tipo_movimento, quantidade, data_movimento, observacao, created_at)
                    VALUES (?, ?, 'estoque_inicial', ?, ?, ?, NOW())
                ")->execute([
                        $id,
                        $tamanho,
                        $qtdInicial,
                        date('Y-m-d'),
                        'Estoque inicial (edição)'
                    ]);
                }

                // 🔥 recalcula saldo REAL
                $stmt = $pdo->prepare("
                SELECT 
                    COALESCE(SUM(
                        CASE 
                            WHEN tipo_movimento IN ('entrada', 'saldo_inicial', 'ajuste') THEN quantidade
                            WHEN tipo_movimento = 'saida' THEN -quantidade
                            ELSE 0
                        END
                    ), 0)
                FROM products_stock_movements
                WHERE produto_id = ? AND tamanho = ?
            ");

                $stmt->execute([$id, $tamanho]);
                $saldo = (int)$stmt->fetchColumn();

                // 🔹 atualiza saldo no stock
                $pdo->prepare("
                UPDATE products_stock
                SET estoque = ?
                WHERE produto_id = ? AND tamanho = ?
            ")->execute([$saldo, $id, $tamanho]);
            }

            // =============================
            // 🔹 TAGS
            // =============================
            $pdo->prepare("DELETE FROM products_tags WHERE produto_id = ?")->execute([$id]);

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
