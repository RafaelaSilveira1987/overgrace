import { api } from '../core/api.js';

export const orderService = {
  criar(dados = {}) {
    return api.post('/orders', dados);
  }, 

  listar(filtros = {}) {
    return api.get('/orders-list', filtros);
  },

  listarPedidosCliente(filtros = {}) {
    console.log("entrou no service");
    return api.get('/orders-client', filtros);
  },

  listarDash(filtros = {}) {
    return api.get('/orders-dash', filtros);
  },

  get(id) {
    return api.get(`/orders/${id}`);
  },

  getPaymentOrder(order_id, filtros = {}) {
    return api.get(`/orders-payment/${order_id}`, filtros);
  },
};
