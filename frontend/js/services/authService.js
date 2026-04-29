import { api } from '../core/api.js';

export const authService = {

    async login(email, password) {
        const res = await api.post('/login', { email, password });

        // salva token
        if (res.token) {
            localStorage.setItem('token', res.token);
        }

        return res;
    },

    logout() {
        localStorage.removeItem('token');
    },

    getUser() {
        return api.get('/auth/me');
    }
};
