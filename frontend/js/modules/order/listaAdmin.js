import { orderService } from "../../services/orderService.js";
import { debounce } from "../../utils/debounce.js";
import { dataUtil, valorUtil } from "../../utils/normalize.js";

let currentPage = 1;
const limit = 10;

export async function carregarPedidos() {
  try {
    const descricao = document.getElementById("filter-descricao").value;
    const status = document.getElementById("filter-status").value;
    const order = document.getElementById("filter-order").value;
    const [order_by, order_dir] = order.split(":");

    const res = await orderService.listar({
      descricao: descricao,
      status: status,
      order_by: order_by,
      order_dir: order_dir,
      page: currentPage,
      limit: limit,
    });

    const container = document.getElementById("lista-pedidos");
    container.innerHTML = "";
    let html = "";

    res.data.forEach((order) => {

      const itemsHtml = order.items
        .map(item => `
            <div>
                ${item.product_name} × ${item.quantity}
            </div>
        `)
        .join('');


      html += `

              <tr>
                <td class="order-num">#${order.id}</td>
                <td class="order-date">${dataUtil(order.created_at, 'format', 'd/m/Y')}</td>
                <td>${order.client_name}</td>
                <td class="order-items">
                  ${itemsHtml}
                </td>
                <td class="order-total">R$ ${order.subtotal}</td>
                <td style="font-size: 12px; color: var(--ink-3)">Pix</td>
                <td><span class="status-pill status-enviado">${order.status}</span></td>
                <td>
                  <button class="detail-btn" onclick="openDetail('10094')">
                    Detalhes
                  </button>
                </td>
              </tr>
            `;
    });

    container.innerHTML = html;

    function getBadgeClass(badge) {
      const mapa = {
        "Em alta": "badge-em-alta",
        Novo: "badge-novo",
        "Edição Limitada": "badge-limitada",
        Promoção: "badge-promo",
        Esgotando: "badge-esgotando",
      };
      return mapa[badge] || "badge-default";
    }

    renderPagination(res.pagination);

    document.querySelector(".table-footer span").innerText =
      `Mostrando ${res.data.length} de ${res.pagination.total} produtos`;

    if (!res.data || res.data.length === 0) {
      container.innerHTML = `
        <tr>
          <td colspan="10" style="text-align:center; padding: 30px; color: #999;">
            Nenhum produto encontrado
          </td>
        </tr>
      `;
      return;
    }

    //totais
    document.getElementById("qt_orders").innerText =
      res.totals.qt_products || 0;
    document.getElementById("qt_pend").innerText = res.totals.active || 0;
    document.getElementById("qt_env").innerText = res.totals.inactive || 0;
    document.getElementById("qt_canc").innerText = res.totals.min_price || 0;
    parseFloat(res.totals.med_price).toFixed(2) || 0;
  } catch (e) {
    console.error(e);

    const container = document.getElementById("lista-pedidos");

    container.innerHTML = `
    <tr>
      <td colspan="10" style="text-align:center; padding: 30px; color: #999;">
        Nenhum produto encontrado
      </td>
    </tr>
  `;
  }
}

function renderPagination(pagination) {
  const wrapper = document.querySelector(".pagination");

  const { page, pages } = pagination;

  let html = "";

  // botão anterior
  if (page > 1) {
    html += `<button class="page-btn" data-page="${page - 1}">←</button>`;
  }

  for (let i = 1; i <= pages; i++) {
    html += `
      <button class="page-btn ${i === page ? "active" : ""}" data-page="${i}">
        ${i}
      </button>
    `;
  }

  // botão próximo
  if (page < pages) {
    html += `<button class="page-btn" data-page="${page + 1}">→</button>`;
  }

  wrapper.innerHTML = html;
}

const carregarComDebounce = debounce(() => {
  currentPage = 1;
  carregarPedidos();
}, 500);

const descricao = document.getElementById("filter-descricao").addEventListener("input", carregarComDebounce);
const status = document.getElementById("filter-status").addEventListener("change", carregarComDebounce);
const order = document.getElementById("filter-order").addEventListener("change", carregarComDebounce);

document.addEventListener("click", (e) => {
  const btn = e.target.closest(".btn-deletar");

  if (btn) {
    const id = btn.dataset.id;
    removerProduto(id);
  }
});

document.addEventListener("click", (e) => {
  if (e.target.classList.contains("add-carrinho")) {
    const id = e.target.dataset.id;
    adicionarAoCarrinho(id);
  }
});

document.addEventListener("click", (e) => {
  if (e.target.classList.contains("page-btn")) {
    const page = parseInt(e.target.dataset.page);

    if (!isNaN(page)) {
      currentPage = page;
      carregarPedidos();
    }
  }
});

carregarPedidos();
