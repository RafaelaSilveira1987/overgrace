import { orderService } from "../../services/orderService.js";
import { debounce } from "../../utils/debounce.js";
import { dataUtil, valorUtil } from "../../utils/normalize.js";

let currentPage = 1;
const limit = 10;

export async function carregarDash() {
    try {
        const res = await orderService.listarDash();

        const kpis = document.getElementById("kpis");

        kpis.querySelector("#kpi-totals").innerHTML = 'R$ ' + res.totals.total_value;
        kpis.querySelector("#kpi-qty-totals").innerHTML = 'R$ ' + res.totals.total_orders;

        let ticket = res.totals.total_value / res.totals.total_orders;
        kpis.querySelector("#kpi-ticket").innerHTML = 'R$ ' + ticket.toFixed(2);

        const container = document.getElementById("pedidos-recentes");
        container.innerHTML = "";

        let htmlPedidosRecentes = "";
        res.data.forEach((order) => {

            htmlPedidosRecentes += `
                <div class="recent-order-row">
                    <span class="recent-order-num">#${order.id}</span>
                    <span class="recent-order-client">${order.client_name}</span>
                    <span class="status-pill status-enviado">${order.status}</span>
                    <span class="recent-order-val">R$ ${order.subtotal}</span>
                </div>
            `;
        });
        container.innerHTML = htmlPedidosRecentes;


        const BASE_IMG = "/overgrace/frontend/uploads/products/";
        const topProducts = document.getElementById("top-products");
        topProducts.innerHTML = "";

        let htmlTopProducts = "";
        res.items.forEach((produto) => {

            const imgSrc = produto.imagem_principal
                ? BASE_IMG + produto.imagem_principal
                : "";

            htmlTopProducts += `
                <div class="top-product-row">
                    <img class="top-product-img"
                        src="${imgSrc}"
                        alt="" />
                    <div class="top-product-info">
                        <div class="top-product-name">${produto.product_name}</div>
                        <div class="top-product-cat">${produto.qty_sum} vendas</div>
                        <div class="top-product-bar">
                            <div class="top-product-bar-fill" style="width: 100%"></div>
                        </div>
                    </div>
                    <div class="top-product-val">R$ ${produto.total_sum}</div>
                </div>
            `;
        });
        topProducts.innerHTML = htmlTopProducts;








    } catch (e) {
        console.error(e);
    }
}

const carregarComDebounce = debounce(() => {
    currentPage = 1;
    carregarDash();
}, 500);


carregarDash();
