import { api } from '../core/api.js?v=11';

function saveSession(response, defaultRole = 'client') {
  if (!response?.token) throw new Error('Token não retornado pelo servidor.');
  const role = response.role || defaultRole;
  const isAdmin = role === 'admin';
  ['token','token_client','refresh_token','refresh_token_client','role'].forEach((key) => localStorage.removeItem(key));
  localStorage.setItem(isAdmin ? 'token' : 'token_client', response.token);
  if (response.refresh_token) {
    localStorage.setItem(isAdmin ? 'refresh_token' : 'refresh_token_client', response.refresh_token);
  }
  localStorage.setItem('role', role);
  return { ...response, role };
}

export async function registerClient(data) {
  return api.post('/register', data);
}

export const authService = {
  async loginClient(email, password) {
    return saveSession(await api.post('/client-login', { email, password }), 'client');
  },
  async loginAdmin(email, password) {
    return saveSession(await api.post('/admin-login', { email, password }), 'admin');
  },
  async login(email, password) {
    try {
      return await this.loginClient(email, password);
    } catch (clientError) {
      const status = Number(clientError?.status || 0);
      if (![401, 403].includes(status)) throw clientError;
      try {
        return await this.loginAdmin(email, password);
      } catch (adminError) {
        if ([401, 403].includes(Number(adminError?.status || 0))) {
          throw new Error('E-mail ou senha inválidos.');
        }
        throw adminError;
      }
    }
  },
  async register(data) { return registerClient(data); },
  logout() {
    ['token','token_client','refresh_token','refresh_token_client','role','order_id','checkout_payment_id','checkout_customer']
      .forEach((key) => localStorage.removeItem(key));
  },
  getRole() { return localStorage.getItem('role'); },
  isAdmin() { return this.getRole() === 'admin' && Boolean(localStorage.getItem('token')); },
  isClient() { return this.getRole() === 'client' && Boolean(localStorage.getItem('token_client')); },
  getUser() { return api.get('/me'); }
};
