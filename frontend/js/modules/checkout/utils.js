import { notify } from '../../utils/notify.js';
import { orderService } from '../../services/orderService.js';
import { paymentService } from '../../services/paymentService.js';


async function criarPagamento(method = "pix") {

  try {

    const orderId = localStorage.getItem('order_id');

    // cria pedido
    const order = await orderService.get(orderId);

    // cria pagamento
    const payment = await paymentService.criar({
      orderId,
      method
    });

    mostrarResultado(payment);

  } catch (e) {

    notify.error(e.message);

  }

}


function mostrarResultado(payment) {

  const checkoutLayout =
    document.getElementById("checkoutLayout");

  const stepsBar =
    document.getElementById("stepsBar");

  const confirmScreen =
    document.getElementById("confirmScreen");

  checkoutLayout.style.display = "none";
  stepsBar.style.display = "none";

  confirmScreen.classList.add("active");

  document.getElementById("confirmOrderNum")
    .textContent = "#OVG-" + payment.order_id;

  document.getElementById("confirmTotal")
    .textContent = formatBRL(payment.amount);

  switch (payment.status) {

    case "approved":

      telaAprovado(payment);

      break;

    case "pending":

      telaPix(payment);

      break;

    case "rejected":

      telaRecusado(payment);

      break;

  }

}

async function acompanharPagamento(id) {

  const payment =
    await paymentService.get(id);

  switch (payment.status) {

    case "approved":

      telaAprovado(payment);

      return;

    case "rejected":

      telaRecusado(payment);

      return;

  }

}

let paymentInterval;

function iniciarPolling(id) {

  clearInterval(paymentInterval);

  paymentInterval = setInterval(async () => {

    const payment =
      await paymentService.get(id);

    if (payment.status == "approved") {

      clearInterval(paymentInterval);

      telaAprovado(payment);

    }

    if (payment.status == "rejected") {

      clearInterval(paymentInterval);

      telaRecusado(payment);

    }

  }, 5000);

}

export async function confirmarPedido() {
  await criarPagamento("pix");
}

/* Máscaras */
function maskCPF(el) {
  let value = el.value.replace(/\D/g, '').slice(0, 11);

  if (value.length > 9) value = value.replace(/(\d{3})(\d{3})(\d{3})(\d{1,2})/, '$1.$2.$3-$4');
  else if (value.length > 6) value = value.replace(/(\d{3})(\d{3})(\d{1,3})/, '$1.$2.$3');
  else if (value.length > 3) value = value.replace(/(\d{3})(\d{1,3})/, '$1.$2');

  el.value = value;
}

function maskPhone(el) {
  let value = el.value.replace(/\D/g, '').slice(0, 11);

  if (value.length > 10) value = value.replace(/(\d{2})(\d{5})(\d{4})/, '($1) $2-$3');
  else if (value.length > 6) value = value.replace(/(\d{2})(\d{4,5})(\d{0,4})/, '($1) $2-$3');
  else if (value.length > 2) value = value.replace(/(\d{2})(\d+)/, '($1) $2');

  el.value = value;
}

function maskCEP(el) {
  let value = el.value.replace(/\D/g, '').slice(0, 8);
  if (value.length > 5) value = value.replace(/(\d{5})(\d{1,3})/, '$1-$2');
  el.value = value;
}

async function fetchCEP(cep) {
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
function selectPayTab(id, btn) {
  document.querySelectorAll('.pay-tab').forEach((tab) => tab.classList.remove('active'));
  document.querySelectorAll('.pay-panel').forEach((panel) => panel.classList.remove('active'));

  btn.classList.add('active');

  const panel = document.getElementById('pay-' + id);
  if (panel) panel.classList.add('active');
}