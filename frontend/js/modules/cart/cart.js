import { carrinhoService } from "../../services/cartService.js";
import { notify } from "../../utils/notify.js";
import { marcarErro } from "../../utils/validateUI.js";
import { alertConfirm } from "../../utils/alerts.js";
import { dataUtil, valorUtil } from "../../utils/normalize.js";
import { setLoading } from "../../utils/spinner.js";

let produtoEditandoId = null;

function formatar(valor) {
  if (valor === null || valor === undefined || valor === "") return "";

  const num = Number(valor);
  if (isNaN(num)) return "";

  return num.toLocaleString("pt-BR", {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2,
  });
}

document.getElementById("formCart").addEventListener("submit", async (e) => {
  e.preventDefault();

  try {
    const selected = document.querySelector("#sizes .size-btn.active");
    const tamanho = selected ? selected.textContent : null;
    if (!tamanho) {
      notify.error("Selecione pelo menos um tamanho.");
      return;
    }

    const produto = document.getElementById("f-id").value;
    const quantidade =
      parseInt(document.getElementById("f-qtd")?.value, 10) || 1;

    await carrinhoService.adicionar(produto, tamanho, quantidade);

    contaItensCarrinho();
    notify.success("Produto adicionado ao carrinho!");
  } catch (e) {
    console.error(e);
    notify.error(e.message || "Erro ao adicionar ao carrinho");
  }
});

export async function carregarCarrinho() {
  try {
    const cart = await carrinhoService.get();
    const cartData = cart && typeof cart === "object" ? cart : {};
    const items = Array.isArray(cartData.items) ? cartData.items : [];

    const appBase = window.location.pathname.includes("/overgrace-main")
      ? "/overgrace-main"
      : window.location.pathname.includes("/overgrace")
        ? "/overgrace"
        : "";
    const BASE_IMG = `${appBase}/frontend/uploads/products/`;

    // Um retorno antigo da API pode conter totais residuais. Sem itens, o carrinho vale zero.
    const hasItems = items.length > 0;
    const itemsSubtotal = items.reduce(
      (sum, item) => sum + Number(item?.subtotal ?? item?.sub_total ?? 0),
      0,
    );
    const subtotal = hasItems
      ? Number(cartData.total ?? itemsSubtotal)
      : 0;
    const desconto = hasItems ? Number(cartData.coupon ?? 0) : 0;
    const total = hasItems
      ? Math.max(Number(cartData.sub_total ?? subtotal - desconto), 0)
      : 0;

    document.getElementById("sub-total-items").textContent =
      "R$ " + formatar(subtotal);
    document.getElementById("cupom-val").textContent =
      "R$ " + formatar(desconto);
    document.getElementById("couponDesc").textContent =
      cartData.coupon_description || "";
    document.getElementById("total-items").textContent =
      "R$ " + formatar(total);

    if (items.length > 0) {
      const renderedItems = items
        .map((t) => {
          const itemId = t?.id ?? "";
          const nome = t?.descricao || "Produto";
          const tamanho = t?.size || "";
          const quantidade = t?.quantity ?? 1;
          const subtotalItem = Number(t?.subtotal ?? 0);

          return `
            <div class="cart-item ui-loading-container" id="item-${itemId}">
              <div class="ui-loading-overlay hidden" id="loading-${itemId}">
                <div class="ui-spinner"></div>
              </div>
              <div class="item-product">
                <img
                  class="item-thumb"
                  src="${BASE_IMG + (t?.imagem || "")}" 
                  alt="${nome}" />
                <div class="item-details">
                  <p class="item-name">${nome}</p>
                  <p class="item-meta">
                    <span>Tamanho: ${tamanho}</span>
                  </p>
                  <button type="button" class="item-remove btn-deletar" data-id="${itemId}">
                    Remover
                  </button>
                </div>
              </div>
              <div>
                <div class="qty-control">
                  <button type="button" class="qty-btn btn-atualizar" data-id="${itemId}" data-action="minus">
                    −
                  </button>
                  <span class="qty-value" id="qty-${itemId}">${quantidade}</span>
                  <button type="button" class="qty-btn btn-atualizar" data-id="${itemId}" data-action="plus">
                    +
                  </button>
                </div>
              </div>
              <div class="item-subtotal" id="sub-${itemId}">R$ ${formatar(subtotalItem)}</div>
              <button
                type="button"
                class="item-delete btn-deletar"
                data-id="${itemId}"
                title="Remover">
                ×
              </button>
            </div>`;
        })
        .join("");

      document.getElementById("list-items").innerHTML = renderedItems;
    } else {
      document.getElementById("list-items").innerHTML = `
        <div class="cart-item">
          <h4>Nenhum item adicionado ao carrinho</h4>
        </div>`;
    }

    document.getElementById("itemCount").textContent =
      `${items.length} item${items.length === 1 ? "" : "s"} selecionado${items.length === 1 ? "" : "s"}`;

    const checkoutButton = document.querySelector(".checkout-btn");
    if (checkoutButton) {
      checkoutButton.classList.toggle("disabled", !hasItems);
      checkoutButton.setAttribute("aria-disabled", String(!hasItems));
      checkoutButton.dataset.cartEmpty = String(!hasItems);
      checkoutButton.tabIndex = hasItems ? 0 : -1;
    }
  } catch (e) {
    document.getElementById("list-items").innerHTML = `
      <div class="cart-item">
        <h4>Nenhum item adicionado ao carrinho</h4>
      </div>`;
    document.getElementById("sub-total-items").textContent = "R$ 0,00";
    document.getElementById("cupom-val").textContent = "R$ 0,00";
    document.getElementById("couponDesc").textContent = "";
    document.getElementById("total-items").textContent = "R$ 0,00";
    document.getElementById("itemCount").textContent = "0 itens selecionados";
    const checkoutButton = document.querySelector(".checkout-btn");
    if (checkoutButton) {
      checkoutButton.classList.add("disabled");
      checkoutButton.setAttribute("aria-disabled", "true");
      checkoutButton.dataset.cartEmpty = "true";
      checkoutButton.tabIndex = -1;
    }
    console.error(e);
  }
}

