<!doctype html>
<html lang="pt-BR">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1.0" />
  <title>Cupons</title>

  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet" />

  <link rel="stylesheet" href="/overgrace/frontend/pages/paineladm/paineladm.css" />
  <link rel="stylesheet" href="/overgrace/frontend/pages/paineladm/pages/pages-css/cupons.css" />
  <link rel="stylesheet" href="/overgrace/frontend/css/utils.css">


</head>

<body>
  <script>
    window.parent.postMessage({
      type: "page",
      name: "cupons"
    }, "*");
  </script>


  <div class="shell">

    <?php include 'frontend/pages/paineladm/sidebar.php' ?>

    <div class="main">

      <?php include 'frontend/pages/paineladm/navbar.php' ?>

      <div class="page-content">
        <div class="page-header">
          <div class="page-header-left">
            <h1>Cupons</h1>
            <p>12 cupons cadastrados</p>
          </div>
          <div class="page-header-actions">
            <button class="btn btn-outline">Exportar lista</button>
            <button class="btn" onclick="openCoupon('new')">+ Novo cupom</button>
          </div>
        </div>

        <div class="toolbar">
          <div class="search-box">
            <svg width="14" height="14" fill="none" viewBox="0 0 14 14" stroke="currentColor" stroke-width="1.5">
              <circle cx="6" cy="6" r="4" />
              <path d="M10 10l2.5 2.5" />
            </svg>
            <input type="text" placeholder="Código do cupom…" />
          </div>

          <select class="filter-select">
            <option>Todos os status</option>
            <option>Ativo</option>
            <option>Pausado</option>
            <option>Expirado</option>
          </select>

          <select class="filter-select">
            <option>Ordenar: mais recente</option>
            <option>Maior desconto</option>
            <option>Mais usado</option>
          </select>
        </div>

        <div class="table-wrap">
          <table>
            <thead>
              <tr>
                <th>Código</th>
                <th>Tipo</th>
                <th>Valor</th>
                <th>Mínimo</th>
                <th>Usos</th>
                <th>Validade</th>
                <th>Status</th>
                <th style="width:80px;">Ação</th>
              </tr>
            </thead>

            <tbody>
              <tr>
                <td><span class="coupon-code">PRIMEIRA10</span></td>
                <td>Percentual</td>
                <td>10%</td>
                <td>R$100</td>
                <td>34 / 100</td>
                <td>31/12/2026</td>
                <td><span class="coupon-tag tag-active">Ativo</span></td>
                <td><button class="detail-btn" onclick="openCoupon('edit')">Editar</button></td>
              </tr>

              <tr>
                <td><span class="coupon-code">FRETEGRATIS</span></td>
                <td>Frete grátis</td>
                <td>—</td>
                <td>R$150</td>
                <td>12 / 50</td>
                <td>30/06/2026</td>
                <td><span class="coupon-tag tag-paused">Pausado</span></td>
                <td><button class="detail-btn" onclick="openCoupon('edit')">Editar</button></td>
              </tr>

              <tr>
                <td><span class="coupon-code">SALE20</span></td>
                <td>Fixo</td>
                <td>R$20</td>
                <td>R$120</td>
                <td>50 / 50</td>
                <td>01/05/2025</td>
                <td><span class="coupon-tag tag-expired">Expirado</span></td>
                <td><button class="detail-btn" onclick="openCoupon('edit')">Editar</button></td>
              </tr>
            </tbody>
          </table>

          <div class="table-footer">
            <span>Mostrando 3 de 12 cupons</span>
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

  <!-- MODAL CUPOM -->
  <div class="client-modal" id="couponModal" onclick="closeCouponOutside(event)">
    <div class="client-panel">
      <div class="modal-header">
        <span class="modal-title" id="couponModalTitle">Novo cupom</span>
        <button class="modal-close" onclick="closeCoupon()">×</button>
      </div>

      <div class="modal-body" style="overflow-y:auto; flex:1;">
        <div class="client-detail-section">
          <h3>Informações do cupom</h3>

          <div class="form-group">
            <label>Código</label>
            <input type="text" id="couponCode">
          </div>

          <div class="form-group">
            <label>Tipo</label>
            <select id="couponType">
              <option>Percentual (%)</option>
              <option>Valor fixo (R$)</option>
              <option>Frete grátis</option>
            </select>
          </div>

          <div class="form-group">
            <label>Valor</label>
            <input type="number" id="couponValue">
          </div>

          <div class="form-group">
            <label>Compra mínima</label>
            <input type="number" id="couponMin">
          </div>

          <div class="form-group">
            <label>Limite de uso</label>
            <input type="number" id="couponLimit">
          </div>

          <div class="form-group">
            <label>Validade</label>
            <input type="date" id="couponDate">
          </div>

          <div class="form-group">
            <label>Status</label>
            <select id="couponStatus">
              <option>Ativo</option>
              <option>Pausado</option>
              <option>Expirado</option>
            </select>
          </div>
        </div>
      </div>

      <div class="modal-footer">
        <button class="btn btn-outline" onclick="closeCoupon()">Cancelar</button>
        <button class="btn btn-primary">Salvar cupom</button>
      </div>
    </div>
  </div>

  <script>
    function openCoupon(mode = "new") {
      document.getElementById("couponModal").classList.add("open");

      if (mode === "new") {
        document.getElementById("couponModalTitle").textContent = "Novo cupom";
        clearCouponForm();
      } else {
        document.getElementById("couponModalTitle").textContent = "Editar cupom";
      }
    }

    function closeCoupon() {
      document.getElementById("couponModal").classList.remove("open");
    }

    function closeCouponOutside(e) {
      if (e.target === e.currentTarget) closeCoupon();
    }

    function clearCouponForm() {
      document.getElementById("couponCode").value = "";
      document.getElementById("couponValue").value = "";
      document.getElementById("couponMin").value = "";
      document.getElementById("couponLimit").value = "";
      document.getElementById("couponDate").value = "";
    }
  </script>

</body>

</html>