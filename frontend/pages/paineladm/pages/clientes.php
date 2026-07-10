<!doctype html>
<html lang="pt-BR">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1.0" />
  <title>Clientes</title>
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link
    href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;1,9..40,300&family=DM+Mono:wght@400;500&display=swap"
    rel="stylesheet" />
  <link rel="stylesheet" href="/overgrace/frontend/pages/paineladm/paineladm.css" />
  <link rel="stylesheet" href="/overgrace/frontend/pages/paineladm/pages/pages-css/clientes.css">

</head>

<body>
  <script>
    window.parent.postMessage({
      type: "page",
      name: "clientes"
    }, "*");
  </script>

  <div class="shell">

    <?php include 'frontend/pages/paineladm/sidebar.php' ?>

    <div class="main">

      <?php include 'frontend/pages/paineladm/navbar.php' ?>

      <div class="page-content">
        <div class="page-header">
          <div class="page-header-left">
            <h1>Clientes</h1>
            <p><span id="qt_clients"></span> clientes cadastrados</p>
          </div>
          <div class="page-header-actions">
            <button class="btn btn-outline">Exportar lista</button>
          </div>
        </div>

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
            <input type="text" id="filter-descricao" placeholder="Nome, e-mail, CPF…" />
          </div>
          <select class="filter-select" id="filter-perfil">
            <option>Todos os perfis</option>
            <option>VIP</option>
            <option>Novo</option>
            <option>Regular</option>
          </select>
          <select class="filter-select" id="filter-order">
            <option>Ordenar: mais recente</option>
            <option>Mais pedidos</option>
            <option>Maior gasto</option>
          </select>
        </div>

        <div class="table-wrap">
          <table>
            <thead>
              <tr>
                <th>Cliente</th>
                <th>Cadastro</th>
                <th>Pedidos</th>
                <th>Total gasto</th>
                <th>Último pedido</th>
                <th>Perfil</th>
                <th style="width: 80px">Ação</th>
              </tr>
            </thead>
            <tbody id="lista-clientes">

            </tbody>
          </table>
          <div class="table-footer">
            <span>Mostrando 5 de 214 clientes</span>
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

  <!-- ── MODAL PERFIL DO CLIENTE ───────────────────────── -->
  <div
    class="client-modal"
    id="clientModal"
    onclick="closeClientOutside(event)">
    <div class="client-panel">
      <div class="modal-header">
        <span class="modal-title">Perfil do cliente</span>
        <button class="modal-close" onclick="closeClient()">×</button>
      </div>
      <div class="modal-body" style="overflow-y: auto; flex: 1">

        <form id="formClient">
          <!-- Cabeçalho do perfil -->
          <div class="client-detail-section">
            <div
              style="
                display: flex;
                align-items: center;
                gap: 14px;
                padding: 8px 0 16px;
              ">
              <div class="client-avatar-lg" id="clientAvatarLg">AB</div>
              <div>
                <div
                  style="font-size: 16px; font-weight: 500"
                  id="clientNameLg">
                  Ana Beatriz Souza
                </div>
                <div
                  style="font-size: 12px; color: var(--ink-3)"
                  id="clientEmailLg">
                  ana.beatriz@gmail.com
                </div>
                <div style="margin-top: 6px">
                  <span class="customer-tag tag-vip" id="clientTagLg">VIP</span>
                </div>
              </div>
            </div>
          </div>

          <div class="client-detail-section">
            <h3>Dados cadastrais</h3>
            <div class="client-detail-row">
              <span class="client-detail-label">Cadastro</span><span id="clientCadastro">Jan 2024</span>
            </div>
            <div class="client-detail-row">
              <span class="client-detail-label">Telefone</span><span>(11) 97777-0000</span>
            </div>
            <div class="client-detail-row">
              <span class="client-detail-label">CPF</span><span style="font-family: var(--mono); font-size: 12px">***.456.789-**</span>
            </div>
          </div>

          <div class="client-detail-section">
            <h3>Histórico de compras</h3>
            <div class="client-detail-row">
              <span class="client-detail-label">Total de pedidos</span><span id="clientPedidos" style="font-weight: 500">12</span>
            </div>
            <div class="client-detail-row">
              <span class="client-detail-label">Total gasto</span><span id="clientTotal" style="font-weight: 500">R$ 2.184</span>
            </div>
            <div class="client-detail-row">
              <span class="client-detail-label">Ticket médio</span><span>R$ 182</span>
            </div>
          </div>

          <div class="client-detail-section">
            <h3>Últimos pedidos</h3>
            <div class="order-mini-row">
              <span class="order-num">#10094</span>
              <span style="flex: 1; font-size: 12px; color: var(--ink-3)">24/04/2025</span>
              <span class="status-pill status-enviado">Enviado</span>
              <span style="font-weight: 500">R$ 259</span>
            </div>
            <div class="order-mini-row">
              <span class="order-num">#10071</span>
              <span style="flex: 1; font-size: 12px; color: var(--ink-3)">12/03/2025</span>
              <span class="status-pill status-pago">Pago</span>
              <span style="font-weight: 500">R$ 189</span>
            </div>
            <div class="order-mini-row">
              <span class="order-num">#10043</span>
              <span style="flex: 1; font-size: 12px; color: var(--ink-3)">05/02/2025</span>
              <span class="status-pill status-pago">Pago</span>
              <span style="font-weight: 500">R$ 119</span>
            </div>
          </div>

        </form>
      </div>
      <div class="modal-footer">
        <button class="btn btn-outline" onclick="closeClient()">
          Fechar
        </button>
        <button class="btn btn-primary">Editar cliente</button>
      </div>
    </div>
  </div>
  <script type="module" src="frontend/js/modules/client/listaAdmin.js"></script>
  <script type="module" src="frontend/js/modules/client/formAdmin.js"></script>
</body>

</html>