import { api } from '../core/api.js';

export const clientService = {

    criar(dados) {
        return api.post('/clients', dados);
    },

    atualizaEndereco(dados) {
        return api.post('/client/address', dados);
    },

    login({ email, password }) {
        return api.post('/client-login', { email, password });
    },

    me() {
        return api.get('/me');
    },

    loginGoogle(token) {
        return api.post('/clients/google', { token });
    },

    listar(filtros = {}) {
        return api.get('/clients-list', filtros);
    },
};
