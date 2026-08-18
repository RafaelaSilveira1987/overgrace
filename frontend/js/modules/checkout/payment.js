import { notify } from '../../utils/notify.js';
import {
  criarPagamento,
  selectShip,
  goToStep,
  selectPayTab,
  maskCEP,
  maskCPF,
  fetchCEP
} from './utils.js';

function hasOrder() {
  return Boolean(localStorage.getItem('order_id'));
}


document.getElementById('btnPix')?.addEventListener('click', (event) => {
  if (!hasOrder()) {
    notify.warning('Conclua os dados e a entrega antes de gerar o PIX.');
    return;
  }
  criarPagamento(event, 'pix');
});
 
document.getElementById('btnBoleto')?.addEventListener('click', (event) => {
  if (!hasOrder()) {
    notify.warning('Conclua os dados e a entrega antes de gerar o BOLETO.');
    return;
  }
  criarPagamento(event, 'boleto');
});

document.getElementById('btnCartao')?.addEventListener('click', (event) => {
  if (!hasOrder()) {
    notify.warning('Conclua os dados e a entrega antes pagar com CARTAO.');
    return;
  }
  criarPagamento(event, 'credit_card');
});

document.querySelectorAll('.btnFrete').forEach((btn) => {
  btn.addEventListener('click', function () {
    selectShip(this, Number.parseFloat(this.dataset.cost || '0'), this.dataset.label || '');
  });
});

document.getElementById('voltarFrete')?.addEventListener('click', () => goToStep(1, true));
document.querySelectorAll('.voltarPedido').forEach((btn) => {
  btn.addEventListener('click', () => goToStep(2, true));
});

function preencherCartaoTeste() {

  document.getElementById('form-checkout__cardNumber').value =
    '5480 8328 0103 3311';

  document.getElementById('form-checkout__expirationDate').value =
    '11/30';

  document.getElementById('form-checkout__securityCode').value =
    '123';

  document.getElementById('form-checkout__cardholderName').value =
    'APRO';

  document.getElementById('form-checkout__cardholderEmail').value =
    'test@testuser.com';

  document.getElementById('form-checkout__identificationType').value =
    'CPF';

  document.getElementById('form-checkout__identificationNumber').value =
    '123.456.789-09';
}

document.querySelectorAll('.pay-tab').forEach((btn) => {
  btn.addEventListener('click', function () {
    if (this.dataset.method === 'pix') selectPayTab('pix', this);
    if (this.dataset.method === 'boleto') selectPayTab('boleto', this);
    if (this.dataset.method === 'cartao'){
      
      selectPayTab('cartao', this);

      preencherCartaoTeste();
    } 
      
  });
});

const formRegister = document.getElementById('formRegister');
formRegister?.querySelector('#cpf')?.addEventListener('input', (event) => maskCPF(event.target));
formRegister?.querySelector('#cep')?.addEventListener('input', (event) => maskCEP(event.target));
formRegister?.querySelector('#cep')?.addEventListener('blur', (event) => fetchCEP(event.target.value));
