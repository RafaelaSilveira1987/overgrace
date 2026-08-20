import { authService } from "/overgrace/frontend/js/services/authService.js?v=7";
import { orderService } from "../../services/orderService.js";

const BASE_IMG = "/overgrace/frontend/uploads/products/";

const ORDER_STATUS = {
  pending: {
    label: "Pendente",
    className: "pending",
    group: "active",
  },

  paid: {
    label: "Pagamento confirmado",
    className: "processing",
    group: "active",
  },

  approved: {
    label: "Pagamento aprovado",
    className: "processing",
    group: "active",
  },

  processing: {
    label: "Em preparação",
    className: "processing",
    group: "active",
  },

  in_process: {
    label: "Em preparação",
    className: "processing",
    group: "active",
  },

  shipped: {
    label: "Em trânsito",
    className: "shipped",
    group: "active",
  },

  in_transit: {
    label: "Em trânsito",
    className: "shipped",
    group: "active",
  },

  delivered: {
    label: "Entregue",
    className: "delivered",
    group: "delivered",
  },

  completed: {
    label: "Concluído",
    className: "delivered",
    group: "delivered",
  },

  expired: {
    label: "Expirado",
    className: "expired",
    group: "expired",
  },

  canceled: {
    label: "Cancelado",
    className: "cancelled",
    group: "canceled",
  },

  cancelled: {
    label: "Cancelado",
    className: "cancelled",
    group: "canceled",
  },

  refunded: {
    label: "Estornado",
    className: "cancelled",
    group: "canceled",
  },

  failed: {
    label: "Falhou",
    className: "cancelled",
    group: "canceled",
  },

  rejected: {
    label: "Recusado",
    className: "cancelled",
    group: "canceled",
  },
};

function normalizeOrderStatus(status) {
  return String(status || "")
    .trim()
    .toLowerCase();
}

function getOrderStatus(status) {
  const normalizedStatus = normalizeOrderStatus(status);

  return (
    ORDER_STATUS[normalizedStatus] || {
      label: status || "Não informado",
      className: "pending",
      group: "active",
    }
  );
}

