import { publicProdutoService } from '../../services/publicProdutoService.js';
import { carrinhoService } from '../../services/cartService.js';
import { contaItensCarrinho } from '../cart/qtyCart.js';
import { notify } from '../../utils/notify.js';

const grid = document.getElementById('featureGrid');

function money(value) {
  return Number(value || 0).toLocaleString('pt-BR', {
    style: 'currency',
    currency: 'BRL',
  });
}

function appPath(path) {
  const base = window.APP_BASE_PATH || '';
  return `${base}${path}`;
}

function availableSizes(product) {
  return (product.tamanhos || []).filter((size) => Number(size.estoque || 0) > 0);
}

function renderProduct(product) {
  const sizes = availableSizes(product);
  const soldOut = sizes.length === 0;
  const singleSize = sizes.length === 1;
  const image = product.imagem_principal
    ? appPath(`/frontend/uploads/products/${product.imagem_principal}`)
    : appPath('/frontend/uploads/placeholders/sem-item.png');

  const variants = sizes.map((s) => s.tamanho).join(' · ');
  const actionText = soldOut
    ? 'Esgotado'
    : singleSize
      ? '+ Adicionar ao carrinho'
      : 'Escolher tamanho';

  return `
    <article class="product-card" data-product-id="${product.id}" data-product-uuid="${product.uuid}">
      <a href="${appPath(`/produto?id=${encodeURIComponent(product.uuid)}`)}" class="product-img-wrap">
        <img src="${image}" alt="${product.descricao || 'Produto'}" />
        ${product.badge ? `<span class="product-badge">${product.badge}</span>` : ''}
      </a>

      <div class="product-info">
        <p class="product-name">${product.descricao || ''}</p>
        <p class="product-variant">${variants || 'Tamanho único'}</p>
        <p class="product-price">
          ${Number(product.preco_antigo || 0) > 0 ? `<span class="old">${money(product.preco_antigo)}</span>` : ''}
          <span class="${Number(product.preco_antigo || 0) > 0 ? 'sale' : ''}">${money(product.preco_atual)}</span>
        </p>

        <button
          type="button"
          class="product-quick-add"
          data-action="quick-add"
          ${soldOut ? 'disabled' : ''}
        >${actionText}</button>
      </div>
    </article>
  `;
}

async function loadFeatured() {
  if (!grid) return;

  try {
    const response = await publicProdutoService.listar({
      ativo: 1,
      limit: 4,
      order_by: 'posicao',
      order_dir: 'ASC',
    });

    const products = (response?.data || []).slice(0, 4);

    if (!products.length) {
      grid.innerHTML = '<p>Nenhum produto disponível no momento.</p>';
      return;
    }

    grid.innerHTML = products.map(renderProduct).join('');
    grid._products = new Map(products.map((product) => [String(product.id), product]));
  } catch (error) {
    console.error('[HOME FEATURED]', error);
    grid.innerHTML = '<p>Não foi possível carregar os destaques.</p>';
  }
}

grid?.addEventListener('click', async (event) => {
  const button = event.target.closest('[data-action="quick-add"]');
  if (!button || button.disabled) return;

  const card = button.closest('[data-product-id]');
  const product = grid._products?.get(card?.dataset.productId);
  if (!product) return;

  const sizes = availableSizes(product);

  if (sizes.length !== 1) {
    window.location.href = appPath(`/produto?id=${encodeURIComponent(product.uuid)}`);
    return;
  }

  try {
    button.disabled = true;
    const original = button.textContent;
    button.textContent = 'Adicionando...';

    await carrinhoService.adicionar(product.id, sizes[0].tamanho, 1);
    await contaItensCarrinho();
    notify.success('Produto adicionado ao carrinho!');

    button.textContent = 'Adicionado ✓';
    setTimeout(() => {
      button.disabled = false;
      button.textContent = original;
    }, 1200);
  } catch (error) {
    console.error('[QUICK ADD]', error);
    button.disabled = false;
    button.textContent = '+ Adicionar ao carrinho';
    notify.error(error?.message || 'Não foi possível adicionar ao carrinho');
  }
});

loadFeatured();
