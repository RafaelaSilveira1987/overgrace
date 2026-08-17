import { notify } from '../../utils/notify.js';
import { orderService } from '../../services/orderService.js';
import { paymentService } from '../../services/paymentService.js';

export async function criarPagamento(event, method = "pix") {

  startButtonLoading(event);

  try { 

    const order_id = localStorage.getItem("order_id");

    const order = await orderService.get(order_id);

    const client_id = order.client_id;

    const payment = await paymentService.criar({
      order_id,
      client_id,
      method
    });

    if (!payment.success) {
      notify.error("Algo deu errado na geração do Pix, tente novamente mais tarde!");
      return;
    }

    const orderPayment = await orderService.getPaymentOrder(order_id, {
      client_id
    });

    mostrarResultado(
      orderPayment.data.method ?? method,
      orderPayment.data
    );

  } catch (e) {

    notify.error(e.message);

  } finally {

    stopButtonLoading(event);

  }

}

function mostrarResultado(method, payment) {

  pararPolling();

  document.getElementById("checkoutLayout").style.display = "none";
  document.getElementById("stepsBar").style.display = "none";
  document.getElementById("confirmScreen").classList.add("active");

  switch (method) {

    case "pix":
      console.log("caiu no mostrar resultado");
      mostrarTelaPix(payment);
      break;

    case "boleto":
      mostrarTelaBoleto(payment);
      break;

    case "card":
      mostrarTelaCartao(payment);
      break;

  }

}

function esconderCards() {

  document.getElementById("pixNextCard").hidden = true;
  document.getElementById("boletoNextCard").hidden = true;
  document.getElementById("analysisNextCard").hidden = true;

}

function preencherResultado(payment) {

  esconderCards();


  document.getElementById("confirmOrderNum").textContent =
    "#OVG-" + payment.order_id;


  document.getElementById("confirmTotal").textContent =
    formatBRL(Number(payment.amount));


  document.getElementById("confirmPaymentMethod").textContent =
    (payment.method || "").toUpperCase();

}

function mostrarTelaPix(payment) {


  preencherResultado(payment);

  switch (payment.status) {

    case "pending":

      console.log("caiu no status de pendente");

      document.getElementById("confirmIcon").textContent = "⌛";

      document.getElementById("confirmEyebrow").textContent =
        "Pedido criado";


      document.getElementById("confirmTitle").innerHTML =
        "Aguardando <em>pagamento</em>";


      document.getElementById("confirmSub").textContent =
        "Seu pedido foi criado. Efetue o pagamento via PIX para iniciarmos a separação.";


      // QR CODE
      const qr = document.getElementById("pixQrCode");

      if (payment.qr_code_base64) {

        qr.src =
          "data:image/png;base64," + payment.qr_code_base64;

        qr.style.display = "block";

      }


      // copia e cola
      document.getElementById("pixCode").textContent =
        payment.pix_copy_paste || "";


      document.getElementById("pixNextCard").hidden = false;


      if (payment.id) {

        iniciarPolling(payment.id);

      }


      break;



    case "paid":

      document.getElementById("confirmIcon").textContent = "✓";

      document.getElementById("confirmEyebrow").textContent =
        "Pagamento aprovado";


      document.getElementById("confirmTitle").innerHTML =
        "Pedido <em>confirmado!</em>";


      document.getElementById("confirmSub").textContent =
        "Recebemos seu PIX e seu pedido já está sendo preparado.";


      break;

    case "approved":

      document.getElementById("confirmIcon").textContent = "✓";

      document.getElementById("confirmEyebrow").textContent =
        "Pagamento aprovado";


      document.getElementById("confirmTitle").innerHTML =
        "Pedido <em>confirmado!</em>";


      document.getElementById("confirmSub").textContent =
        "Recebemos seu PIX e seu pedido já está sendo preparado.";


      break;



    case "rejected":
    case "cancelled":

      document.getElementById("confirmIcon").textContent = "✕";

      document.getElementById("confirmEyebrow").textContent =
        "Pagamento recusado";


      document.getElementById("confirmTitle").innerHTML =
        "<em>Pagamento recusado</em>";


      document.getElementById("confirmSub").textContent =
        "O pagamento PIX não foi confirmado.";


      break;

  }

}

