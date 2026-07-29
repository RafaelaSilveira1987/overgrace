import { clientService } from '../../services/clientService.js';
import { orderService } from '../../services/orderService.js';
import { notify } from '../../utils/notify.js';
import { marcarErro } from '../../utils/validateUI.js';
import { goToStep } from '../checkout/utils.js';

function getEl(id) {
  return document.getElementById(id);
}

function value(id) {
  return getEl(id)?.value?.trim() || '';
}

function markRequired(ids) {
  let hasError = false;

  ids.forEach((id) => {
    const el = getEl(id);
    if (!el || !el.value.trim()) {
      marcarErro(el);
      hasError = true;
    }
  });

  return hasError;
}

function saveAuth(auth) {
  if (!auth?.token) throw new Error('Login não retornou token');

  localStorage.setItem('token_client', auth.token);
  if (auth.refresh_token) localStorage.setItem('refresh_token_client', auth.refresh_token);
  if (auth.role) localStorage.setItem('role', auth.role);
}

function saveDemoAuth() {
  localStorage.setItem('token_client', 'demo-checkout-token');
  localStorage.setItem('role', 'client-demo');
}

function createDemoOrderId() {
  const current = localStorage.getItem('order_id');
  if (current) return current;

  const id = String(Math.floor(Math.random() * 99999)).padStart(5, '0');
  localStorage.setItem('order_id', id);
  return id;
}

function fillDemoFields() {
  const demo = {
    nome: 'Cliente',
    sobrenome: 'Demo',
    email: 'cliente.demo@overgrace.local',
    password: '123456',
    cpf: '000.000.000-00',
    tel: '(11) 99999-9999',
    cep: '01001-000',
    endereco: 'Praça da Sé',
    numero: '100',
    bairro: 'Sé',
    cidade: 'São Paulo',
    estado: 'SP'
  };

  Object.entries(demo).forEach(([id, val]) => {
    const el = getEl(id);
    if (el && !el.value) el.value = val;
  });

  window.updateAddressPreview?.();
  window.updateDeliveryResume?.();
}


function extractOrderId(response) {
  return (
    response?.order_id?.order_id ||
    response?.order_id?.id ||
    response?.order_id ||
    response?.id ||
    response?.order?.id ||
    null
  );
}

function getCheckoutPayload() {
  return {
    cliente: {
      nome: value('nome'),
      sobrenome: value('sobrenome'),
      email: value('email'),
      cpf: value('cpf'),
      telefone: value('tel')
    },
    entrega: {
      cep: value('cep'),
      endereco: value('endereco'),
      numero: value('numero'),
      complemento: value('comp') || null,
      bairro: value('bairro'),
      cidade: value('cidade'),
      estado: value('estado')
    }
  };
}

async function loginOrCreateClient() {
  const credentials = {
    email: value('email'),
    password: value('password')
  };

  try {
    return await clientService.login(credentials);
  } catch (loginError) {
    const dados = {
      ...getCheckoutPayload().cliente,
      password: credentials.password,
      telefone: value('tel'),
      ...getCheckoutPayload().entrega
    };

    await clientService.criar(dados);
    return await clientService.login(credentials);
  }
}

const formRegister = getEl('formRegister');
if (formRegister) {
  formRegister.addEventListener('submit', async (event) => {
    event.preventDefault();

    const required = [
      'nome',
      'sobrenome',
      'email',
      'password',
      'cpf',
      'tel',
      'cep',
      'endereco',
      'numero',
      'bairro',
      'cidade',
      'estado'];

    try {
      if (markRequired(required)) throw new Error('Preencha os campos obrigatórios');

      const submitButton = formRegister.querySelector('.submit-btn');
      if (submitButton) {
        submitButton.disabled = true;
        submitButton.innerHTML = 'Validando dados...';
      }

      /*
      if (DEMO_CHECKOUT) {
        saveDemoAuth();
        localStorage.setItem('checkout_customer', JSON.stringify(getCheckoutPayload()));
        notify.success('Dados confirmados no modo demonstração');
        window.goToStep?.(2, true);
        return;
      }*/

      const auth = await loginOrCreateClient();
      saveAuth(auth);

      localStorage.setItem('checkout_customer', JSON.stringify(getCheckoutPayload()));
      notify.success('Dados confirmados');

      window.goToStep?.(2, true);
    } catch (error) {
      notify.error(error?.message || 'Erro ao continuar');
      console.error(error);
    } finally {
      const submitButton = formRegister.querySelector('.submit-btn');
      if (submitButton) {
        submitButton.disabled = false;
        submitButton.innerHTML = 'Continuar para entrega <span class="arrow">→</span>';
      }
    }
  });
}

const formShipping = getEl('formShipping');
if (formShipping) {
  formShipping.addEventListener('submit', async (event) => {
    event.preventDefault();

    const submitButton = formShipping.querySelector('.submit-btn');

    try {
      if (submitButton) {
        submitButton.disabled = true;
        submitButton.innerHTML = 'Criando pedido...';
      }

      const selectedShipping = document.querySelector('input[name="ship"]:checked');
      if (!selectedShipping) throw new Error('Escolha uma forma de entrega');

      const shipOption = selectedShipping.closest('.ship-option');
      const shippingType = selectedShipping.value;
      const shippingValue = Number(shipOption?.dataset?.cost || 0);
      const shippingLabel = shipOption?.dataset?.label || shippingType;

      if (!localStorage.getItem('token')) {
        const auth = await clientService.login({
          email: value('email'),
          password: value('password')
        });
        saveAuth(auth);
      }

      const order = await orderService.criar({
        shipping_tipo: shippingType,
        shipping_label: shippingLabel,
        shipping_valor: shippingValue,
        checkout: getCheckoutPayload()
      });

      const orderId = extractOrderId(order);
      if (orderId) localStorage.setItem('order_id', orderId);

      notify.success('Pedido criado. Falta só o pagamento.');
      goToStep(3, true);
    } catch (error) {
      notify.error(error?.message || 'Erro ao continuar');
      console.error(error);
    } finally {
      if (submitButton) {
        submitButton.disabled = false;
        submitButton.innerHTML = 'Continuar para pagamento <span class="arrow">→</span>';
      }
    }
  });
}

document.addEventListener('DOMContentLoaded', async () => {

  const token = localStorage.getItem('token');
  const orderId = localStorage.getItem('order_id');

  console.log("token:", token);

  if (!token) {
    return;
  }

  try {

    await clientService.me();

    if (orderId) {
      try {
        const order = await orderService.get(orderId);

        if (order?.status === 'pending') {
          goToStep(3, true);
          return;
        }

        localStorage.removeItem('order_id');

      } catch (error) {
        console.warn("Pedido salvo não localizado:", error);
      }
    }

    goToStep(2, true);

  } catch (error) {

    localStorage.removeItem("token_client");
    localStorage.removeItem("refresh_token_client");

    console.warn("Sessão do cliente expirada:", error);

  }

});
