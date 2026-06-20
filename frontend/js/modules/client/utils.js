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
function goToStep(n, force = false) {
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
function selectShip(option, cost = 0, label = 'Frete Grátis') {
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

/* Tela final / demonstração sem backend */
let lastDemoPaymentMethod = 'pix';
let lastDemoOrderNumber = null;

function getOrderNumber() {
  const storedOrderId = localStorage.getItem('order_id');
  if (storedOrderId) return String(storedOrderId).replace(/^#?OVG-?/i, '');

  if (!lastDemoOrderNumber) {
    lastDemoOrderNumber = String(Math.floor(Math.random() * 99999)).padStart(5, '0');
    localStorage.setItem('order_id', lastDemoOrderNumber);
  }

  return lastDemoOrderNumber;
}

function methodLabel(method) {
  const labels = {
    pix: 'PIX',
    boleto: 'Boleto',
    cartao: 'Cartão de crédito'
  };

  return labels[method] || 'Pagamento';
}

function getTotalLabel() {
  if (window.checkoutCartState?.total !== undefined) {
    return formatBRL(window.checkoutCartState.total);
  }

  return document.getElementById('total-items-final')?.textContent || formatBRL(0);
}

function hideNextCards() {
  ['pixNextCard', 'boletoNextCard', 'analysisNextCard'].forEach((id) => {
    const card = document.getElementById(id);
    if (card) card.hidden = true;
  });
}

function setResultStatus(status = 'approved', method = lastDemoPaymentMethod) {
  const confirmScreen = document.getElementById('confirmScreen');
  const confirmIcon = document.getElementById('confirmIcon');
  const confirmEyebrow = document.getElementById('confirmEyebrow');
  const confirmTitle = document.getElementById('confirmTitle');
  const confirmSub = document.getElementById('confirmSub');
  const confirmPaymentMethod = document.getElementById('confirmPaymentMethod');

  if (!confirmScreen) return;

  confirmScreen.classList.remove('approved', 'pending', 'rejected');
  confirmScreen.classList.add(status);
  hideNextCards();

  if (confirmPaymentMethod) confirmPaymentMethod.textContent = methodLabel(method);

  if (status === 'approved') {
    if (confirmIcon) confirmIcon.textContent = '✓';
    if (confirmEyebrow) confirmEyebrow.textContent = 'Pagamento aprovado';
    if (confirmTitle) confirmTitle.innerHTML = 'Pedido <em>confirmado!</em>';
    if (confirmSub) confirmSub.textContent = 'Pagamento aprovado na simulação. Na integração real, essa tela aparece quando o Mercado Pago devolver status aprovado.';
    return;
  }

  if (status === 'rejected') {
    if (confirmIcon) confirmIcon.textContent = '!';
    if (confirmEyebrow) confirmEyebrow.textContent = 'Pagamento recusado';
    if (confirmTitle) confirmTitle.innerHTML = 'Ops, pagamento <em>não aprovado</em>';
    if (confirmSub) confirmSub.textContent = 'Essa é a tela para cartão recusado ou pagamento negado. O ideal é oferecer outra forma de pagamento sem assustar a cliente.';
    return;
  }

  if (confirmIcon) confirmIcon.textContent = '…';
  if (confirmEyebrow) confirmEyebrow.textContent = 'Pedido criado';

  if (method === 'pix') {
    if (confirmTitle) confirmTitle.innerHTML = 'PIX <em>gerado</em>';
    if (confirmSub) confirmSub.textContent = 'Agora a cliente copia o código ou lê o QR Code. O pedido fica aguardando pagamento.';
    const pixCard = document.getElementById('pixNextCard');
    if (pixCard) pixCard.hidden = false;
    return;
  }

  if (method === 'boleto') {
    if (confirmTitle) confirmTitle.innerHTML = 'Boleto <em>gerado</em>';
    if (confirmSub) confirmSub.textContent = 'A cliente recebe a linha digitável. O pedido fica pendente até compensar.';
    const boletoCard = document.getElementById('boletoNextCard');
    if (boletoCard) boletoCard.hidden = false;
    return;
  }

  if (confirmTitle) confirmTitle.innerHTML = 'Pagamento <em>em análise</em>';
  if (confirmSub) confirmSub.textContent = 'Essa é a tela para cartão pendente de análise. Nada de pânico: só informar que a confirmação pode levar alguns minutos.';
  const analysisCard = document.getElementById('analysisNextCard');
  if (analysisCard) analysisCard.hidden = false;
}

function setBoletoDate() {
  const el = document.getElementById('boletoDueDate');
  if (!el) return;

  const due = new Date();
  due.setDate(due.getDate() + 3);
  el.textContent = due.toLocaleDateString('pt-BR');
}

function confirmarPedido(method = 'pix', status = 'approved') {
  lastDemoPaymentMethod = method;

  const checkoutLayout = document.getElementById('checkoutLayout');
  const stepsBar = document.getElementById('stepsBar');
  const confirmScreen = document.getElementById('confirmScreen');
  const confirmOrderNum = document.getElementById('confirmOrderNum');
  const confirmTotal = document.getElementById('confirmTotal');

  if (checkoutLayout) checkoutLayout.style.display = 'none';
  if (stepsBar) stepsBar.style.display = 'none';

  const number = getOrderNumber();
  if (confirmOrderNum) confirmOrderNum.textContent = '#OVG-' + number;
  if (confirmTotal) confirmTotal.textContent = getTotalLabel();
  if (confirmScreen) confirmScreen.classList.add('active');

  setBoletoDate();
  setResultStatus(status, method);

  localStorage.setItem('checkout_demo_last_status', status);
  localStorage.setItem('checkout_demo_last_method', method);

  window.scrollTo({ top: 0, behavior: 'smooth' });
}

function simularStatus(status) {
  setResultStatus(status, lastDemoPaymentMethod);
  localStorage.setItem('checkout_demo_last_status', status);
  window.scrollTo({ top: 0, behavior: 'smooth' });
}

function voltarCheckoutDemo() {
  const checkoutLayout = document.getElementById('checkoutLayout');
  const stepsBar = document.getElementById('stepsBar');
  const confirmScreen = document.getElementById('confirmScreen');

  if (checkoutLayout) checkoutLayout.style.display = '';
  if (stepsBar) stepsBar.style.display = '';
  if (confirmScreen) {
    confirmScreen.classList.remove('active', 'approved', 'pending', 'rejected');
  }

  goToStep(3, true);
}

function copyTextFromElement(id, event, successText) {
  const code = document.getElementById(id)?.textContent?.trim();
  if (!code) return;

  navigator.clipboard.writeText(code).then(() => {
    const btn = event?.target;
    if (!btn) return;

    const original = btn.textContent;
    btn.textContent = successText || '✓ Copiado!';
    setTimeout(() => { btn.textContent = original || 'Copiar código'; }, 2000);
  });
}

function copyPix(event) {
  copyTextFromElement('pixCode', event, '✓ PIX copiado!');
}

function copyBoleto(event) {
  copyTextFromElement('boletoCode', event, '✓ Linha copiada!');
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

/* Preparação leve do Mercado Pago Brick */
async function initMercadoPagoBrick() {
  const status = document.getElementById('mpPaymentStatus');
  const container = document.getElementById('paymentBrick_container');
  const publicKey = window.OVERGRACE_MP_PUBLIC_KEY;

  if (!status || !container) return;

  if (window.OVERGRACE_DEMO_CHECKOUT === true) {
    status.classList.add('ready');
    status.innerHTML = 'Modo demonstração ativo: navegue por PIX, boleto e cartão sem backend. Quando for integrar de verdade, troque para false.';
    return;
  }

  if (!publicKey) {
    status.classList.remove('ready');
    status.innerHTML = 'Configure a <strong>Public Key</strong> do Mercado Pago no topo desta página para renderizar o Brick real.';
    return;
  }

  if (!window.MercadoPago) {
    status.innerHTML = 'SDK do Mercado Pago não carregou. Confira conexão e bloqueadores do navegador.';
    return;
  }

  status.classList.add('ready');
  status.innerHTML = 'Mercado Pago detectado. Agora falta ligar o endpoint backend que cria o pagamento.';

  /*
    Aqui entra o Brick real. Mantive comentado para não quebrar enquanto o backend /api/payments ainda não existir.

    const mp = new MercadoPago(publicKey, { locale: window.OVERGRACE_MP_LOCALE || 'pt-BR' });
    const bricksBuilder = mp.bricks();

    await bricksBuilder.create('payment', 'paymentBrick_container', {
      initialization: {
        amount: Number(window.checkoutCartState?.total || 0)
      },
      callbacks: {
        onReady: () => {},
        onError: (error) => console.error(error),
        onSubmit: ({ formData }) => {
          return fetch('/overgrace/api/payments/mercadopago', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
              order_id: localStorage.getItem('order_id'),
              formData
            })
          });
        }
      }
    });
  */
}

document.addEventListener('DOMContentLoaded', () => {
  ['endereco', 'numero', 'bairro', 'cidade', 'estado', 'cep'].forEach((id) => {
    const field = document.getElementById(id);
    if (!field) return;
    field.addEventListener('input', () => {
      updateAddressPreview();
      updateDeliveryResume();
    });
  });

  updateAddressPreview();
  refreshTotals();
  initMercadoPagoBrick();
});
