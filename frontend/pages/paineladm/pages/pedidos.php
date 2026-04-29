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
            <div class="kpi-value">94</div>
          </div>
          <div class="kpi">
            <div class="kpi-label">Pendentes</div>
            <div class="kpi-value" style="color: var(--amber)">7</div>
          </div>
          <div class="kpi">
            <div class="kpi-label">Enviados</div>
            <div class="kpi-value" style="color: var(--blue)">38</div>
          </div>
          <div class="kpi">
            <div class="kpi-label">Cancelados</div>
            <div class="kpi-value" style="color: var(--red)">3</div>
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
            <input type="text" placeholder="Buscar pedido, cliente…" />
          </div>
          <select class="filter-select">
            <option>Todos os status</option>
            <option>Pago</option>
            <option>Pendente</option>
            <option>Enviado</option>
            <option>Cancelado</option>
          </select>
          <select class="filter-select">
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
            <tbody>
              <tr>
                <td class="order-num">#10094</td>
                <td class="order-date">24/04 14:32</td>
                <td>Ana Beatriz Souza</td>
                <td class="order-items">Kit Camisa + Boné × 1</td>
                <td class="order-total">R$ 259,00</td>
                <td style="font-size: 12px; color: var(--ink-3)">Pix</td>
                <td><span class="status-pill status-enviado">Enviado</span></td>
                <td>
                  <button class="detail-btn" onclick="openDetail('10094')">
                    Detalhes
                  </button>
                </td>
              </tr>
              <tr>
                <td class="order-num">#10093</td>
                <td class="order-date">24/04 11:18</td>
                <td>Carlos Henrique M.</td>
                <td class="order-items">Camisa Linho Off-White × 1</td>
                <td class="order-total">R$ 189,00</td>
                <td style="font-size: 12px; color: var(--ink-3)">Cartão</td>
                <td><span class="status-pill status-pago">Pago</span></td>
                <td>
                  <button class="detail-btn" onclick="openDetail('10093')">
                    Detalhes
                  </button>
                </td>
              </tr>
              <tr>
                <td class="order-num">#10092</td>
                <td class="order-date">24/04 09:45</td>
                <td>Fernanda Oliveira</td>
                <td class="order-items">Camisa Linho × 1 · Boné Bege × 1</td>
                <td class="order-total">R$ 318,00</td>
                <td style="font-size: 12px; color: var(--ink-3)">Boleto</td>
                <td><span class="status-pill status-pendente">Pendente</span></td>
                <td>
                  <button class="detail-btn" onclick="openDetail('10092')">
                    Detalhes
                  </button>
                </td>
              </tr>
              <tr>
                <td class="order-num">#10091</td>
                <td class="order-date">23/04 20:01</td>
                <td>Rafael Teixeira</td>
                <td class="order-items">Boné Aba Curva × 1</td>
                <td class="order-total">R$ 99,00</td>
                <td style="font-size: 12px; color: var(--ink-3)">Pix</td>
                <td><span class="status-pill status-pago">Pago</span></td>
                <td>
                  <button class="detail-btn" onclick="openDetail('10091')">
                    Detalhes
                  </button>
                </td>
              </tr>
              <tr>
                <td class="order-num">#10090</td>
                <td class="order-date">23/04 17:55</td>
                <td>Juliana Ferreira</td>
                <td class="order-items">Camisa Oversized Cáqui × 1</td>
                <td class="order-total">R$ 175,00</td>
                <td style="font-size: 12px; color: var(--ink-3)">Cartão</td>
                <td>
                  <span class="status-pill status-cancelado">Cancelado</span>
                </td>
                <td>
                  <button class="detail-btn" onclick="openDetail('10090')">
                    Detalhes
                  </button>
                </td>
              </tr>
              <tr>
                <td class="order-num">#10089</td>
                <td class="order-date">23/04 14:22</td>
                <td>Bruno Alves</td>
                <td class="order-items">Kit Camisa + Boné × 2</td>
                <td class="order-total">R$ 518,00</td>
                <td style="font-size: 12px; color: var(--ink-3)">Cartão 6×</td>
                <td><span class="status-pill status-enviado">Enviado</span></td>
                <td>
                  <button class="detail-btn" onclick="openDetail('10089')">
                    Detalhes
                  </button>
                </td>
              </tr>
              <tr>
                <td class="order-num">#10088</td>
                <td class="order-date">23/04 10:08</td>
                <td>Larissa Campos</td>
                <td class="order-items">Boné Trucker Branco × 1</td>
                <td class="order-total">R$ 89,00</td>
                <td style="font-size: 12px; color: var(--ink-3)">Pix</td>
                <td><span class="status-pill status-pago">Pago</span></td>
                <td>
                  <button class="detail-btn" onclick="openDetail('10088')">
                    Detalhes
                  </button>
                </td>
              </tr>
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

  <script>
    const orders = {
      10094: {
        client: "Ana Beatriz Souza",
        status: "enviado"
      },
      10093: {
        client: "Carlos Henrique M.",
        status: "pago"
      },
      10092: {
        client: "Fernanda Oliveira",
        status: "pendente"
      },
      10091: {
        client: "Rafael Teixeira",
        status: "pago"
      },
      10090: {
        client: "Juliana Ferreira",
        status: "cancelado"
      },
      10089: {
        client: "Bruno Alves",
        status: "enviado"
      },
      10088: {
        client: "Larissa Campos",
        status: "pago"
      },
    };

    function openDetail(num) {
      const o = orders[num];
      document.getElementById("modalOrderNum").textContent = "Pedido #" + num;
      document.getElementById("detailClientName").textContent = o.client;
      document.getElementById("orderStatusSelect").value = o.status;
      document.getElementById("orderModal").classList.add("open");
    }

    function closeDetail() {
      document.getElementById("orderModal").classList.remove("open");
    }

    function closeDetailOutside(e) {
      if (e.target === e.currentTarget) closeDetail();
    }
  </script>
</body>

</html>