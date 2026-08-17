import { carrinhoService } from "../../services/cartService.js";
import { orderService } from "../../services/orderService.js";
import { notify } from "../../utils/notify.js";

const APP_BASE = window.location.pathname.includes("/overgrace-main")
  ? "/overgrace-main"
  : window.location.pathname.includes("/overgrace")
    ? "/overgrace"
    : "";
const BASE_IMG = `${APP_BASE}/frontend/uploads/products/`;
const PLACEHOLDER_IMG = `${APP_BASE}/frontend/uploads/placeholders/sem-item.png`;

window.checkoutCartState = {
  subtotal: 0,
  discount: 0,
  shipping: 0,
  shippingName: "",
  total: 0,
  items: [],
};

function formatBRL(value) {
  return Number(value || 0).toLocaleString("pt-BR", {
    style: "currency",
    currency: "BRL",
  });
}

function setText(id, value) {
  const el = document.getElementById(id);
  if (el) el.textContent = value;
}

function renderInstallments(total) {
  const select =
    document.getElementById("parcelas") ||
    document.getElementById("form-checkout__installments");
  if (!select) return;

  const max = 6;
  select.innerHTML = "";

  for (let i = 1; i <= max; i++) {
    const option = document.createElement("option");
    option.value = String(i);
    option.textContent = `${i}x de ${formatBRL(total / i)} sem juros`;
    select.appendChild(option);
  }
}

window.updateCheckoutTotals = function updateCheckoutTotals(
  shipping = window.checkoutCartState.shipping,
  shippingName = "",
) {
  const subtotal = Number(window.checkoutCartState.subtotal || 0);
  const discount = Number(window.checkoutCartState.discount || 0);
  const shippingValue = Number(shipping || 0);
  const total = Math.max(subtotal - discount + shippingValue, 0);

  window.checkoutCartState.shipping = shippingValue;
  window.checkoutCartState.shippingName = shippingName || "";
  window.checkoutCartState.total = total;

  setText("total-items", formatBRL(subtotal));
  setText(
    "total-descontos",
    discount > 0 ? `− ${formatBRL(discount)}` : formatBRL(0),
  );
  setText(
    "shipLabel",
    shippingName ? (shippingValue > 0 ? formatBRL(shippingValue) : "Grátis") : "Selecione"
  );
  setText("total-items-final", formatBRL(total));

  renderInstallments(total);
};

function renderCart(cart) {
  const cartData = cart && typeof cart === "object" ? cart : {};
  const items = Array.isArray(cartData.items) ? cartData.items : [];
  const container = document.getElementById("order-items");

  const hasItems = items.length > 0;
  const itemsSubtotal = items.reduce(
    (sum, item) => sum + Number(item?.subtotal ?? item?.sub_total ?? 0),
    0,
  );

  window.checkoutCartState.items = items;
  window.checkoutCartState.loaded = true;
  window.checkoutCartState.subtotal = hasItems
    ? Number(cartData.total ?? itemsSubtotal)
    : 0;
  window.checkoutCartState.discount = hasItems
    ? Number(cartData.coupon ?? 0)
    : 0;
  window.checkoutCartState.total = hasItems
    ? Math.max(
        Number(cartData.sub_total ?? window.checkoutCartState.subtotal - window.checkoutCartState.discount),
        0,
      )
    : 0;

  const existingOrderId = localStorage.getItem("order_id");
  const canContinue = hasItems || Boolean(existingOrderId);
  document.body.classList.toggle("checkout-cart-empty", !canContinue);
  document.querySelectorAll("#formRegister .submit-btn, #formShipping .submit-btn, #btnPix, #btnBoleto, #form-checkout__submit").forEach((button) => {
    button.disabled = !canContinue;
    button.setAttribute("aria-disabled", String(!canContinue));
  });

  const qtd = items.reduce((acc, item) => acc + Number(item.quantity || 0), 0);
  setText("summaryItemsCount", `${qtd} ${qtd === 1 ? "item" : "itens"}`);

  if (!container) return;

  if (!items.length) {
    container.innerHTML = `
      <div class="summary-empty">
        Seu carrinho está vazio. Volte para a loja e escolha umas peças, senão esse checkout fica tristinho.
      </div>
    `;
    window.updateCheckoutTotals(0, "");
    notify.warning("Seu carrinho está vazio. Adicione um produto antes de continuar.");
    return;
  }

  container.innerHTML = items
    .map((item) => {
      const image = item.imagem ? BASE_IMG + item.imagem : PLACEHOLDER_IMG;
      const name = item.descricao || "Produto OverGrace";
      const size = item.size || "Único";
      const quantity = Number(item.quantity || 1);
      const subtotal = Number(item.subtotal ?? item.sub_total ?? 0);

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
    })
    .join("");

  window.updateCheckoutTotals(
    window.checkoutCartState.shipping,
    window.checkoutCartState.shippingName || ""
  );
}

export async function carregarCarrinho() {
  try {
    const cart = await carrinhoService.get();
    const items = Array.isArray(cart?.items) ? cart.items : [];

    if (items.length > 0) {
      renderCart(cart);
      return;
    }

    const orderId = localStorage.getItem("order_id");
    if (orderId) {
      try {
        const order = await orderService.get(orderId);
        renderCart({
          items: order?.items || [],
          total: Number(order?.subtotal || 0),
          coupon: Number(order?.discount || 0),
          sub_total: Number(order?.total_amount || 0),
        });
        window.checkoutCartState.shipping = Number(order?.shipping || 0);
        window.checkoutCartState.shippingName = order?.shipping_label || order?.shipping_method || '';
        window.updateCheckoutTotals?.(
          Number(order?.shipping || 0),
          order?.shipping_label || order?.shipping_method || ''
        );
        return;
      } catch (orderError) {
        console.warn("Pedido salvo não localizado no resumo:", orderError);
        localStorage.removeItem("order_id");
        localStorage.removeItem("checkout_payment_id");
        window.dispatchEvent(new Event("checkout:stale-order"));
      }
    }

    renderCart(cart);
  } catch (error) {
    console.error("Erro ao carregar carrinho/pedido no checkout:", error);
    renderCart({ items: [] });
    notify.error(error?.message || "Erro ao carregar os dados do checkout.");
  }
}

carregarCarrinho();
