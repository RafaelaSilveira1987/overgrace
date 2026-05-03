import { couponService } from '../../services/couponService.js';
import { debounce } from '../../utils/debounce.js';
import { removerCupom } from './form.js';
import { dataUtil, valorUtil } from '../../utils/normalize.js';
 
let currentPage = 1;
const limit = 10;

function formatar(valor) {
    if (!valor) return '';

    const num = Number(valor);
    if (isNaN(num)) return '';

    return num.toLocaleString('pt-BR', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    });
}

export async function carregarCupons() {

  try {

    const status = document.getElementById("filter-status").value;
    const cupom = document.getElementById("filter-descricao").value;

    const res = await couponService.listar({
      cupom,
      status,
      page: currentPage,
      limit
    });


    let STATUS = {
      'ativo': 'tag-active',
      'pausado': 'tag-paused',
      'expirado': 'tag-expired',
    }

    const container = document.getElementById('lista-cupons');
    let html = '';

    if (!res.data || res.data.length === 0) {
      container.innerHTML = `
        <tr>
          <td colspan="8" style="text-align:center;padding:30px;color:#999;">
            Nenhum cupom encontrado
          </td>
        </tr>
      `;
      return;
    }
 
    res.data.forEach(c => {

      html += `
        <tr>
          <td><span class="coupon-code">${c.cupom}</span></td>
          <td>${c.tipo}</td>
          <td>${formatar(c.valor) ?? '-'}</td>
          <td>${formatar(c.minimo) ?? '-'}</td>
          <td>${dataUtil(c.validade, 'format', 'd/m/Y')}</td>
          <td><span class="coupon-tag ${STATUS[c.status]}">${c.status}</span></td>
          <td><button class="detail-btn" onclick="openCoupon('edit')">Editar</button></td>
        </tr>
      `;
    });

    container.innerHTML = html;

    renderPagination(res.pagination);

  } catch (e) {
    console.error(e);
  }
}

function renderPagination(pagination) {
  const wrapper = document.querySelector('.pagination');

  const { page, pages } = pagination;

  let html = '';

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

  if (page < pages) {
    html += `<button class="page-btn" data-page="${page + 1}">→</button>`;
  }

  wrapper.innerHTML = html;
}


// 🔎 filtros
const inputCupom = document.getElementById("filter-descricao");
const inputStatus = document.getElementById("filter-status");

const carregarComDebounce = debounce(() => {
  currentPage = 1;
  carregarCupons();
}, 500);

inputCupom.addEventListener('input', carregarComDebounce);
inputStatus.addEventListener('change', carregarComDebounce);


// 🔥 ações
document.addEventListener('click', (e) => {

  if (e.target.closest('.btn-deletar')) {
    const id = e.target.closest('.btn-deletar').dataset.id;
    removerCupom(id);
  }

  if (e.target.closest('.btn-editar')) {
    const id = e.target.closest('.btn-editar').dataset.id;
    editarCupom(id);
  }

  if (e.target.classList.contains('page-btn')) {
    const page = parseInt(e.target.dataset.page);

    if (!isNaN(page)) {
      currentPage = page;
      carregarCupons();
    }
  }

});

carregarCupons();
