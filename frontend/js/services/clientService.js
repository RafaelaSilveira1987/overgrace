import { api } from '../core/api.js';

export const clientService = {

    criar(dados) {
        return api.post('/clients', dados);
    },

    login(email, senha) {
        return api.post('/clients/login', { email, senha });
    },

    loginGoogle(token) {
        return api.post('/clients/google', { token });
    }
};
