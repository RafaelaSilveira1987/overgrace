import { orderService } from "../../services/orderService.js";

function money(value) {
  return Number(value || 0).toLocaleString("pt-BR", {
    style: "currency",
    currency: "BRL",
  });
}

function appBasePath() {
  const path = window.location.pathname;
  if (path.includes("/overgrace-main")) return "/overgrace-main";
  if (path.includes("/overgrace")) return "/overgrace";
  return "";
}

export async function carregarDash() {
  try {
    const res = await orderService.listarDash();
    const totals = res?.totals || {};
    const totalValue = Number(totals.total_value || 0);
    const totalOrders = Number(totals.total_orders || 0);
    const ticket = totalOrders > 0 ? totalValue / totalOrders : 0;

    const kpis = document.getElementById("kpis");
    if (kpis) {
      const totalEl = kpis.querySelector("#kpi-totals");
      const qtyEl = kpis.querySelector("#kpi-qty-totals");
      const ticketEl = kpis.querySelector("#kpi-ticket");
      if (totalEl) totalEl.textContent = money(totalValue);
      if (qtyEl) qtyEl.textContent = String(totalOrders);
      if (ticketEl) ticketEl.textContent = money(ticket);
    }

    const container = document.getElementById("pedidos-recentes");
    if (container) {
      const orders = Array.isArray(res?.data) ? res.data : [];
      container.innerHTML = orders.length
        ? orders.map((order) => `
          <div class="recent-order-row">
            <span class="recent-order-num">#${order.id}</span>
            <span class="recent-order-client">${order.client_name || "Cliente"}</span>
            <span class="status-pill">${order.status || "—"}</span>
            <span class="recent-order-val">${money(order.total_amount ?? order.subtotal)}</span>
          </div>
        `).join("")
        : '<div class="empty-state">Nenhum pedido no período.</div>';
    }

    const topProducts = document.getElementById("top-products");
    if (topProducts) {
      const products = Array.isArray(res?.items) ? res.items : [];
      const baseImg = `${appBasePath()}/frontend/uploads/products/`;
      const maxQty = Math.max(1, ...products.map((p) => Number(p.qty_sum || 0)));

      topProducts.innerHTML = products.length
        ? products.map((produto) => {
            const qty = Number(produto.qty_sum || 0);
            const width = Math.max(0, Math.min(100, (qty / maxQty) * 100));
            const imgSrc = produto.imagem_principal ? baseImg + produto.imagem_principal : "";
            return `
              <div class="top-product-row">
                ${imgSrc ? `<img class="top-product-img" src="${imgSrc}" alt="">` : '<div class="top-product-img"></div>'}
                <div class="top-product-info">
                  <div class="top-product-name">${produto.product_name || "Produto"}</div>
                  <div class="top-product-cat">${qty} vendas</div>
                  <div class="top-product-bar">
                    <div class="top-product-bar-fill" style="width: ${width}%"></div>
                  </div>
                </div>
                <div class="top-product-val">${money(produto.total_sum)}</div>
              </div>
            `;
          }).join("")
        : '<div class="empty-state">Sem vendas de produtos no período.</div>';
    }
  } catch (error) {
    console.error("[DASHBOARD]", error);
  }
}

carregarDash();
