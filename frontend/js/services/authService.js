import { api } from '../core/api.js';

export async function registerClient(data) {
    return api.post('/register', data);
}

export const authService = {

    async login(email, password) {
        const res = await api.post('/login', { email, password });

        // salva token
        if (res.token) {
            localStorage.setItem('token', res.token);
        }

        if (res.role) {
            localStorage.setItem('role', res.role);
        }

        return res;
    },

    async loginAdmin(email, password) {
        const res = await api.post('/admin-login', { email, password });

        if (res.token) {
            localStorage.setItem('token', res.token);
        }

        if (res.role) {
            localStorage.setItem('role', res.role);
        }

        return res;
    },

    async register(data) {
        return registerClient(data);
    },

    logout() {
        localStorage.removeItem('token');
        localStorage.removeItem('role');
    },

    getUser() {
        return api.get('/me');
    }
};