function mostrarTelaCartao(payment) {

  preencherResultado(payment);

  switch (payment.status) {

    case "approved":

      document.getElementById("confirmIcon").textContent = "✓";
      document.getElementById("confirmEyebrow").textContent = "Pagamento aprovado";
      document.getElementById("confirmTitle").innerHTML = "Pedido <em>confirmado!</em>";
      document.getElementById("confirmSub").textContent =
        "Seu pagamento foi aprovado e seu pedido já está sendo preparado.";

      break;

    case "in_process":

      document.getElementById("confirmIcon").textContent = "⏳";
      document.getElementById("confirmEyebrow").textContent = "Pagamento em análise";
      document.getElementById("confirmTitle").innerHTML = "Pagamento <em>em análise</em>";
      document.getElementById("confirmSub").textContent =
        "Estamos aguardando a confirmação da operadora do cartão.";

      document.getElementById("analysisNextCard").hidden = false;

      if (payment.id) {
        iniciarPolling(payment.id);
      }

      break;

    case "rejected":

      document.getElementById("confirmIcon").textContent = "✕";
      document.getElementById("confirmEyebrow").textContent = "Pagamento recusado";
      document.getElementById("confirmTitle").innerHTML = "<em>Pagamento recusado</em>";
      document.getElementById("confirmSub").textContent =
        "A operadora recusou a transação. Tente outro cartão ou outro meio de pagamento.";

      break;

  }

}

function mostrarTelaBoleto(payment) {

  preencherResultado(payment);

  switch (payment.status) {

    case "pending":

      document.getElementById("confirmIcon").textContent = "🧾";
      document.getElementById("confirmEyebrow").textContent = "Boleto gerado";
      document.getElementById("confirmTitle").innerHTML = "Aguardando <em>pagamento</em>";
      document.getElementById("confirmSub").textContent =
        "Seu boleto foi gerado. Após a compensação iniciaremos a preparação do pedido.";

      document.getElementById("boletoCode").textContent =
        payment.boleto_code || "";

      document.getElementById("boletoDueDate").textContent =
        payment.boleto_due_date || "--/--/----";

      document.getElementById("boletoNextCard").hidden = false;

      if (payment.id) {
        iniciarPolling(payment.id);
      }

      break;

    case "approved":

      document.getElementById("confirmIcon").textContent = "✓";
      document.getElementById("confirmEyebrow").textContent = "Pagamento aprovado";
      document.getElementById("confirmTitle").innerHTML = "Pedido <em>confirmado!</em>";
      document.getElementById("confirmSub").textContent =
        "O boleto foi compensado e seu pedido já está sendo preparado.";

      break;

    case "rejected":

      document.getElementById("confirmIcon").textContent = "✕";
      document.getElementById("confirmEyebrow").textContent = "Pagamento recusado";
      document.getElementById("confirmTitle").innerHTML = "<em>Pagamento recusado</em>";
      document.getElementById("confirmSub").textContent =
        "O boleto foi cancelado ou não pôde ser processado.";

      break;

  }

}

let paymentInterval = null;

function iniciarPolling(paymentId) {

  clearInterval(paymentInterval);

  paymentInterval = setInterval(async () => {

    try {

      const payment = await paymentService.consultar(paymentId);

      switch (payment.data.status) {

        // Continua aguardando
        case "pending":
        case "in_process":
          return;

        // Finalizou
        case "paid":
        case "approved":
        case "rejected":
        case "cancelled":
        case "refunded":
        case "charged_back":

          clearInterval(paymentInterval);
          paymentInterval = null;

          mostrarResultado(payment.data.method, payment.data);

          return;

        // Qualquer outro status desconhecido
        default:

          console.warn("Status de pagamento desconhecido:", payment.data.status);

          return;

      }

    } catch (e) {

      console.error("Erro ao consultar pagamento:", e);

    }

  }, 5000);

}

function pararPolling() {

  clearInterval(paymentInterval);
  paymentInterval = null;

}

function voltarCheckoutDemo() {

  pararPolling();

  document.getElementById("confirmScreen").classList.remove("active");

  document.getElementById("checkoutLayout").style.display = "";
  document.getElementById("stepsBar").style.display = "";

}


export function startButtonLoading(event) {

  const button = event.currentTarget;

  button.classList.add("loading");
  button.disabled = true;

  document.querySelectorAll("button").forEach(btn => {
    if (btn !== button) {
      btn.disabled = true;
    }
  });

}

export function stopButtonLoading(event) {

  const button = event.currentTarget;

  button.classList.remove("loading");

  document.querySelectorAll("button").forEach(btn => {
    btn.disabled = false;
  });

}

/* Máscaras */
export function maskCPF(el) {
  let value = el.value.replace(/\D/g, '').slice(0, 11);

  if (value.length > 9) value = value.replace(/(\d{3})(\d{3})(\d{3})(\d{1,2})/, '$1.$2.$3-$4');
  else if (value.length > 6) value = value.replace(/(\d{3})(\d{3})(\d{1,3})/, '$1.$2.$3');
  else if (value.length > 3) value = value.replace(/(\d{3})(\d{1,3})/, '$1.$2');

  el.value = value;
}

