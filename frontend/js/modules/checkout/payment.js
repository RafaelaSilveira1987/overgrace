import { notify } from '../../utils/notify.js';
import { confirmarPedido, selectShip, goToStep, selectPayTab } from './utils.js';

const DEMO_CHECKOUT = window.OVERGRACE_DEMO_CHECKOUT === true;
const PUBLIC_KEY = window.OVERGRACE_MP_PUBLIC_KEY || '';
const LOCALE = window.OVERGRACE_MP_LOCALE || 'pt-BR';
const PAYMENT_ENDPOINT = '/overgrace/api/payments/mercadopago';

let cardFormInstance = null;

function getEl(id) {
  return document.getElementById(id);
}

function value(id) {
  return getEl(id)?.value?.trim() || '';
}

function onlyNumbers(text) {
  return String(text || '').replace(/\D/g, '');
}

function formatAmount(value) {
  return Number(value || 0).toFixed(2);
}

function getAmount() {
  return Number(window.checkoutCartState?.total || 0) || 0;
}

function getOrderId() {
  return localStorage.getItem('order_id') || null;
}

function setPaymentStatus(html, ready = false) {
  const status = getEl('mpPaymentStatus');
  if (!status) return;
  status.classList.toggle('ready', Boolean(ready));
  status.innerHTML = html;
}

function fillPayerDefaults() {
  const email = getEl('email')?.value || 'cliente.demo@overgrace.local';
  const cpf = getEl('cpf')?.value || '000.000.000-00';
  const nome = [getEl('nome')?.value, getEl('sobrenome')?.value].filter(Boolean).join(' ') || 'Cliente Demo';

  if (getEl('form-checkout__cardholderEmail') && !value('form-checkout__cardholderEmail')) {
    getEl('form-checkout__cardholderEmail').value = email;
  }

  if (getEl('form-checkout__identificationNumber') && !value('form-checkout__identificationNumber')) {
    getEl('form-checkout__identificationNumber').value = cpf;
  }

  if (getEl('form-checkout__cardholderName') && !value('form-checkout__cardholderName')) {
    getEl('form-checkout__cardholderName').value = nome.toUpperCase();
  }

  updateCardPreview();
}

function updateCardPreview() {
  const name = value('form-checkout__cardholderName');
  const previewName = getEl('cardPreviewName');
  if (previewName) previewName.textContent = name ? name.toUpperCase().slice(0, 28) : 'NOME IMPRESSO';

  const email = value('form-checkout__cardholderEmail');
  const brand = getEl('cardPreviewBrand');
  if (brand && DEMO_CHECKOUT) brand.textContent = email ? 'DEMO' : 'CARD';
}

function setButtonLoading(isLoading) {
  const btn = getEl('form-checkout__submit');
  if (!btn) return;
  btn.disabled = Boolean(isLoading);
  btn.innerHTML = isLoading
    ? 'Processando pagamento...'
    : (DEMO_CHECKOUT ? 'Simular pagamento no cartão <span class="arrow">→</span>' : 'Pagar com cartão <span class="arrow">→</span>');
}

function enableDemoSecureFields() {
  document.querySelectorAll('.mp-secure-field').forEach((field) => {
    field.classList.add('demo-secure-field');
    field.setAttribute('role', 'presentation');
    field.setAttribute('aria-label', field.dataset.demoPlaceholder || 'Campo seguro demonstrativo');
  });
}

async function submitRealCardPayment(formData) {
  const orderId = getOrderId();
  if (!orderId) throw new Error('Pedido não encontrado. Volte para a etapa de entrega e gere o pedido.');

  const payload = {
    order_id: orderId,
    provider: 'mercadopago',
    payment_type: 'credit_card',
    token: formData.token,
    issuer_id: formData.issuerId,
    payment_method_id: formData.paymentMethodId,
    transaction_amount: Number(formData.amount),
    installments: Number(formData.installments || 1),
    description: 'Pedido OverGrace #' + orderId,
    payer: {
      email: formData.cardholderEmail,
      identification: {
        type: formData.identificationType,
        number: onlyNumbers(formData.identificationNumber)
      }
    }
  };

  const response = await fetch(PAYMENT_ENDPOINT, {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json'
    },
    body: JSON.stringify(payload)
  });

  const result = await response.json().catch(() => ({}));

  if (!response.ok) {
    throw new Error(result?.message || result?.error || 'Pagamento não foi processado.');
  }

  return result;
}

function statusFromMercadoPago(result) {
  const status = result?.status || result?.payment?.status || result?.transactions?.payments?.[0]?.status || '';
  const detail = result?.status_detail || result?.payment?.status_detail || result?.transactions?.payments?.[0]?.status_detail || '';

  if (['approved', 'processed'].includes(status) || detail === 'accredited') return 'approved';
  if (['rejected', 'cancelled', 'failed'].includes(status)) return 'rejected';
  return 'pending';
}

