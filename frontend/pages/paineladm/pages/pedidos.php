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
  <link rel="stylesheet" href="/overgrace/frontend/pages/paineladm/paineladm.css?v=22" />
  <link rel="stylesheet" href="/overgrace/frontend/pages/paineladm/pages/pages-css/pedidos.css">

  <link rel="stylesheet" href="/overgrace/frontend/css/backend-pix-compat.css">
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
            <p id="ordersSubtitle">Carregando pedidos...</p>
          </div>
          <div class="page-header-actions">
            <button class="btn btn-outline" id="btnExportOrders" type="button">Exportar CSV</button>
            <button class="btn btn-outline" id="btnExportOrdersPdf" type="button">Exportar PDF</button>
          </div>
        </div>

        <div id="paymentReviewAlert" class="payment-review-alert" hidden>
          <strong>Pagamentos que exigem revisão</strong>
          <span id="paymentReviewAlertText"></span>
          <button type="button" id="paymentReviewFilterBtn">Ver pedidos</button>
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
            <option value="processing">Em preparação</option>
            <option value="shipped">Enviado</option>
            <option value="delivered">Entregue</option>
            <option value="canceled">Cancelado</option>
            <option value="expired">Expirado</option>
            <option value="payment_review">Revisão de pagamento</option>
          </select>
          <select class="filter-select" id="filter-order">
            <option value="created_at:DESC">Mais recentes</option>
            <option value="created_at:ASC">Mais antigos</option>
            <option value="total_amount:DESC">Maior valor</option>
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
            <span>Carregando...</span>
            <div class="pagination"></div>
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
              <option value="pending">Pendente</option>
              <option value="paid" disabled>Pago (confirmado pelo Mercado Pago)</option>
              <option value="processing">Em preparação</option>
              <option value="shipped">Enviado</option>
              <option value="delivered">Entregue</option>
              <option value="canceled">Cancelado</option>
              <option value="expired" disabled>Expirado</option>
              <option value="payment_review" disabled>Revisão de pagamento</option>
            </select>
          </div>
          <div class="order-detail-section"><h3>Itens do pedido</h3><div id="orderItemsList"></div></div>
          <div class="order-detail-section">
            <h3>Resumo financeiro</h3>
            <div class="order-detail-row"><span class="order-detail-label">Subtotal</span><span id="detailSubtotal">—</span></div>
            <div class="order-detail-row"><span class="order-detail-label">Desconto</span><span id="detailDiscount">—</span></div>
            <div class="order-detail-row"><span class="order-detail-label">Frete</span><span id="detailShipping">—</span></div>
            <div class="order-detail-row" style="font-weight:500"><span class="order-detail-label">Total</span><span id="detailTotal">—</span></div>
            <div class="order-detail-row"><span class="order-detail-label">Pagamento</span><span id="detailPayment">—</span></div>
            <div class="order-detail-row"><span class="order-detail-label">Data</span><span id="detailCreated">—</span></div>
          </div>
          <div class="order-detail-section">
            <h3>Cliente</h3>
            <div class="order-detail-row"><span class="order-detail-label">Nome</span><span id="detailClientName">—</span></div>
            <div class="order-detail-row"><span class="order-detail-label">E-mail</span><span id="detailClientEmail">—</span></div>
            <div class="order-detail-row"><span class="order-detail-label">Telefone</span><span id="detailClientPhone">—</span></div>
          </div>
          <div class="order-detail-section">
            <h3>Endereço de entrega</h3>
            <div class="order-detail-row"><span class="order-detail-label">Endereço</span><span id="detailAddress">—</span></div>
            <div class="order-detail-row"><span class="order-detail-label">Bairro</span><span id="detailDistrict">—</span></div>
            <div class="order-detail-row"><span class="order-detail-label">Cidade/UF</span><span id="detailCity">—</span></div>
            <div class="order-detail-row"><span class="order-detail-label">CEP</span><span id="detailCep">—</span></div>
          </div>
        </form>

      </div>
      <div class="modal-footer">
        <button class="btn btn-outline" onclick="closeDetail()">
          Fechar
        </button>
        <div style="display: flex; gap: 8px">
          <button class="btn btn-primary" id="btnSaveOrderStatus" type="button">Salvar status</button>
        </div>
      </div>
    </div>
  </div>
  <script type="module" src="frontend/js/modules/order/listaAdmin.js?v=4"></script>
  <script type="module" src="frontend/js/modules/order/formAdmin.js?v=3"></script>
</body>

</html>