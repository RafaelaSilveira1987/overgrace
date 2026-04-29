// services/publicProdutoService.js
import { api } from '../core/api.js';

export const publicProdutoService = {
    listar(filtros = {}) { 
        return api.get('/product', filtros);
    },

    buscar(id) {
        return api.get(`/products/public/${id}`);
    }
};
