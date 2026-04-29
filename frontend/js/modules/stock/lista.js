import { stockService } from '../../services/stockService.js';
import { debounce } from '../../utils/debounce.js';
import { dataUtil, valorUtil } from '../../utils/normalize.js';
import { reposicao } from '../../modules/stock/form.js';

let currentPage = 1;
const limit = 10;


export async function carregarProdutos() {
  try {
    const descricao = document.getElementById("filter-descricao").value;
    const categoria = document.getElementById("filter-categoria").value;
    const order = document.getElementById('filter-order').value;
    const [order_by, order_dir] = order.split(':');

    const res = await stockService.listar({
      descricao: descricao,
      categoria: categoria,
      order_by: order_by,
      order_dir: order_dir,
      page: currentPage,
      limit: limit
    });

    const container = document.getElementById('lista-estoque');
    container.innerHTML = '';
    let html = '';

    res.data.forEach(produto => {

      const BASE_IMG = '/overgrace/frontend/uploads/products/';

      const imgSrc = produto.imagem_principal
        ? BASE_IMG + produto.imagem_principal
        : '';

      let situacao = '';

      const estoque = Number(produto.estoque);
      const minimo = Number(produto.minimo);

      if (estoque <= 0) {
        situacao = 'Esgotado';
      } else if (estoque <= minimo) {
        situacao = 'Baixo';
      } else if (estoque > minimo) {
        situacao = 'OK';
      } else {
        situacao = 'Inconsistente';
      }

      let percentual = 0;

      if (minimo > 0) {
        percentual = Math.min((estoque / minimo) * 100, 100);
      }

      // cor da barra
      let barColor = '#28a745'; // verde

      if (estoque <= 0) {
        barColor = '#dc3545'; // vermelho
      } else if (estoque <= minimo) {
        barColor = '#ffc107'; // amarelo
      }

      const rowClass = situacao !== 'OK' ? `row-${situacao.toLowerCase()}` : '';


      html += `
              <tr class="${rowClass}">
              <td>
                <div style="display:flex;align-items:center;gap:10px">
                      <img 
                        class="prod-img-thumb"
                        src="${imgSrc}"
                        onerror="this.onerror=null; this.src='/overgrace/frontend/uploads/placeholders/sem-item.png';"
                      />
                  <div>
                    <div style="font-size:13px">${produto.descricao}</div>
                  </div>
                </div>
              </td>
              <td><span class="size-tag">${produto.tamanho}</span></td>
              <td style="font-size:13px">${produto.categoria}</td>
              <td style="font-family:var(--mono);font-size:11px;color:var(--ink-3)">${produto.minimo}</td>
              <td>
                <div class="stock-cell">
                  <span class="stock-num 15">${produto.estoque}</span>
                 <div class="stock-bar-wrap">
                  <div 
                    class="stock-bar-fill" 
                    style="width:${percentual}%; background:${barColor}"
                  ></div>
                </div>
                </div>
              </td>
              <td style="font-size:12px;color:var(--ink-3)">-</td>
              <td style="font-size:12px;color:var(--ink-3)">${situacao}</td>
              <td>
                <button class="detail-btn btn-repor" data-id="${produto.id}">
                  Repor
                </button>
              </td>
            </tr>
            `;
    });
 
    container.innerHTML = html;
    renderPagination(res.pagination);

    document.querySelector('.table-footer span').innerText =
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
    document.getElementById('qt_products').innerText = res.totals.qt_products || 0;
    document.getElementById('qt_stock').innerText = res.totals.qt_stock || 0;
    document.getElementById('qt_baixo').innerText = res.totals.qt_baixo || 0;
    document.getElementById('qt_esgotado').innerText = res.totals.qt_zerados || 0;

  } catch (e) {
    console.error(e);

    const container = document.getElementById('lista-estoque');

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
  const wrapper = document.querySelector('.pagination');

  const { page, pages } = pagination;

  let html = '';

  // botão anterior
  if (page > 1) {
    html += `<button class="page-btn" data-page="${page - 1}">←</button>`;
  }

  for (let i = 1; i <= pages; i++) {
    html += `
      <button class="page-btn ${i === page ? 'active' : ''}" data-page="${i}">
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

const inputDescricao = document.getElementById("filter-descricao");
const inputCategoria = document.getElementById("filter-categoria");
const order = document.getElementById('filter-order');
const carregarComDebounce = debounce(() => {
  currentPage = 1;
  carregarProdutos();
}, 500);

inputDescricao.addEventListener('input', carregarComDebounce);
inputCategoria.addEventListener('change', carregarComDebounce);
order.addEventListener('change', carregarComDebounce);

document.addEventListener('click', (e) => {
  const btn = e.target.closest('.btn-deletar');

  if (btn) {
    const id = btn.dataset.id;
    removerProduto(id);
  }
});

document.addEventListener('click', (e) => {
  if (e.target.classList.contains('add-carrinho')) {
    const id = e.target.dataset.id;
    adicionarAoCarrinho(id);
  }
});

document.addEventListener('click', (e) => {
  if (e.target.classList.contains('page-btn')) {
    const page = parseInt(e.target.dataset.page);

    if (!isNaN(page)) {
      currentPage = page;
      carregarProdutos();
    }
  }
});


carregarProdutos();