try {
  const user = await authService.getUser();

  if (user.role !== "client") {
    window.location.href = "/overgrace/login";
  }

  const userName = String(user.nome || user.name || "").trim();

  const displayName = userName || "Cliente";
  const email = String(user.email || "").trim();

  const nameParts = displayName.split(/\s+/).filter(Boolean);

  const firstName = nameParts[0] || "Cliente";
  const lastName = nameParts.slice(1).join(" ");

  // Identificação visual
  document.querySelector(".avatar-circle").textContent = initials(displayName);

  document.querySelector(".sidebar-name").textContent = displayName;

  document.querySelector(".sidebar-email").textContent = email;

  document.getElementById("headerAccountName").textContent =
    `Olá, ${firstName}`;

  // Campos do perfil
  document.getElementById("inp-nome").value =
    firstName === "Cliente" ? "" : firstName;

  document.getElementById("inp-sobrenome").value = lastName;

  document.getElementById("inp-email").value = email;

  document.getElementById("inp-cpf").value = formatCpf(user.cpf);

  document.getElementById("inp-tel").value = formatPhone(user.telefone);

  const birthInput = document.getElementById("inp-nasc");

  if (birthInput) {
    birthInput.value = normalizeDateForInput(
      user.data_nascimento ?? user.dataNascimento ?? user.birth_date ?? "",
    );
  }

  const orders = await orderService.listarPedidosCliente({
    client_id: user.id,
  });

  const orderList = Array.isArray(orders?.data) ? orders.data : [];

  const summary = document.getElementById("summary-cards");

  const totalOrders = Number(orders?.pagination?.total ?? orderList.length);

  const ordersInTransit = orderList.filter((order) => {
    const status = normalizeOrderStatus(order.status);

    return ["shipped", "in_transit"].includes(status);
  }).length;

  const totalInvested = orderList
    .filter((order) => {
      const paymentStatus = normalizeOrderStatus(order.payment_status);

      return ["paid", "approved"].includes(paymentStatus);
    })
    .reduce((total, order) => {
      return total + Number(order.total_amount || 0);
    }, 0);

  summary.querySelector(".pedidos_realizados").textContent = totalOrders;

  summary.querySelector(".em_transito").textContent = ordersInTransit;

  summary.querySelector(".valor_investido").textContent =
    totalInvested.toLocaleString("pt-BR", {
      style: "currency",
      currency: "BRL",
    });

  const sidebarOrdersCount = document.getElementById("sidebarOrdersCount");

  if (sidebarOrdersCount) {
    sidebarOrdersCount.textContent = totalOrders;
  }

  const listaPedidos = document.getElementById("lista-pedidos");

  const html = orderList
    .map((order) => {
      const statusInfo = getOrderStatus(order.status);

      return `
              <div
                class="order-card"
                data-group="${statusInfo.group}"
                data-status="${normalizeOrderStatus(order.status)}"
              >

                    <div class="order-row" onclick="toggleOrder('od${order.id}')">
                        <div>
                            <p class="order-id">#FOR-${String(order.id).padStart(5, "0")}</p>
                            <p class="order-date">
                                ${new Date(order.created_at).toLocaleDateString("pt-BR")}
                            </p>
                        </div>

                        <div class="order-items-thumb">
                            ${order.items
                              .map(
                                (item) => `
                                <img
                                    class="order-thumb"
                                    src="${BASE_IMG}${item.imagem_principal}"
                                    alt="${item.product_name}"
                                />
                            `,
                              )
                              .join("")}
                        </div>

                        <div class="order-total">
                                ${Number(order.total_amount).toLocaleString(
                                  "pt-BR",
                                  {
                                    style: "currency",
                                    currency: "BRL",
                                  },
                                )}
                        </div>

                        <span class="status-pill ${statusInfo.className}">
  ${statusInfo.label}
</span>

                        <div class="order-chevron">></div>
                    </div>

                    <div class="order-detail" id="od${order.id}">
                        <div class="od-grid">

                            <div>

                                <div class="od-items">

                                    ${order.items
                                      .map(
                                        (item) => `
                                        <div class="od-item">

                                            <img
                                                class="od-thumb"
                                                src="${BASE_IMG}${item.imagem_principal}"
                                                alt="${item.product_name}"
                                            />

                                            <div>
                                                <p class="od-name">${item.product_name}</p>
                                                <p class="od-meta">
                                                    Tamanho ${item.size} ◆ Qtd: ${item.quantity}
                                                </p>
                                            </div>

                                            <p class="od-price">
                                                ${Number(
                                                  item.subtotal,
                                                ).toLocaleString("pt-BR", {
                                                  style: "currency",
                                                  currency: "BRL",
                                                })}
                                            </p>

                                        </div>
                                    `,
                                      )
                                      .join("")}

                                </div>

                                <div class="od-tracking">
                                    <div>
                                        <p class="od-tracking-label">Código de rastreio</p>
                                        <p class="od-tracking-code">-</p>
                                    </div>

                                    <button class="od-track-btn" disabled>
                                        Rastrear →
                                    </button>
                                </div>

                                <div class="od-action-row">
                                    <button class="od-action-btn" onclick="requestExchange(${order.id})">
                                        Solicitar troca
                                    </button>

                                    <button class="od-action-btn" onclick="viewInvoice(${order.id})">
                                        Ver nota fiscal
                                    </button>

                                    ${
                                      order.payment_method == "pix" &&
                                      order.payment_status == "pending"
                                        ? `
                                        <button
                                            class="od-action-btn btn-pix"
                                            data-id="${order.id}">
                                            Mostrar QR Code Pix
                                        </button>
                                        `
                                        : ""
                                    }

                                </div>

                            </div>

                            <div class="od-summary">

                                <p class="od-sum-title">Resumo do pedido</p>

                                <div class="od-sum-row">
                                    <span class="l">Subtotal</span>
                                    <span class="v">
                                        ${Number(order.subtotal).toLocaleString(
                                          "pt-BR",
                                          {
                                            style: "currency",
                                            currency: "BRL",
                                          },
                                        )}
                                    </span>
                                </div>

                                ${
                                  Number(order.discount) > 0
                                    ? `
                                    <div class="od-sum-row">
                                        <span class="l">Desconto</span>
                                        <span class="v">
                                            - ${Number(
                                              order.discount,
                                            ).toLocaleString("pt-BR", {
                                              style: "currency",
                                              currency: "BRL",
                                            })}
                                                                    </span>
                                                                </div>
                                                            `
                                    : ""
                                }

                                <div class="od-sum-row">
                                    <span class="l">Frete</span>
                                    <span class="v">
                                        ${
                                          Number(order.shipping) === 0
                                            ? "Grátis"
                                            : Number(
                                                order.shipping,
                                              ).toLocaleString("pt-BR", {
                                                style: "currency",
                                                currency: "BRL",
                                              })
                                        }
                                    </span>
                                </div>

                                <div class="od-sum-row">
                                    <span class="l">Status do pagamento</span>
                                    <span class="v">
                                        ${
                                          {
                                            pending: "Pendente",
                                            paid: "Pago",
                                            refunded: "Estornado",
                                            canceled: "Cancelado",
                                          }[order.payment_status] ??
                                          order.payment_status
                                        }
                                    </span>
                                </div>

                                <div class="od-sum-total">
                                    <span class="l">Total</span>
                                    <span class="v">
                                        ${Number(
                                          order.total_amount,
                                        ).toLocaleString("pt-BR", {
                                          style: "currency",
                                          currency: "BRL",
                                        })}
                                    </span>
                                </div>

                            </div>

                            <div class="pix-detail" id="pix-${order.id}">
                                <div class="pix-loading">
                                    Carregando...
                                </div>
                            </div>

                        </div>
                    </div>

                              </div>
          `;
    })
    .join("");

  listaPedidos.innerHTML = html;

  document.querySelectorAll(".btn-pix").forEach((btn) => {
    btn.addEventListener("click", () => {
      togglePix(btn.dataset.id);
    });
  });

  const filterButtons = document.querySelectorAll(".order-filter");

  filterButtons.forEach((button) => {
    button.addEventListener("click", () => {
      const selectedFilter = button.dataset.filter || "all";

      filterButtons.forEach((item) => {
        item.classList.remove("active");
      });

      button.classList.add("active");

      listaPedidos.querySelectorAll(".order-card").forEach((card) => {
        const shouldShow =
          selectedFilter === "all" || card.dataset.group === selectedFilter;

        card.style.display = shouldShow ? "" : "none";
      });
    });
  });
} catch (err) {
  authService.logout();
  //window.location.href = "/overgrace/login";

  console.error(err);
}

