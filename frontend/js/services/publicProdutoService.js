// services/publicProdutoService.js
import { api } from '../core/api.js';

export const publicProdutoService = {
    listar(filtros = {}) { 
        return api.get('/product', filtros);
    },

    buscar(uuid) {
        return api.get(`/product/uuid/${uuid}`);
    }
};