export function maskPhone(el) {
  let value = el.value.replace(/\D/g, '').slice(0, 11);

  if (value.length > 10) value = value.replace(/(\d{2})(\d{5})(\d{4})/, '($1) $2-$3');
  else if (value.length > 6) value = value.replace(/(\d{2})(\d{4,5})(\d{0,4})/, '($1) $2-$3');
  else if (value.length > 2) value = value.replace(/(\d{2})(\d+)/, '($1) $2');

  el.value = value;
}

export function maskCEP(el) {
  let value = el.value.replace(/\D/g, '').slice(0, 8);
  if (value.length > 5) value = value.replace(/(\d{5})(\d{1,3})/, '$1-$2');
  el.value = value;
}

export async function fetchCEP(cep) {
  const cleanCep = String(cep || '').replace(/\D/g, '');
  if (cleanCep.length !== 8) return;

  try {
    const response = await fetch('https://viacep.com.br/ws/' + cleanCep + '/json/');
    const data = await response.json();

    if (!data.erro) {
      document.getElementById('endereco').value = data.logradouro || '';
      document.getElementById('bairro').value = data.bairro || '';
      document.getElementById('cidade').value = data.localidade || '';
      document.getElementById('estado').value = data.uf || '';
      updateAddressPreview();
    }
  } catch (error) {
    console.error('Erro ao buscar CEP', error);
  }
}

let currentStep = 1;
let shippingCost = 0;
let shippingLabel = 'Frete Grátis';

function formatBRL(value) {
  const number = Number(value || 0);
  return number.toLocaleString('pt-BR', {
    style: 'currency',
    currency: 'BRL'
  });
}

function updateDeliveryResume() {
  const endereco = document.getElementById('endereco')?.value?.trim();
  const numero = document.getElementById('numero')?.value?.trim();
  const bairro = document.getElementById('bairro')?.value?.trim();
  const cidade = document.getElementById('cidade')?.value?.trim();
  const estado = document.getElementById('estado')?.value?.trim();
  const cep = document.getElementById('cep')?.value?.trim();

  const resumo = [
    endereco && numero ? `${endereco}, ${numero}` : endereco,
    bairro,
    cidade && estado ? `${cidade}/${estado}` : cidade,
    cep ? `CEP ${cep}` : ''
  ].filter(Boolean).join(' · ');

  const deliveryResume = document.getElementById('deliveryResume');
  if (deliveryResume) {
    deliveryResume.textContent = resumo || 'Endereço informado no passo anterior';
  }
}

function updateAddressPreview() {
  const endereco = document.getElementById('endereco')?.value?.trim();
  const bairro = document.getElementById('bairro')?.value?.trim();
  const cidade = document.getElementById('cidade')?.value?.trim();
  const estado = document.getElementById('estado')?.value?.trim();
  const preview = document.getElementById('addressPreview');

  if (!preview) return;

  const text = [endereco, bairro, cidade && estado ? `${cidade}/${estado}` : cidade].filter(Boolean).join(' · ');
  preview.textContent = text || 'Digite o CEP para buscar rua, bairro e cidade automaticamente.';
}

function refreshTotals() {
  if (typeof window.updateCheckoutTotals === 'function') {
    window.updateCheckoutTotals(shippingCost, shippingLabel);
    return;
  }

  const shipLabelEl = document.getElementById('shipLabel');
  if (shipLabelEl) shipLabelEl.textContent = shippingCost > 0 ? formatBRL(shippingCost) : 'Grátis';
}

/* Navegação de passos */
export function goToStep(n, force = false) {
  if (!force && n > currentStep + 1) return;

  document.querySelectorAll('.panel').forEach((panel) => panel.classList.remove('active'));

  const target = document.getElementById('panel' + n);
  if (!target) return;

  target.classList.add('active');

  document.querySelectorAll('.step-item').forEach((step, index) => {
    step.classList.remove('active', 'done');

    if (index + 1 < n) step.classList.add('done');
    if (index + 1 === n) step.classList.add('active');
  });

  currentStep = n;

  if (n === 2) updateDeliveryResume();
  if (n === 3) refreshTotals();

  window.scrollTo({ top: 0, behavior: 'smooth' });
}

/* Frete */
export function selectShip(option, cost = 0, label = 'Frete Grátis') {
  document.querySelectorAll('.ship-option').forEach((item) => item.classList.remove('selected'));
  option.classList.add('selected');

  const radio = option.querySelector('input[type="radio"]');
  if (radio) radio.checked = true;

  shippingCost = Number(cost || 0);
  shippingLabel = label || (shippingCost > 0 ? 'Frete' : 'Frete Grátis');

  refreshTotals();
}



/* Abas de pagamento */
export function selectPayTab(id, btn) {
  document.querySelectorAll('.pay-tab').forEach((tab) => tab.classList.remove('active'));
  document.querySelectorAll('.pay-panel').forEach((panel) => panel.classList.remove('active'));

  btn.classList.add('active');

  const panel = document.getElementById('pay-' + id);
  if (panel) panel.classList.add('active');
}


