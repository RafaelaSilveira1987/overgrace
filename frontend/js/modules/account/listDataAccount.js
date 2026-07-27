import {
    authService
} from "/overgrace/frontend/js/services/authService.js?v=7";
import { orderService } from '../../services/orderService.js';

const BASE_IMG = "/overgrace/frontend/uploads/products/";



try {
    const user = await authService.getUser();

    if (user.role !== "client") {
        window.location.href = "/overgrace/login";
    }

    const fullName = user.name || user.email;
    const nameParts = fullName.split(" ");

    document.querySelector(".avatar-circle").textContent = initials(fullName);
    document.querySelector(".sidebar-name").textContent = fullName;
    document.querySelector(".sidebar-email").textContent = user.email;
    document.getElementById("headerAccountName").textContent = `Ola, ${fullName.split(" ")[0]}`;
    document.getElementById("inp-nome").value = nameParts[0] || "";
    document.getElementById("inp-sobrenome").value = nameParts.slice(1).join(" ");
    document.getElementById("inp-email").value = user.email;
    document.getElementById("inp-cpf").value = formatCpf(user.cpf);
    document.getElementById("inp-tel").value = formatPhone(user.telefone);

    const orders = await orderService.listarPedidosCliente({
        client_id: user.id
    });

    console.log("chegou aqui")


    const summary = document.getElementById("summary-cards");
    summary.querySelector(".pedidos_realizados").innerHTML = orders.pagination.total;
    summary.querySelector(".em_transito").innerHTML = orders.pagination.total;

    const totalSubtotal = orders.data.reduce((total, order) => {
        return total + Number(order.subtotal);
    }, 0);

    const subtotalFormatado = totalSubtotal.toLocaleString("pt-BR", {
        style: "currency",
        currency: "BRL",
    });

    summary.querySelector(".valor_investido").innerHTML = subtotalFormatado;

    const listaPedidos = document.getElementById("lista-pedidos");
    const html = orders.data.map(order => `
                <div class="order-card">

                    <div class="order-row" onclick="toggleOrder('od${order.id}')">
                        <div>
                            <p class="order-id">#FOR-${String(order.id).padStart(5, "0")}</p>
                            <p class="order-date">
                                ${new Date(order.created_at).toLocaleDateString("pt-BR")}
                            </p>
                        </div>

                        <div class="order-items-thumb">
                            ${order.items.map(item => `
                                <img
                                    class="order-thumb"
                                    src="${BASE_IMG}${item.imagem_principal}"
                                    alt="${item.product_name}"
                                />
                            `).join("")}
                        </div>

                        <div class="order-total">
                            ${Number(order.total_amount).toLocaleString("pt-BR", {
                    style: "currency",
                    currency: "BRL"
                })}
                        </div>

                        <span class="status-pill ${order.status}">
                            ${{
                        pending: "Pendente",
                        processing: "Em preparação",
                        shipped: "Em trânsito",
                        delivered: "Entregue",
                        cancelled: "Cancelado"
                    }[order.status] ?? order.status}
                        </span>

                        <div class="order-chevron">></div>
                    </div>

                    <div class="order-detail" id="od${order.id}">
                        <div class="od-grid">

                            <div>

                                <div class="od-items">

                                    ${order.items.map(item => `
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
                                                ${Number(item.subtotal).toLocaleString("pt-BR", {
                                                    style: "currency",
                                                    currency: "BRL"
                                                })}
                                            </p>

                                        </div>
                                    `).join("")}

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
                                </div>

                            </div>

                            <div class="od-summary">

                                <p class="od-sum-title">Resumo do pedido</p>

                                <div class="od-sum-row">
                                    <span class="l">Subtotal</span>
                                    <span class="v">
                                        ${Number(order.subtotal).toLocaleString("pt-BR", {
                                            style: "currency",
                                            currency: "BRL"
                                        })}
                                    </span>
                                </div>

                                ${Number(order.discount) > 0 ? `
                                    <div class="od-sum-row">
                                        <span class="l">Desconto</span>
                                        <span class="v">
                                            - ${Number(order.discount).toLocaleString("pt-BR", {
                                                    style: "currency",
                                                    currency: "BRL"
                                                })}
                                                                    </span>
                                                                </div>
                                                            ` : ""}

                                <div class="od-sum-row">
                                    <span class="l">Frete</span>
                                    <span class="v">
                                        ${Number(order.shipping) === 0
                                        ? "Grátis"
                                        : Number(order.shipping).toLocaleString("pt-BR", {
                                            style: "currency",
                                            currency: "BRL"
                                        })}
                                    </span>
                                </div>

                                <div class="od-sum-row">
                                    <span class="l">Status do pagamento</span>
                                    <span class="v">
                                        ${{
                                            pending: "Pendente",
                                            paid: "Pago",
                                            refunded: "Estornado",
                                            canceled: "Cancelado"
                                        }[order.payment_status] ?? order.payment_status}
                                    </span>
                                </div>

                                <div class="od-sum-total">
                                    <span class="l">Total</span>
                                    <span class="v">
                                        ${Number(order.total_amount).toLocaleString("pt-BR", {
                                            style: "currency",
                                            currency: "BRL"
                                        })}
                                    </span>
                                </div>

                            </div>

                        </div>
                    </div>

                </div>
            `).join("");

    listaPedidos.innerHTML = html;


} catch (err) {
    authService.logout();
    //window.location.href = "/overgrace/login";

    console.error(err);
}