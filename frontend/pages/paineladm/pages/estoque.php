<!doctype html>
<html lang="pt-BR">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1.0" />
  <title>Estoque</title>
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link
    href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;1,9..40,300&family=DM+Mono:wght@400;500&display=swap"
    rel="stylesheet" />
  <link rel="stylesheet" href="/overgrace/frontend/pages/paineladm/paineladm.css" />
  <link rel="stylesheet" href="/overgrace/frontend/pages/paineladm/pages/pages-css/estoque.css">
</head>

<body>
  <script>
    window.parent.postMessage({
      type: "page",
      name: "estoque"
    }, "*");
  </script>


  <div class="shell">

    <?php include 'frontend/pages/paineladm/sidebar.php' ?>

    <div class="main">

      <?php include 'frontend/pages/paineladm/navbar.php' ?>

      <div class="page-content">
        <!-- ── HEADER ──────────────────────────────────────────── -->
        <div class="page-header">
          <div class="page-header-left">
            <h1>Estoque</h1>
            <p>Controle de quantidades por produto e tamanho</p>
          </div>
          <div class="page-header-actions">
            <button class="btn btn-outline">Exportar CSV</button>
            <button class="btn btn-primary" onclick="openRepor(null)">
              <svg
                width="14"
                height="14"
                fill="none"
                viewBox="0 0 14 14"
                stroke="currentColor"
                stroke-width="2">
                <path d="M7 1v12M1 7h12" />
              </svg>
              Registrar entrada
            </button>
          </div>
        </div>

        <!-- ── ALERTA ──────────────────────────────────────────── -->
        <div class="stock-alert">
          <svg
            width="16"
            height="16"
            fill="none"
            viewBox="0 0 16 16"
            stroke="currentColor"
            stroke-width="1.5">
            <path d="M8 2L1.5 13.5h13L8 2z" />
            <path d="M8 7v3M8 12v.5" />
          </svg>
          <span><strong>3 itens</strong> com estoque abaixo do mínimo recomendado (10
            unidades). Ação necessária.</span>
        </div>

        <!-- ── KPIs ────────────────────────────────────────────── -->
        <div class="stats-mini">
          <div class="stat-mini">
            <div class="stat-mini-label">Total de SKUs</div>
            <div class="stat-mini-val"><span id="qt_products">0</span></div>
            <div class="stat-mini-sub">Entre produtos e tamanhos</div>
          </div>
          <div class="stat-mini">
            <div class="stat-mini-label">Unidades em estoque</div>
            <div class="stat-mini-val"><span id="qt_stock">0</span></div>
            <div class="stat-mini-sub">Valor estimado R$ 0,00</div>
          </div>
          <div class="stat-mini">
            <div class="stat-mini-label">Estoque baixo</div>
            <div class="stat-mini-val" style="color: var(--amber)"><span id="qt_baixo">0</span></div>
            <div class="stat-mini-sub">Abaixo de 0 unidades</div>
          </div>
          <div class="stat-mini">
            <div class="stat-mini-label">Esgotados</div>
            <div class="stat-mini-val" style="color: var(--red)"><span id="qt_esgotado">0</span></div>
            <div class="stat-mini-sub">Zero unidades disponíveis</div>
          </div>
        </div>

        <!-- ── TOOLBAR ─────────────────────────────────────────── -->
        <div class="toolbar">
          <div class="search-box">
            <svg
              width="14"
              height="14"
              fill="none"
              viewBox="0 0 14 14"
              stroke="currentColor"
              stroke-width="1.5">
              <circle cx="6" cy="6" r="4" />
              <path d="M10 10l2.5 2.5" />
            </svg>
            <input
              type="text"
              placeholder="Buscar produto, SKU…"
              id="filter-descricao"
              oninput="filterStock()" />
          </div>
          <select
            class="filter-select"
            id="filter-categoria"
            onchange="filterStock()">
            <option value="">Todas as categorias</option>
            <option value="Camisas">Camisas</option>
            <option value="Bonés">Bonés</option>
            <option value="Kits">Kits</option>
          </select>
          <select
            class="filter-select"
            id="stockStatusFilter"
            onchange="filterStock()">
            <option value="">Todas as situações</option>
            <option value="ok">OK</option>
            <option value="low">Baixo</option>
            <option value="out">Esgotado</option>
          </select>
          <select class="filter-select" id="filter-order" onchange="filterStock()">
            <option value="name">Ordenar: nome A–Z</option>
            <option value="qty-asc">Menor estoque</option>
            <option value="qty-desc">Maior estoque</option>
          </select>
        </div>

        <!-- ── TABELA ──────────────────────────────────────────── -->
        <div class="table-wrap">
          <table id="stockTable">
            <thead>
              <tr>
                <th>Produto</th>
                <th>Tamanho</th>
                <th>Categoria</th>
                <th>Mínimo</th>
                <th>Estoque atual</th>
                <th>Última entrada</th>
                <th>Situação</th>
                <th style="width: 90px">Ação</th>
              </tr>
            </thead>
            <tbody id="lista-estoque">
              <!-- preenchido por JS -->
            </tbody>
          </table>
          <div class="table-footer">
            <span id="stockCount">Mostrando 0 itens</span>
            <div class="pagination">
              <button class="page-btn active">1</button>
              <button class="page-btn">2</button>
              <button class="page-btn">→</button>
            </div>
          </div>
        </div>

        <!-- ── HISTÓRICO DE MOVIMENTAÇÕES ──────────────────────── -->
        <div style="margin-top: 24px">
          <div class="card">
            <div class="card-header">
              <span class="card-title">Movimentações recentes</span>
              <button
                class="btn btn-ghost"
                style="font-size: 12px; color: var(--ink-3)">
                Ver tudo →
              </button>
            </div>
            <div class="card-body" style="padding: 0 20px">
              <div class="mov-row">
                <div class="mov-icon entrada">
                  <svg
                    width="12"
                    height="12"
                    fill="none"
                    viewBox="0 0 12 12"
                    stroke="currentColor"
                    stroke-width="2">
                    <path d="M6 1v10M1 6h10" />
                  </svg>
                </div>
                <div class="mov-desc">
                  <div class="mov-title">Entrada — Kit Camisa + Boné (P)</div>
                  <div class="mov-meta">
                    24/04/2025 · 14:02 · Reposição manual · Admin
                  </div>
                </div>
                <div class="mov-qty entrada">+20</div>
              </div>
              <div class="mov-row">
                <div class="mov-icon saida">
                  <svg
                    width="12"
                    height="12"
                    fill="none"
                    viewBox="0 0 12 12"
                    stroke="currentColor"
                    stroke-width="2">
                    <path d="M1 6h10" />
                  </svg>
                </div>
                <div class="mov-desc">
                  <div class="mov-title">
                    Saída — Camisa Linho Off-White (M) × 1
                  </div>
                  <div class="mov-meta">24/04/2025 · 11:18 · Pedido #10093</div>
                </div>
                <div class="mov-qty saida">−1</div>
              </div>
              <div class="mov-row">
                <div class="mov-icon saida">
                  <svg
                    width="12"
                    height="12"
                    fill="none"
                    viewBox="0 0 12 12"
                    stroke="currentColor"
                    stroke-width="2">
                    <path d="M1 6h10" />
                  </svg>
                </div>
                <div class="mov-desc">
                  <div class="mov-title">Saída — Kit Camisa + Boné (M) × 1</div>
                  <div class="mov-meta">24/04/2025 · 14:32 · Pedido #10094</div>
                </div>
                <div class="mov-qty saida">−1</div>
              </div>
              <div class="mov-row">
                <div class="mov-icon ajuste">
                  <svg
                    width="12"
                    height="12"
                    fill="none"
                    viewBox="0 0 12 12"
                    stroke="currentColor"
                    stroke-width="1.5">
                    <path d="M2 6h8M8 4l2 2-2 2" />
                  </svg>
                </div>
                <div class="mov-desc">
                  <div class="mov-title">Ajuste — Camisa Oversized Cáqui (G)</div>
                  <div class="mov-meta">
                    23/04/2025 · 09:30 · Correção de inventário · Admin
                  </div>
                </div>
                <div class="mov-qty saida">−3</div>
              </div>
              <div class="mov-row">
                <div class="mov-icon entrada">
                  <svg
                    width="12"
                    height="12"
                    fill="none"
                    viewBox="0 0 12 12"
                    stroke="currentColor"
                    stroke-width="2">
                    <path d="M6 1v10M1 6h10" />
                  </svg>
                </div>
                <div class="mov-desc">
                  <div class="mov-title">
                    Entrada — Boné Estruturado Bege (Único)
                  </div>
                  <div class="mov-meta">
                    22/04/2025 · 16:00 · Reposição de fornecedor
                  </div>
                </div>
                <div class="mov-qty entrada">+30</div>
              </div>
            </div>
          </div>
        </div>
      </div>

    </div>
  </div>

  <!-- /page -->

  <!-- ── MODAL REPOSIÇÃO ─────────────────────────────────── -->

  <form id="formEstoque">
    <div class="repor-modal" id="reporModal" onclick="closeReporOutside(event)">
      <div class="repor-panel">
        <div class="modal-header">
          <span class="modal-title" id="reporTitle">Registrar entrada de estoque</span>
          <button class="modal-close" onclick="closeRepor()">×</button>
        </div>

        <div class="modal-body" style="overflow-y: auto; flex: 1">
          <!-- Produto selecionado -->
          <div
            class="repor-section"
            id="reporProdutoWrap"
            style="padding-top: 4px">
            <div class="repor-produto-header" id="reporProdutoHeader">
              <img
                class="repor-produto-img"
                id="reporImg"
                src="https://images.unsplash.com/photo-1523381210434-271e8be1f52b?w=80&q=70"
                alt="" />
              <div>
                <div class="repor-produto-name" id="reporNome">
                  Kit Camisa + Boné
                </div>
                <div class="repor-produto-id" id="reporId">kit-camisa-bone</div>
              </div>
            </div>
          </div>

          <!-- Quando modal é aberto genérico (sem produto fixo) -->
          <div class="repor-section" id="reporSelectWrap" style="display: none">
            <h3>Produto</h3>
            <input
              class="form-select"
              id="f-name"
              readonly />

            </select>
          </div>

          <div class="repor-section">

            <div class="repor-info-row">
              <span class="repor-info-label">Data da entrada</span>
              <input
                type="date"
                class="form-input"
                id="f-date"
                style="width: 160px; padding: 5px 9px; font-size: 12px" />
            </div>
          </div>

          <!-- Tipo de movimentação -->
          <div class="repor-section">
            <h3>Tipo de movimentação</h3>
            <div style="display: flex; gap: 8px">
              <label
                style="
                    display: flex;
                    align-items: center; 
                    gap: 6px;
                    font-size: 13px;
                    cursor: pointer;
                  ">
                <input type="radio" name="movType" value="entrada" checked />
                Entrada (reposição)
              </label>
              <label
                style="
                    display: flex;
                    align-items: center;
                    gap: 6px;
                    font-size: 13px;
                    cursor: pointer;
                  ">
                <input type="radio" name="movType" value="ajuste" /> Ajuste
                manual
              </label>
            </div>
          </div>

          <!-- Quantidades por tamanho -->
          <div class="repor-section">
            <h3>Quantidades por tamanho</h3>
            <div id="reporSizesContainer">
              <!-- preenchido por JS -->
            </div>
          </div>

          <!-- Informações do fornecedor / lote -->
          <div class="repor-section" id="fornecedor-repor">
            <h3>Fornecedor &amp; lote</h3>
            <div class="repor-info-row">
              <span class="repor-info-label">Fornecedor</span>
              <input
                class="form-input"
                placeholder="Nome do fornecedor"
                style="width: 220px; padding: 5px 9px; font-size: 12px"
                id="f-fornecedor" />
            </div>
            <div class="repor-info-row">
              <span class="repor-info-label">Nº do lote</span>
              <input
                class="form-input"
                placeholder="LOT-2025-001"
                id="f-lote"
                style="
                    width: 220px;
                    padding: 5px 9px;
                    font-size: 12px;
                    font-family: var(--mono);
                  " />
            </div>
            <div class="repor-info-row">
              <span class="repor-info-label">Custo unitário (R$)</span>
              <input
                type="number"
                class="form-input"
                id="f-custo"
                placeholder="0,00"
                style="
                    width: 120px;
                    padding: 5px 9px;
                    font-size: 12px;
                    font-family: var(--mono);
                  " />
            </div>
          </div>

          <!-- Nota interna -->
          <div class="repor-section">
            <h3>Nota interna</h3>
            <textarea
              class="repor-nota"
              id="f-obs"
              placeholder="Observações sobre esta movimentação…"></textarea>
          </div>
        </div>

        <!-- INPUTS HIDDEM -->
         <input type="hidden" id="f-tipe-movement" value="entrada">
         <input type="hidden" id="f-product-id">

        <div class="modal-footer">
          <button class="btn btn-outline" onclick="closeRepor()">
            Cancelar
          </button>
          <div style="display: flex; gap: 8px">
            <button class="btn btn-primary" >
              Confirmar entrada
            </button>
          </div>
        </div>
      </div>
    </div>
  </form>

  <script src="frontend/js/modules/stock/utils.js"></script>
  <script type="module" src="frontend/js/modules/stock/lista.js"></script>
  <script type="module" src="frontend/js/modules/stock/form.js"></script>

</body>

</html>