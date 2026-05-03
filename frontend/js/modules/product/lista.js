import { produtoService } from '../../services/productService.js';
import { debounce } from '../../utils/debounce.js';
import { dataUtil, valorUtil } from '../../utils/normalize.js';
import { removerProduto } from '../../modules/product/form.js';

let currentPage = 1;
const limit = 10;


export async function carregarProdutos() {
  try {
    const descricao = document.getElementById("filter-descricao").value;
    const ativo = document.getElementById("filter-ativo").value;
    const categoria = document.getElementById("filter-categoria").value;
    const order = document.getElementById('filter-order').value;
    const [order_by, order_dir] = order.split(':');

    const res = await produtoService.listar({
      descricao: descricao,
      ativo: ativo,
      categoria: categoria,
      order_by: order_by,
      order_dir: order_dir,
      page: currentPage,
      limit: limit
    });

    const container = document.getElementById('lista-produtos');
    container.innerHTML = '';
    let html = '';

    res.data.forEach(produto => {

      let totalEstoque = 0;

      const tamanhosHtml = produto.tamanhos?.length
        ? produto.tamanhos
          .map(t => {
            totalEstoque += Number(t.estoque) || 0;
            return `<span class="size-tag">${t.tamanho}</span>`;
          })
          .join('')
        : `<span style="font-size:12px;color:#999">—</span>`;


      const BASE_IMG = '/overgrace/frontend/uploads/products/';

      const imgSrc = produto.imagem_principal
        ? BASE_IMG + produto.imagem_principal
        : '';


      html += `
                <tr>
                <td>
                  <input type="checkbox" style="accent-color: var(--ink)" />
                </td>
                <td>
                  <div class="prod-name-cell">
                  <div class="prod-img-thumb-wrap">
                      <img 
                        class="prod-img-thumb"
                        src="${imgSrc}"
                        onerror="this.onerror=null; this.src='/overgrace/frontend/uploads/placeholders/sem-item.png';"
                      />
                  </div>

                    <div>
                      <div class="prod-name-text">${produto.descricao}</div>
                      <div class="prod-id">${produto.desc_slug}</div>
                    </div>
                  </div>
                </td>
                <td>${produto.categoria}</td>
                <td><span class="badge badge-novo">${produto.badge || ''}</span></td>
                <td>
                  <div class="sizes-cell">
                    ${tamanhosHtml}
                  </div>
                </td>
                <td class="price-cell">R$ ${produto.preco_atual}</td>
                <td style="font-size: 12px; color: var(--ink-3)">${dataUtil(produto.inicio_exibicao, 'format', 'd/m/Y')}</td>
                <td style="font-size: 13px">${totalEstoque}</td> 
                <td><span class="ativo-dot ${produto.ativo == "1" ? 'ativo-sim' : 'ativo-nao'}"></span>${produto.ativo == "1" ? 'Ativo' : 'Inativo'}</td>
                <td class="actions-cell">
                  <button class="row-btn btn-editar" data-id="${produto.id}">
                    <svg fill="none" viewBox="0 0 12 12" stroke="currentColor" stroke-width="1.5">
                      <path d="M1 9l2-1 6-6-1-1-6 6-1 2z" />
                    </svg>Editar
                  </button>
                  <button class="row-btn danger btn-deletar" data-id="${produto.id}">
                    <svg fill="none" viewBox="0 0 12 12" stroke="currentColor" stroke-width="1.5">
                      <path d="M2 4h8l-.8 6H2.8L2 4z" />
                      <path d="M4.5 2h3M1 4h10" />
                    </svg>
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
    document.getElementById('active').innerText = res.totals.active || 0;
    document.getElementById('inactive').innerText = res.totals.inactive || 0;
    document.getElementById('min_price').innerText = res.totals.min_price || 0;
    document.getElementById('max_price').innerText = res.totals.max_price || 0;
    document.getElementById('med_price').innerText = parseFloat(res.totals.med_price).toFixed(2) || 0;



  } catch (e) {
    console.error(e);

    const container = document.getElementById('lista-produtos');

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
const inputAtivo = document.getElementById("filter-ativo");
const inputCategoria = document.getElementById("filter-categoria");
const order = document.getElementById('filter-order');
const carregarComDebounce = debounce(() => {
  currentPage = 1;
  carregarProdutos();
}, 500);

inputDescricao.addEventListener('input', carregarComDebounce);
inputAtivo.addEventListener('change', carregarComDebounce);
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
