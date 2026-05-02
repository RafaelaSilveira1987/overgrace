import { request } from './http.js';
import { BASE_URL } from './config.js';

export const api = {

    get: (url, params = {}) => {
        let query = '';

        if (params && Object.keys(params).length > 0) {
            const queryString = new URLSearchParams(params).toString();
            query = `?${queryString}`;
        }

        return request(BASE_URL + url + query);
    },

    post: (url, body) => {
        
        const isFormData = body instanceof FormData;

        return request(BASE_URL + url, {
            method: 'POST',
            body: isFormData ? body : JSON.stringify(body),
            headers: isFormData
                ? {} // 🔥 deixa o browser setar multipart automaticamente
                : { 'Content-Type': 'application/json' }
        });
    },


    put: (url, body) => {
        const isFormData = body instanceof FormData;

        return request(BASE_URL + url, {
            method: 'PUT',
            body: isFormData ? body : JSON.stringify(body),
            headers: isFormData
                ? {}
                : { 'Content-Type': 'application/json' }
        });
    },


    delete: (url) => request(BASE_URL + url, {
        method: 'DELETE'
    })
};

