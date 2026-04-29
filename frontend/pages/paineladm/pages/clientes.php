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
            <p>214 clientes cadastrados</p>
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
            <input type="text" placeholder="Nome, e-mail, CPF…" />
          </div>
          <select class="filter-select">
            <option>Todos os perfis</option>
            <option>VIP</option>
            <option>Novo</option>
            <option>Regular</option>
          </select>
          <select class="filter-select">
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
            <tbody>
              <tr>
                <td>
                  <div class="customer-cell">
                    <div class="customer-avatar">AB</div>
                    <div>
                      <div class="customer-name">Ana Beatriz Souza</div>
                      <div class="customer-email">ana.beatriz@gmail.com</div>
                    </div>
                  </div>
                </td>
                <td style="font-size: 12px; color: var(--ink-3)">Jan 2024</td>
                <td>12</td>
                <td style="font-weight: 500">R$ 2.184</td>
                <td style="font-size: 12px; color: var(--ink-3)">24/04/2025</td>
                <td><span class="customer-tag tag-vip">VIP</span></td>
                <td>
                  <button
                    class="detail-btn"
                    onclick="
                    openClient(
                      'AB',
                      'Ana Beatriz Souza',
                      'ana.beatriz@gmail.com',
                      'Jan 2024',
                      '12',
                      'R$ 2.184',
                      'VIP',
                    )
                  ">
                    Ver perfil
                  </button>
                </td>
              </tr>
              <tr>
                <td>
                  <div class="customer-cell">
                    <div class="customer-avatar">BM</div>
                    <div>
                      <div class="customer-name">Bruno Alves Monteiro</div>
                      <div class="customer-email">brunomonteiro@hotmail.com</div>
                    </div>
                  </div>
                </td>
                <td style="font-size: 12px; color: var(--ink-3)">Mar 2024</td>
                <td>8</td>
                <td style="font-weight: 500">R$ 1.560</td>
                <td style="font-size: 12px; color: var(--ink-3)">23/04/2025</td>
                <td><span class="customer-tag tag-vip">VIP</span></td>
                <td>
                  <button
                    class="detail-btn"
                    onclick="
                    openClient(
                      'BM',
                      'Bruno Alves Monteiro',
                      'brunomonteiro@hotmail.com',
                      'Mar 2024',
                      '8',
                      'R$ 1.560',
                      'VIP',
                    )
                  ">
                    Ver perfil
                  </button>
                </td>
              </tr>
              <tr>
                <td>
                  <div class="customer-cell">
                    <div class="customer-avatar">FO</div>
                    <div>
                      <div class="customer-name">Fernanda Oliveira</div>
                      <div class="customer-email">fe.oliveira@outlook.com</div>
                    </div>
                  </div>
                </td>
                <td style="font-size: 12px; color: var(--ink-3)">Abr 2025</td>
                <td>1</td>
                <td style="font-weight: 500">R$ 318</td>
                <td style="font-size: 12px; color: var(--ink-3)">24/04/2025</td>
                <td><span class="customer-tag tag-new">Novo</span></td>
                <td>
                  <button
                    class="detail-btn"
                    onclick="
                    openClient(
                      'FO',
                      'Fernanda Oliveira',
                      'fe.oliveira@outlook.com',
                      'Abr 2025',
                      '1',
                      'R$ 318',
                      'Novo',
                    )
                  ">
                    Ver perfil
                  </button>
                </td>
              </tr>
              <tr>
                <td>
                  <div class="customer-cell">
                    <div class="customer-avatar">RT</div>
                    <div>
                      <div class="customer-name">Rafael Teixeira</div>
                      <div class="customer-email">rafael.t@gmail.com</div>
                    </div>
                  </div>
                </td>
                <td style="font-size: 12px; color: var(--ink-3)">Jun 2024</td>
                <td>4</td>
                <td style="font-weight: 500">R$ 692</td>
                <td style="font-size: 12px; color: var(--ink-3)">23/04/2025</td>
                <td><span class="customer-tag tag-regular">Regular</span></td>
                <td>
                  <button
                    class="detail-btn"
                    onclick="
                    openClient(
                      'RT',
                      'Rafael Teixeira',
                      'rafael.t@gmail.com',
                      'Jun 2024',
                      '4',
                      'R$ 692',
                      'Regular',
                    )
                  ">
                    Ver perfil
                  </button>
                </td>
              </tr>
              <tr>
                <td>
                  <div class="customer-cell">
                    <div class="customer-avatar">LC</div>
                    <div>
                      <div class="customer-name">Larissa Campos</div>
                      <div class="customer-email">larissa.campos@usp.br</div>
                    </div>
                  </div>
                </td>
                <td style="font-size: 12px; color: var(--ink-3)">Abr 2025</td>
                <td>1</td>
                <td style="font-weight: 500">R$ 89</td>
                <td style="font-size: 12px; color: var(--ink-3)">23/04/2025</td>
                <td><span class="customer-tag tag-new">Novo</span></td>
                <td>
                  <button
                    class="detail-btn"
                    onclick="
                    openClient(
                      'LC',
                      'Larissa Campos',
                      'larissa.campos@usp.br',
                      'Abr 2025',
                      '1',
                      'R$ 89',
                      'Novo',
                    )
                  ">
                    Ver perfil
                  </button>
                </td>
              </tr>
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
      </div>
      <div class="modal-footer">
        <button class="btn btn-outline" onclick="closeClient()">
          Fechar
        </button>
        <button class="btn btn-primary">Editar cliente</button>
      </div>
    </div>
  </div>

  <script>
    function openClient(
      initials,
      name,
      email,
      cadastro,
      pedidos,
      total,
      tag,
    ) {
      document.getElementById("clientAvatarLg").textContent = initials;
      document.getElementById("clientNameLg").textContent = name;
      document.getElementById("clientEmailLg").textContent = email;
      document.getElementById("clientCadastro").textContent = cadastro;
      document.getElementById("clientPedidos").textContent = pedidos;
      document.getElementById("clientTotal").textContent = total;
      const tagEl = document.getElementById("clientTagLg");
      tagEl.textContent = tag;
      tagEl.className =
        "customer-tag " +
        (tag === "VIP" ?
          "tag-vip" :
          tag === "Novo" ?
          "tag-new" :
          "tag-regular");
      document.getElementById("clientModal").classList.add("open");
    }

    function closeClient() {
      document.getElementById("clientModal").classList.remove("open");
    }

    function closeClientOutside(e) {
      if (e.target === e.currentTarget) closeClient();
    }
  </script>
</body>

</html>