export async function removerItemCarrinho(id) {
  try {
    await carrinhoService.remover(id);

    notify.success("Produto removido do carrinho!");

    // atualizar grid
    carregarCarrinho();
  } catch (e) {
    notify.error("Algo deu errado na exclusão do item no carrinho!");
  }
}

export async function atualizaItemCarrinho(id, quantity) {
  const el = document.getElementById(`item-${id}`);

  try {
    setLoading(el, true, { delay: 1500 });

    await carrinhoService.atualizar(id, quantity);

    notify.success("Produto atualizado com sucesso!");

    // atualizar grid
    carregarCarrinho();
  } catch (e) {
    notify.error("Algo deu errado!");
  }
}

export async function aplicarCupom(code) {
  if (!code || !code.trim()) {
    const msg = document.getElementById("couponMsg");
    msg.textContent = "Informe um cupom.";
    msg.style.color = "#8b3a2a";

    notify.warning("Digite um cupom antes de aplicar");
    return; // 🔥 corta execução aqui
  }

  try {
    const coupon = await carrinhoService.aplicarCupom(code);

    const msg = document.getElementById("couponMsg");
    console.log(msg);

    if (coupon.success) {
      msg.textContent = `✓ Cupom aplicado: R$${formatar(coupon.desconto)} de desconto`;
      msg.style.color = "#3a6248";
    } else {
      msg.textContent = "Cupom inválido ou expirado.";
      msg.style.color = "#8b3a2a";
      document.getElementById("discountRow").style.display = "none";

      notify.error(coupon.mensagem);
      return;
    }

    notify.success(coupon.mensagem);

    carregarCarrinho();
  } catch (e) {
    notify.error("Algo deu errado!");
  }
}

document.addEventListener("click", function (e) {
  if (e.target.matches(".btn-deletar") || e.target.closest(".btn-deletar")) {
    const btn = e.target.closest(".btn-deletar");

    const id = btn.getAttribute("data-id");

    console.log("ID FINAL:", id);

    removerItemCarrinho(id);
  }
});

document.addEventListener("click", function (e) {
  const btn = e.target.closest(".btn-atualizar");
  if (!btn) return;

  const id = btn.dataset.id;
  const action = btn.dataset.action;

  const qtyElement = document.getElementById(`qty-${id}`);
  let currentQty = parseInt(qtyElement.textContent);

  if (action === "plus") {
    currentQty++;
  }

  if (action === "minus") {
    currentQty--;
  }

  // evita quantidade inválida
  if (currentQty < 1) return;

  qtyElement.textContent = currentQty;

  console.log("ID:", id);
  console.log("Nova quantidade:", currentQty);

  atualizaItemCarrinho(id, currentQty);
});

document.addEventListener("click", function (e) {
  const checkoutButton = e.target.closest(".checkout-btn");
  if (!checkoutButton || checkoutButton.dataset.cartEmpty !== "true") return;

  e.preventDefault();
  notify.warning("Adicione pelo menos um produto antes de finalizar o pedido.");
});

document.addEventListener("click", function (e) {
  const btn = e.target.closest(".btn-apply-coupon");
  if (!btn) return;

  const code = document
    .getElementById("couponInput")
    .value.trim()
    .toUpperCase();

  console.log(code);

  aplicarCupom(code);
});

carregarCarrinho();
