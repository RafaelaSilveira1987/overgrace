<!doctype html>
<html lang="pt-BR">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1.0" />
  <title>Pedidos</title>
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link
    href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;1,9..40,300&family=DM+Mono:wght@400;500&display=swap"
    rel="stylesheet" />
  <link rel="stylesheet" href="/overgrace/frontend/pages/paineladm/paineladm.css" />
  <link rel="stylesheet" href="/overgrace/frontend/pages/paineladm/pages/pages-css/pedidos.css">

</head>

<body>
  <script>
    window.parent.postMessage({
      type: "page",
      name: "pedidos"
    }, "*");
  </script>

  <div class="shell">

    <?php include 'frontend/pages/paineladm/sidebar.php' ?>

    <div class="main">

      <?php include 'frontend/pages/paineladm/navbar.php' ?>

      <div class="page-content">
        <div class="page-header">
          <div class="page-header-left">
            <h1>Pedidos</h1>
            <p>94 pedidos em abril · 7 aguardando ação</p>
          </div>
          <div class="page-header-actions">
            <button class="btn btn-outline">Exportar</button>
          </div>
        </div>

        <!-- KPIs de status -->
        <div class="kpi-grid" style="grid-template-columns: repeat(4, 1fr)">
          <div class="kpi">
            <div class="kpi-label">Todos</div>
            <div class="kpi-value" id="qt_orders">0</div>
          </div>
          <div class="kpi">
            <div class="kpi-label">Pendentes</div>
            <div class="kpi-value" style="color: var(--amber)" id="qt_pend">0</div>
          </div>
          <div class="kpi">
            <div class="kpi-label">Enviados</div>
            <div class="kpi-value" style="color: var(--blue)" id="qt_env">0</div>
          </div>
          <div class="kpi">
            <div class="kpi-label">Cancelados</div>
            <div class="kpi-value" style="color: var(--red)" id="qt_canc">0</div>
          </div>
        </div>

        <!-- Toolbar -->
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
            <input type="text" id="filter-descricao" placeholder="Buscar pedido, cliente…" />
          </div>
          <select class="filter-select" id="filter-status">
            <option value="">Todos os status</option>
            <option value="paid">Pago</option>
            <option value="pending">Pendente</option>
            <option value="envied">Enviado</option>
            <option value="canceled">Cancelado</option>
          </select>
          <select class="filter-select" id="filter-order">
            <option>Mais recentes</option>
            <option>Mais antigos</option>
            <option>Maior valor</option>
          </select>
        </div>

        <!-- Tabela -->
        <div class="table-wrap">
          <table>
            <thead>
              <tr>
                <th>Pedido</th>
                <th>Data</th>
                <th>Cliente</th>
                <th>Itens</th>
                <th>Total</th>
                <th>Pagamento</th>
                <th>Status</th>
                <th>Ação</th>
              </tr>
            </thead>
            <tbody id="lista-pedidos">

            </tbody>
          </table>
          <div class="table-footer">
            <span>Mostrando 7 de 94 pedidos</span>
            <div class="pagination">
              <button class="page-btn active">1</button>
              <button class="page-btn">2</button>
              <button class="page-btn">3</button>
              <button class="page-btn">…</button>
              <button class="page-btn">14</button>
              <button class="page-btn">→</button>
            </div>
          </div>
        </div>
      </div>

    </div>
  </div>

  <!-- ── MODAL DETALHE DO PEDIDO ────────────────────────── -->
  <div
    class="order-detail-modal"
    id="orderModal"
    onclick="closeDetailOutside(event)">
    <div class="order-detail-panel">
      <div class="modal-header">
        <span class="modal-title" id="modalOrderNum">Pedido #10094</span>
        <button class="modal-close" onclick="closeDetail()">×</button>
      </div>
      <div class="modal-body" style="overflow-y: auto; flex: 1">

        <form id="formOrder">

          <div class="order-detail-section">
            <h3>Status do pedido</h3>
            <select class="status-select" id="orderStatusSelect">
              <option value="pago">Pago</option>
              <option value="pendente">Pendente</option>
              <option value="enviado" selected>Enviado</option>
              <option value="cancelado">Cancelado</option>
            </select>
          </div>

          <div class="order-detail-section">
            <h3>Itens do pedido</h3>
            <div id="orderItemsList">
              <div class="order-item-line">
                <img
                  class="order-item-img"
                  src="https://images.unsplash.com/photo-1523381210434-271e8be1f52b?w=80&q=70"
                  alt="" />
                <div class="order-item-name">
                  Kit Camisa + Boné<br /><span
                    style="
                      font-size: 11px;
                      color: var(--ink-3);
                      font-weight: 400;
                    ">Tamanho M</span>
                </div>
                <span class="order-item-qty">× 1</span>
                <span class="order-item-price">R$ 259,00</span>
              </div>
            </div>
          </div>

          <div class="order-detail-section">
            <h3>Resumo financeiro</h3>
            <div class="order-detail-row">
              <span class="order-detail-label">Subtotal</span><span>R$ 259,00</span>
            </div>
            <div class="order-detail-row">
              <span class="order-detail-label">Frete</span><span style="color: var(--green)">Grátis</span>
            </div>
            <div class="order-detail-row" style="font-weight: 500">
              <span class="order-detail-label">Total</span><span>R$ 259,00</span>
            </div>
          </div>

          <div class="order-detail-section">
            <h3>Cliente</h3>
            <div class="order-detail-row">
              <span class="order-detail-label">Nome</span><span id="detailClientName">Ana Beatriz Souza</span>
            </div>
            <div class="order-detail-row">
              <span class="order-detail-label">E-mail</span><span>ana.beatriz@gmail.com</span>
            </div>
            <div class="order-detail-row">
              <span class="order-detail-label">Telefone</span><span>(11) 98888-0000</span>
            </div>
          </div>

          <div class="order-detail-section">
            <h3>Endereço de entrega</h3>
            <div class="order-detail-row">
              <span class="order-detail-label">Rua</span><span>Av. Paulista, 1000 — Apto 42</span>
            </div>
            <div class="order-detail-row">
              <span class="order-detail-label">Bairro</span><span>Bela Vista</span>
            </div>
            <div class="order-detail-row">
              <span class="order-detail-label">Cidade/UF</span><span>São Paulo — SP</span>
            </div>
            <div class="order-detail-row">
              <span class="order-detail-label">CEP</span><span>01310-100</span>
            </div>
          </div>

          <div class="order-detail-section">
            <h3>Rastreamento</h3>
            <div class="order-detail-row">
              <span class="order-detail-label">Transportadora</span><span>Correios PAC</span>
            </div>
            <div class="order-detail-row">
              <span class="order-detail-label">Código</span><span style="font-family: var(--mono); font-size: 12px">BR1234567890BR</span>
            </div>
          </div>

        </form>

      </div>
      <div class="modal-footer">
        <button class="btn btn-outline" onclick="closeDetail()">
          Fechar
        </button>
        <div style="display: flex; gap: 8px">
          <button class="btn btn-outline">Imprimir</button>
          <button class="btn btn-primary">Salvar status</button>
        </div>
      </div>
    </div>
  </div>
  <script type="module" src="frontend/js/modules/order/listaAdmin.js"></script>
  <script type="module" src="frontend/js/modules/order/formAdmin.js"></script>
</body>

</html>