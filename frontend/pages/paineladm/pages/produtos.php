<!doctype html>
<html lang="pt-BR">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1.0" />
  <title>Produtos</title>
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link
    href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;1,9..40,300&family=DM+Mono:wght@400;500&display=swap"
    rel="stylesheet" />
  <link rel="stylesheet" href="/overgrace/frontend/pages/paineladm/paineladm.css" />
  <link rel="stylesheet" href="/overgrace/frontend/pages/paineladm/pages/pages-css/produtos.css" />
  <link rel="stylesheet" href="/overgrace/frontend/css/utils.css">
</head>

<body>
  <script>
    window.parent.postMessage({
      type: "page",
      name: "produtos"
    }, "*");
  </script>

  <div class="shell">

    <?php include 'frontend/pages/paineladm/sidebar.php' ?>

    <div class="main">

      <?php include 'frontend/pages/paineladm/navbar.php' ?>

      <div class="page-content">
        <div class="page-header">
          <div class="page-header-left">
            <h1>Produtos</h1>
          </div>
          <div class="page-header-actions">
            <button class="btn btn-outline">Importar CSV</button>
            <button class="btn btn-primary" onclick="openModal()">
              <svg width="14" height="14" fill="none" viewBox="0 0 14 14" stroke="currentColor" stroke-width="2">
                <path d="M7 1v12M1 7h12" />
              </svg>
              Novo produto
            </button>
          </div>
        </div>

        <!-- Stats rápidas -->
        <div class="stats-mini">
          <div class="stat-mini">
            <div class="stat-mini-label">Total de produtos</div>
            <div class="stat-mini-val" id="qt_products">0</div>
            <div class="stat-mini-sub">Ativos: <span id="active">0</span> · Inativos: <span id="inactive">0</span></div>
          </div>
          <div class="stat-mini">
            <div class="stat-mini-label">Categorias</div>
            <div class="stat-mini-val">3</div>
            <div class="stat-mini-sub">Camisas · Bonés · Kits</div>
          </div>
          <div class="stat-mini">
            <div class="stat-mini-label">Preço médio</div>
            <div class="stat-mini-val">R$ <span id="med_price">0</span></div>
            <div class="stat-mini-sub">Mín R$<span id="min_price">0</span> · Máx R$<span id="max_price">0</span></div>
          </div>
        </div>

        <!-- Toolbar -->
        <div class="toolbar">
          <div class="search-box">
            <svg width="14" height="14" fill="none" viewBox="0 0 14 14" stroke="currentColor" stroke-width="1.5">
              <circle cx="6" cy="6" r="4" />
              <path d="M10 10l2.5 2.5" />
            </svg>
            <input type="text" id="filter-descricao" placeholder="Buscar produto, SKU…" />
          </div>
          <select class="filter-select" id="filter-categoria">
            <option value="">Todas as categorias</option>
            <option value="camisas">Camisas</option>
            <option value="bones">Bonés</option>
            <option values="kits">Kits</option>
          </select>
          <select class="filter-select" id="filter-ativo">
            <option value="">Todos os status</option>
            <option value="ativo">Ativo</option>
            <option value="inativo">Inativo</option>
          </select>
          <select class="filter-select" id="filter-order">
            <option value="id:desc">Mais recente</option>
            <option value="preco:asc">Menor preço</option>
            <option value="preco:desc">Maior preço</option>
            <option value="descricao:asc">Nome A–Z</option>
          </select>
        </div>

        <!-- Tabela -->
        <div class="table-wrap">
          <table>
            <thead>
              <tr>
                <th style="width: 40px">
                  <input type="checkbox" style="accent-color: var(--ink)" />
                </th>
                <th>Produto</th>
                <th>Categoria</th>
                <th>Badge</th>
                <th>Tamanhos</th>
                <th>Preço</th>
                <th>Início exibição</th>
                <th>Estoque</th>
                <th>Status</th>
                <th style="width: 100px">Ações</th>
              </tr>
            </thead>
            <tbody id="lista-produtos">

            </tbody>
          </table>
          <div class="table-footer">
            <span>Mostrando 5 de 8 produtos</span>
            <div class="pagination">
              <button class="page-btn active">1</button>
              <button class="page-btn">2</button>
              <button class="page-btn">→</button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>


  <!-- ── MODAL NOVO/EDITAR PRODUTO ──────────────────────── -->
  <div class="modal-overlay" id="modalOverlay" onclick="closeModalOutside(event)">
    <div class="modal-panel">
      <div class="modal-header">
        <span class="modal-title">Novo produto</span>
        <button class="modal-close" onclick="closeModal()">×</button>
      </div>
      <div class="modal-body">
        <form id="formProd">
          <div class="form-grid">
            <div class="form-section-title" style="grid-column: 1/-1">
              Identificação
            </div>

            <div class="form-group form-full">
              <label class="form-label">Nome do produto <span class="req">*</span></label>
              <input class="form-input" placeholder="Ex: Boné Estruturado Bege" id="f-name" />
            </div>
            <div class="form-group">
              <label class="form-label">ID / Slug <span class="req">*</span></label>
              <input class="form-input" placeholder="bone-estruturado-bege" id="f-id"
                style="font-family: var(--mono); font-size: 12px" />
              <span class="form-hint">Gerado automaticamente pelo nome. Usado na URL do
                produto.</span>
            </div>
            <div class="form-group">
              <label class="form-label">Categoria <span class="req">*</span></label>
              <select class="form-select" id="f-cat">
                <option value="">Selecionar...</option>
                <option value="camisas">Camisas</option>
                <option value="bones">Bonés</option>
                <option value="kits">Kits</option>
              </select>
            </div>

            <hr class="form-divider" style="grid-column: 1/-1" />
            <div class="form-section-title" style="grid-column: 1/-1">
              Preço & Badge
            </div>

            <div class="form-group">
              <label class="form-label">Preço atual (R$) <span class="req">*</span></label>
              <input class="form-input" type="number" placeholder="119.00" id="f-price" step="0.01" />
            </div>
            <div class="form-group">
              <label class="form-label">Preço antigo (R$)</label>
              <input class="form-input" type="number" placeholder="149.00 (opcional)" id="f-price-old" step="0.01" />
              <span class="form-hint">Preencha para exibir desconto riscado.</span>
            </div>
            <div class="form-group">
              <label class="form-label">Badge</label>
              <select class="form-select" id="f-badge">
                <option value="">Nenhum</option>
                <option value="Novo">Novo</option>
                <option value="-20%">-20%</option>
                <option value="Kit">Kit</option>
                <option value="Edição Limitada">Edição Limitada</option>
              </select>
            </div>
            <div class="form-group">
              <label class="form-label">Posição em destaque</label>
              <select class="form-select" id="f-position">
                <option value="">Sem destaque</option>
                <option value="1">1ª posição</option>
                <option value="2">2ª posição</option>
                <option value="3">3ª posição</option>
                <option value="4">4ª posição</option>
              </select>
              <span class="form-hint">Define onde aparece na grade de destaques da home.</span>
            </div>

            <hr class="form-divider" style="grid-column: 1/-1" />
            <div class="form-section-title" style="grid-column: 1/-1">
              Exibição & Disponibilidade
            </div>

            <div class="form-group">
              <label class="form-label">Início de exibição <span class="req">*</span></label>
              <input class="form-input" type="date" id="f-date-start" />
              <span class="form-hint">O produto aparece na loja a partir desta data.</span>
            </div>
            <div class="form-group">
              <label class="form-label">Fim de exibição</label>
              <input class="form-input" type="date" id="f-date-end" />
              <span class="form-hint">Deixe vazio para exibição permanente.</span>
            </div>

            <div class="form-group form-full">
              <label class="form-label">Tamanhos disponíveis <span class="req">*</span></label>
              <div class="size-selector" id="sizeSelector">
                <span class="size-opt">P</span>
                <span class="size-opt">M</span>
                <span class="size-opt">G</span>
                <span class="size-opt">GG</span>
                <span class="size-opt">GGG</span>
                <span class="size-opt">Único</span>
              </div>

            </div>

            <div id="reporSizesContainer">
              <!-- preenchido por JS -->
            </div>

            <hr class="form-divider" style="grid-column: 1/-1" />
            <div class="form-section-title" style="grid-column: 1/-1">
              Imagem
            </div>

            <div class="form-group">
              <label class="form-label">
                Buscar imagens <span class="req">*</span>
              </label>

              <input type="file" id="f-images" accept="image/*" multiple />

              <span class="form-hint">
                Recomendado: 600×600px, JPG ou WebP.
              </span>
            </div>

            <div class="form-group form-full">
              <div class="img-preview-wrap" id="imgPreviewWrap"></div>
            </div>


            <hr class="form-divider" style="grid-column: 1/-1" />
            <div class="form-section-title" style="grid-column: 1/-1">
              Descrição & Detalhes
            </div>

            <div class="form-group form-full">
              <label class="form-label">Descrição <span class="req">*</span></label>
              <textarea class="form-textarea" placeholder="Boné 5 painéis em ripstop de algodão…" id="f-desc"
                style="min-height: 96px"></textarea>
            </div>
            <div class="form-group">
              <label class="form-label">Material</label>
              <input class="form-input" placeholder="100% algodão ripstop" id="f-material" />
            </div>
            <div class="form-group">
              <label class="form-label">Peso (gramas)</label>
              <input class="form-input" type="number" placeholder="320" id="f-weight" />
              <span class="form-hint">Usado para cálculo de frete.</span>
            </div>
            <div class="form-group">
              <label class="form-label">Tags</label>
              <input class="form-input" placeholder="verão, algodão, structured" id="f-tags" />
              <span class="form-hint">Separadas por vírgula.</span>
            </div>
            <div class="form-group">
              <label class="form-label">Estoque inicial</label>
              <input class="form-input" type="number" placeholder="0" id="f-stock" />
            </div>

            <hr class="form-divider" style="grid-column: 1/-1" />
            <div class="form-section-title" style="grid-column: 1/-1">
              Visibilidade
            </div>

            <div class="form-group form-full">
              <div class="toggle-row">
                <div>
                  <div class="toggle-label">Produto ativo</div>
                  <div class="toggle-sub">
                    Quando inativo, não aparece na loja mesmo que a data de
                    início tenha passado.
                  </div>
                </div>
                <label class="toggle-wrap">
                  <input type="checkbox" id="f-active" />
                  <span class="toggle-track"></span>
                  <span class="toggle-thumb"></span>
                </label>
              </div>
            </div>
            <div class="form-group form-full" style="margin-top: 4px">
              <div class="toggle-row">
                <div>
                  <div class="toggle-label">Exibir na home (destaques)</div>
                  <div class="toggle-sub">
                    Aparece na seção "Destaques" da página inicial.
                  </div>
                </div>
                <label class="toggle-wrap">
                  <input type="checkbox" id="f-featured" />
                  <span class="toggle-track"></span>
                  <span class="toggle-thumb"></span>
                </label>
              </div>
            </div>
            <div class="form-group form-full" style="margin-top: 4px">
              <div class="toggle-row">
                <div>
                  <div class="toggle-label">Permitir compra sem estoque</div>
                  <div class="toggle-sub">
                    Aceita pedidos mesmo com estoque zerado (pré-venda).
                  </div>
                </div>
                <label class="toggle-wrap">
                  <input type="checkbox" id="f-backorder" />
                  <span class="toggle-track"></span>
                  <span class="toggle-thumb"></span>
                </label>
              </div>
            </div>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button class="btn btn-danger" onclick="closeModal()">
          Cancelar
        </button>
        <div style="display: flex; gap: 8px">
          <button class="btn btn-outline">Salvar rascunho</button>
          <button class="btn btn-primary" id="btn-add-prod" form="formProd">Publicar produto</button>
        </div>
      </div>
    </div>
  </div>
  <script src="frontend/js/modules/product/utils.js"></script>
  <script type="module" src="frontend/js/modules/product/lista.js"></script>
  <script type="module" src="frontend/js/modules/product/form.js"></script>

</body>

</html>