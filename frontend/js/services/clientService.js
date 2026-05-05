import { api } from '../core/api.js';

export const clientService = {

    criar(dados) {
        return api.post('/clients', dados);
    },

    atualizaEndereco(dados) {
        return api.post('/client/address', dados);
    },

    login(email, senha) {
        return api.post('/client-login', { email, password });
    },

    loginGoogle(token) {
        return api.post('/clients/google', { token });
    }
};