function bindDemoSubmit() {
  const form = getEl('form-checkout');
  if (!form) return;

  form.addEventListener('submit', (event) => {
    event.preventDefault();
    setButtonLoading(true);
    setTimeout(() => {
      setButtonLoading(false);
      window.confirmarPedido?.('cartao', 'approved');
    }, 450);
  });
}

function bindPreviewEvents() {
  ['form-checkout__cardholderName', 'form-checkout__cardholderEmail'].forEach((id) => {
    const field = getEl(id);
    if (!field) return;
    field.addEventListener('input', updateCardPreview);
  });
}

function markRealMode() {
  const warning = getEl('realCardWarning');
  if (warning) warning.hidden = false;

  const demoBox = document.querySelector('.card-demo-box');
  if (demoBox) demoBox.hidden = true;
}

function initCardForm() {
  const form = getEl('form-checkout');
  if (!form) return;

  fillPayerDefaults();
  bindPreviewEvents();

  if (DEMO_CHECKOUT) {
    enableDemoSecureFields();
    bindDemoSubmit();
    setPaymentStatus('Modo demonstração ativo: cartão com layout próprio da OverGrace, sem backend e sem dados reais.', true);
    return;
  }

  if (!PUBLIC_KEY) {
    enableDemoSecureFields();
    bindDemoSubmit();
    setPaymentStatus('Configure a <strong>Public Key</strong> do Mercado Pago para ativar os campos seguros reais. Enquanto isso, o cartão fica em prévia visual.', false);
    return;
  }

  if (!window.MercadoPago) {
    enableDemoSecureFields();
    bindDemoSubmit();
    setPaymentStatus('SDK do Mercado Pago não carregou. Confira conexão, bloqueadores ou CDN.', false);
    return;
  }

  markRealMode();
  setPaymentStatus('MercadoPago.js ativo: campos seguros carregados dentro do checkout da OverGrace.', true);

  const mp = new window.MercadoPago(PUBLIC_KEY, { locale: LOCALE });

  cardFormInstance = mp.cardForm({
    amount: formatAmount(getAmount()),
    iframe: true,
    form: {
      id: 'form-checkout',
      cardNumber: {
        id: 'form-checkout__cardNumber',
        placeholder: 'Número do cartão'
      },
      expirationDate: {
        id: 'form-checkout__expirationDate',
        placeholder: 'MM/AA'
      },
      securityCode: {
        id: 'form-checkout__securityCode',
        placeholder: 'CVV'
      },
      cardholderName: {
        id: 'form-checkout__cardholderName',
        placeholder: 'Nome impresso no cartão'
      },
      issuer: {
        id: 'form-checkout__issuer',
        placeholder: 'Banco emissor'
      },
      installments: {
        id: 'form-checkout__installments',
        placeholder: 'Parcelas'
      },
      identificationType: {
        id: 'form-checkout__identificationType',
        placeholder: 'Tipo de documento'
      },
      identificationNumber: {
        id: 'form-checkout__identificationNumber',
        placeholder: 'Número do documento'
      },
      cardholderEmail: {
        id: 'form-checkout__cardholderEmail',
        placeholder: 'E-mail'
      }
    },
    callbacks: {
      onFormMounted: (error) => {
        if (error) {
          console.warn('Erro ao montar CardForm:', error);
          setPaymentStatus('Não consegui montar os campos seguros do Mercado Pago. Veja o console para detalhes.', false);
          return;
        }
        setPaymentStatus('Campos seguros do cartão carregados. Visual OverGrace, tokenização Mercado Pago.', true);
      },
      onSubmit: async (event) => {
        event.preventDefault();
        setButtonLoading(true);

        try {
          const formData = cardFormInstance.getCardFormData();
          const result = await submitRealCardPayment(formData);
          const status = statusFromMercadoPago(result);
          window.confirmarPedido?.('cartao', status);
        } catch (error) {
          notify.error(error?.message || 'Erro ao processar pagamento');
          console.error(error);
        } finally {
          setButtonLoading(false);
        }
      },
      onFetching: () => {
        const progressBar = getEl('cardProgress');
        if (progressBar) progressBar.removeAttribute('value');

        return () => {
          if (progressBar) progressBar.setAttribute('value', '0');
        };
      }
    }
  });
}

document.addEventListener('DOMContentLoaded', initCardForm);

document.getElementById('btnPix')?.addEventListener('click', () => { confirmarPedido('pix', 'pending'); });

document.querySelectorAll('.btnFrete').forEach(btn => {
  btn.addEventListener('click', function () {
    const cost = parseFloat(this.dataset.cost);
    const label = this.dataset.label;

    selectShip(this, cost, label);
  });
});

document.getElementById('voltarFrete')?.addEventListener('click', () => { goToStep(1, true) });

document.querySelectorAll('.voltarPedido').forEach(btn => {
  btn.addEventListener('click', function () {
    goToStep(2, true);
  });
});

document.querySelectorAll('.pay-tab').forEach(btn => {
  btn.addEventListener('click', function () {
    const method = this.dataset.method;

    selectPayTab(method, this);
  });
});


