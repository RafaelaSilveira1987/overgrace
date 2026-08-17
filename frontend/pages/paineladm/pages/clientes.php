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
  <link rel="stylesheet" href="/overgrace/frontend/pages/paineladm/paineladm.css?v=22" />
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
            <button class="btn btn-outline" id="btnExportClients" type="button">Exportar CSV</button>
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
            <option value="">Todos os perfis</option>
            <option value="VIP">VIP</option>
            <option value="Novo">Novo</option>
            <option value="Regular">Regular</option>
          </select>
          <select class="filter-select" id="filter-order">
            <option value="created_at:DESC">Mais recentes</option>
            <option value="pedidos:DESC">Mais pedidos</option>
            <option value="valor_gasto:DESC">Maior gasto</option>
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
            <span>Carregando...</span>
            <div class="pagination"></div>
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
          <div class="client-detail-section">
            <div style="display:flex;align-items:center;gap:14px;padding:8px 0 16px">
              <div class="client-avatar-lg" id="clientAvatarLg">—</div>
              <div><div style="font-size:16px;font-weight:500" id="clientNameLg">—</div><div style="font-size:12px;color:var(--ink-3)" id="clientEmailLg">—</div><div style="margin-top:6px"><span class="customer-tag" id="clientTagLg">—</span></div></div>
            </div>
          </div>
          <div class="client-detail-section"><h3>Dados cadastrais</h3>
            <div class="client-edit-grid">
              <label>Nome<input id="client-nome" data-editable disabled></label>
              <label>Sobrenome<input id="client-sobrenome" data-editable disabled></label>
              <label>E-mail<input id="client-email" type="email" data-editable disabled></label>
              <label>Telefone<input id="client-telefone" data-editable disabled></label>
              <label>CPF<input id="client-cpf" data-editable disabled></label>
            </div>
            <div class="client-detail-row"><span class="client-detail-label">Cadastro</span><span id="clientCadastro">—</span></div>
          </div>
          <div class="client-detail-section"><h3>Histórico de compras</h3>
            <div class="client-detail-row"><span class="client-detail-label">Total de pedidos</span><span id="clientPedidos">0</span></div>
            <div class="client-detail-row"><span class="client-detail-label">Total gasto</span><span id="clientTotal">R$ 0,00</span></div>
            <div class="client-detail-row"><span class="client-detail-label">Ticket médio</span><span id="clientTicket">R$ 0,00</span></div>
          </div>
          <div class="client-detail-section"><h3>Endereço</h3>
            <div class="client-detail-row"><span class="client-detail-label">Endereço</span><span id="clientAddress">—</span></div>
            <div class="client-detail-row"><span class="client-detail-label">Cidade/UF</span><span id="clientCity">—</span></div>
            <div class="client-detail-row"><span class="client-detail-label">CEP</span><span id="clientCep">—</span></div>
          </div>
          <div class="client-detail-section"><h3>Últimos pedidos</h3><div id="clientOrdersList"></div></div>
        </form>
      </div>
      <div class="modal-footer">
        <button class="btn btn-outline" onclick="closeClient()">
          Fechar
        </button>
        <button class="btn btn-primary" id="btnEditClient" type="button">Editar cliente</button>
      </div>
    </div>
  </div>
  <script type="module" src="frontend/js/modules/client/listaAdmin.js?v=2"></script>
  <script type="module" src="frontend/js/modules/client/formAdmin.js?v=2"></script>
</body>

</html>