async function togglePix(orderId) {
  const container = document.getElementById(`pix-${orderId}`);

  container.classList.toggle("open");

  if (container.dataset.loaded) {
    return;
  }

  const user = await authService.getUser();

  const payment = await orderService.getPaymentOrder(orderId, {
    client_id: user.id,
  });

  const pix = payment.data;

  container.innerHTML = `
        <div class="pix-box">

            <div class="pix-info">
                <span><strong>Valor:</strong> R$ ${Number(pix.amount).toFixed(2)}</span>
                <span><strong>Status:</strong> ${pix.status}</span>
            </div>

            <img
                class="pix-qrcode"
                src="data:image/png;base64,${pix.qr_code_base64}"
                alt="QR Code Pix"
            />

            <textarea
                class="pix-copy"
                readonly>${pix.pix_copy_paste}</textarea>

            <div class="pix-actions">
                <button onclick="copiarPix('${pix.pix_copy_paste}')">
                    Copiar código
                </button>

                <button onclick="checkPix(${orderId})">
                    Atualizar
                </button>
            </div>

        </div>
        `;

  container.dataset.loaded = "true";
}

function normalizeDateForInput(value) {
  if (!value) {
    return "";
  }

  const rawValue = String(value).trim();

  /*
   * Formatos aceitos:
   * 1994-03-12
   * 1994-03-12 00:00:00
   * 1994-03-12T00:00:00
   */
  const isoMatch = rawValue.match(
    /^(\d{4})-(\d{2})-(\d{2})/
  );

  if (isoMatch) {
    return `${isoMatch[1]}-${isoMatch[2]}-${isoMatch[3]}`;
  }

  /*
   * Formato brasileiro:
   * 12/03/1994
   */
  const brMatch = rawValue.match(
    /^(\d{2})\/(\d{2})\/(\d{4})$/
  );

  if (brMatch) {
    return `${brMatch[3]}-${brMatch[2]}-${brMatch[1]}`;
  }

  return "";
}