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

document.addEventListener('DOMContentLoaded', () => {
  // O backend desta versão possui integração real somente com PIX.
  document.querySelectorAll('.pay-tab').forEach((tab) => {
    const method = tab.dataset.method;
    const enabled = method === 'pix';
    tab.hidden = !enabled;
    tab.disabled = !enabled;
    tab.classList.toggle('active', enabled);
  });

  document.querySelectorAll('.pay-panel').forEach((panel) => {
    const enabled = panel.id === 'pay-pix';
    panel.hidden = !enabled;
    panel.classList.toggle('active', enabled);
  });
});

document.getElementById('btnPix')?.addEventListener('click', () => {
  if (!hasOrder()) {
    notify.warning('Conclua os dados e a entrega antes de gerar o PIX.');
    return;
  }
  criarPagamento('pix');
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

document.querySelectorAll('.pay-tab').forEach((btn) => {
  btn.addEventListener('click', function () {
    if (this.dataset.method === 'pix') selectPayTab('pix', this);
  });
});

const formRegister = document.getElementById('formRegister');
formRegister?.querySelector('#cpf')?.addEventListener('input', (event) => maskCPF(event.target));
formRegister?.querySelector('#cep')?.addEventListener('input', (event) => maskCEP(event.target));
formRegister?.querySelector('#cep')?.addEventListener('blur', (event) => fetchCEP(event.target.value));
