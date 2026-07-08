import { carrinhoService } from '../../services/cartService.js';
import { notify } from '../../utils/notify.js';

const BASE_IMG = '/overgrace/frontend/uploads/products/';
const PLACEHOLDER_IMG = '/overgrace/frontend/uploads/placeholders/sem-item.png';

window.checkoutCartState = {
  subtotal: 0,
  discount: 0,
  shipping: 0,
  total: 0,
  items: []
}; 

function formatBRL(value) {
  return Number(value || 0).toLocaleString('pt-BR', {
    style: 'currency',
    currency: 'BRL'
  });
}

function setText(id, value) {
  const el = document.getElementById(id);
  if (el) el.textContent = value;
}

function renderInstallments(total) {
  const select = document.getElementById('parcelas') || document.getElementById('form-checkout__installments');
  if (!select) return;

  const max = 6;
  select.innerHTML = '';

  for (let i = 1; i <= max; i++) {
    const option = document.createElement('option');
    option.value = String(i);
    option.textContent = `${i}x de ${formatBRL(total / i)} sem juros`;
    select.appendChild(option);
  }
}

window.updateCheckoutTotals = function updateCheckoutTotals(shipping = window.checkoutCartState.shipping, shippingName = '') {
  const subtotal = Number(window.checkoutCartState.subtotal || 0);
  const discount = Number(window.checkoutCartState.discount || 0);
  const shippingValue = Number(shipping || 0);
  const total = Math.max(subtotal - discount + shippingValue, 0);

  window.checkoutCartState.shipping = shippingValue;
  window.checkoutCartState.total = total;

  setText('total-items', formatBRL(subtotal));
  setText('total-descontos', discount > 0 ? `− ${formatBRL(discount)}` : formatBRL(0));
  setText('shipLabel', shippingValue > 0 ? formatBRL(shippingValue) : 'Grátis');
  setText('total-items-final', formatBRL(total));

  renderInstallments(total);
};

function getDemoCart() {
  return {
    total: 347.00,
    coupon: 20.00,
    sub_total: 327.00,
    items: [
      {
        descricao: 'Camisa Oversized Cáqui',
        size: 'M',
        quantity: 1,
        subtotal: 175.00,
        imagem: '2ad8bcd04644e51ecba9fe81a9dd49c7.jpg'
      },
      {
        descricao: 'Boné Aba Curva Preto',
        size: 'Único',
        quantity: 1,
        subtotal: 79.00,
        imagem: '004944ebb7bd7b8d85f3a419ec418b49.png'
      },
      {
        descricao: 'Cropped Basic Off',
        size: 'P',
        quantity: 1,
        subtotal: 93.00,
        imagem: '0204f163a1560c945ce48bab18a12847.jpeg'
      }
    ]
  };
}

function renderCart(cart) {
  const items = Array.isArray(cart.items) ? cart.items : [];
  const container = document.getElementById('order-items');

  window.checkoutCartState.items = items;
  window.checkoutCartState.subtotal = Number(cart.total || 0);
  window.checkoutCartState.discount = Number(cart.coupon || 0);
  window.checkoutCartState.total = Number(cart.sub_total || cart.total || 0);

  const qtd = items.reduce((acc, item) => acc + Number(item.quantity || 0), 0);
  setText('summaryItemsCount', `${qtd} ${qtd === 1 ? 'item' : 'itens'}`);

  if (!container) return;

  if (!items.length) {
    container.innerHTML = `
      <div class="summary-empty">
        Seu carrinho está vazio. Volte para a loja e escolha umas peças, senão esse checkout fica tristinho.
      </div>
    `;
    window.updateCheckoutTotals();
    return;
  }

  container.innerHTML = items.map((item) => {
    const image = item.imagem ? BASE_IMG + item.imagem : PLACEHOLDER_IMG;
    const name = item.descricao || 'Produto OverGrace';
    const size = item.size || 'Único';
    const quantity = Number(item.quantity || 1);
    const subtotal = Number(item.subtotal || 0);

    return `
      <div class="order-item">
        <div class="order-thumb-wrap">
          <img class="order-thumb" src="${image}" alt="${name}" onerror="this.src='${PLACEHOLDER_IMG}'" />
          <span class="order-qty-badge">${quantity}</span>
        </div>
        <div>
          <p class="order-item-name">${name}</p>
          <p class="order-item-variant">Tam · ${size}</p>
        </div>
        <p class="order-item-price">${formatBRL(subtotal)}</p>
      </div>
    `;
  }).join('');

  window.updateCheckoutTotals(window.checkoutCartState.shipping);
}

export async function carregarCarrinho() {
  try {
    const cart = await carrinhoService.get();
    renderCart(cart);
  } catch (error) {
    console.warn('Carrinho real não carregou. Exibindo carrinho demonstrativo:', error);
    renderCart(getDemoCart());
    notify.error(error?.message || 'Erro ao carregar carrinho real. Usei dados demonstrativos para prévia.');
  }
}

carregarCarrinho();
