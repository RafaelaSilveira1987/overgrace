import { api } from '../core/api.js';

export const siteContentService = {
    listarPublico() {
        return api.get('/site-content/public');
    },

    listarAdmin() {
        return api.get('/site-content');
    },

    salvar(dados) {
        return api.post('/site-content', dados);
    }
};
