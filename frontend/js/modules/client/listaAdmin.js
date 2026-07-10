import { clientService } from "../../services/clientService.js";
import { debounce } from "../../utils/debounce.js";
import { dataUtil, valorUtil } from "../../utils/normalize.js";
import { getInitials } from "../../utils/inicials.js";

let currentPage = 1;
const limit = 10;

export async function carregarClientes() {
  try {
    const descricao = document.getElementById("filter-descricao").value;
    const perfil = document.getElementById("filter-perfil").value;
    const order = document.getElementById("filter-order").value;
    const [order_by, order_dir] = order.split(":");

    const res = await clientService.listar({
      descricao: descricao,
      perfil: perfil,
      order_by: order_by,
      order_dir: order_dir,
      page: currentPage,
      limit: limit,
    });

    const container = document.getElementById("lista-clientes");
    container.innerHTML = "";
    let html = "";

    res.data.forEach((client) => {

      html += `

              <tr>
                <td>
                  <div class="customer-cell">
                    <div class="customer-avatar">${getInitials(client.nome)}</div>
                    <div>
                      <div class="customer-name">${client.nome}</div>
                      <div class="customer-email">${client.email}</div>
                    </div>
                  </div>
                </td>
                <td style="font-size: 12px; color: var(--ink-3)">Jan 2024</td>
                <td>${client.pedidos}</td>
                <td style="font-weight: 500">R$ ${client.valor_gasto}</td>
                <td style="font-size: 12px; color: var(--ink-3)">${dataUtil(client.ultimo_pedido, 'format', 'd/m/Y')}</td>
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
            `;
    });

    container.innerHTML = html;


    document.getElementById("qt_clients").innerHTML = res.totals.qt_clients;

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

  } catch (e) {
    console.error(e);

    const container = document.getElementById("lista-clientes");

    container.innerHTML = `
    <tr>
      <td colspan="10" style="text-align:center; padding: 30px; color: #999;">
        Nenhum cliente encontrado
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
  carregarClientes();
}, 500);

document.getElementById("filter-descricao").addEventListener("input", carregarComDebounce);
document.getElementById("filter-perfil").addEventListener("change", carregarComDebounce);
document.getElementById("filter-order").addEventListener("change", carregarComDebounce);

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
      carregarClientes();
    }
  }
});

carregarClientes();
