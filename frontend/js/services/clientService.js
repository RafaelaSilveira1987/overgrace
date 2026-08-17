import { api } from '../core/api.js';

export const clientService = {
  criar(dados) { return api.post('/clients', dados); },
  login({ email, password }) { return api.post('/client-login', { email, password }); },
  me() { return api.get('/me'); },
  listar(filtros = {}) { return api.get('/clients-list', filtros); },
  buscar(id) { return api.get(`/clients/${id}`); },
  atualizar(id, dados) { return api.post(`/clients/${id}`, dados); }
};
