import { BASE_URL } from './config.js';

const PUBLIC_AUTH_PATHS = [
    '/client-login',
    '/admin-login',
    '/register',
    '/refresh'
]

function normalizePath(url) {
    try {
        return new URL(url, window.location.origin).pathname;
    } catch {
        return String(url || '');
    }
}

function isPublicAuthRequest(url) {
    const pathname = normalizePath(url);
    return PUBLIC_AUTH_PATHS.some(path => pathname.endsWith(`/api${path}`) || pathname.endsWith(path));
}

async function refreshAccessToken() {
    const clientRefreshToken = localStorage.getItem('refresh_token_client');
    const adminRefreshToken = localStorage.getItem('refresh_token');
    const refreshToken = clientRefreshToken || adminRefreshToken;

    if (!refreshToken) {
        throw new Error('Sessão expirada. Faça login novamente.');
    }

    const response = await fetch(BASE_URL + '/refresh', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ refresh_token: refreshToken })
    });

    let data = {};
    const contentType = response.headers.get('content-type') || '';
    if (contentType.includes('application/json')) {
        data = await response.json();
    }

    if (!response.ok || !data.token) {
        throw new Error(data.message || data.error || 'Sessão expirada. Faça login novamente.');
    }

    if (clientRefreshToken) {
        localStorage.setItem('token_client', data.token);
    } else {
        localStorage.setItem('token', data.token);
    }

    return data.token;
}

export async function request(url, options = {}) {
    const makeRequest = async (forcedToken = null) => {
        const isFormData = options.body instanceof FormData;
        const headers = { ...(options.headers || {}) };
        const storedToken = localStorage.getItem('token') || localStorage.getItem('token_client');
        const token = forcedToken || storedToken;

        if (token) headers.Authorization = `Bearer ${token}`;
        if (!isFormData && !headers['Content-Type']) headers['Content-Type'] = 'application/json';

        return fetch(url, {
            method: 'GET',
            ...options,
            headers
        });
    };

    try {
        const hadAccessToken = Boolean(localStorage.getItem('token') || localStorage.getItem('token_client'));
        let response = await makeRequest();

        // Nunca tenta renovar token em login, cadastro ou no próprio refresh.
        // Também não tenta renovar quando a requisição começou sem sessão.
        if (response.status === 401 && hadAccessToken && !isPublicAuthRequest(url)) {
            try {
                const newToken = await refreshAccessToken();
                response = await makeRequest(newToken);
            } catch (refreshError) {
                ['token', 'token_client', 'refresh_token', 'refresh_token_client', 'role']
                    .forEach(key => localStorage.removeItem(key));
                throw refreshError;
            }
        }

        const contentType = response.headers.get('content-type') || '';
        const rawBody = await response.text();
        let data = rawBody;

        if (contentType.includes('application/json') || /^[\s]*[\[{]/.test(rawBody)) {
            try {
                data = rawBody ? JSON.parse(rawBody) : {};
            } catch {
                data = rawBody;
            }
        }

        if (!response.ok) {
            const htmlError = typeof data === 'string' && /<[^>]+>/.test(data);
            throw {
                status: response.status,
                message: htmlError
                    ? 'O servidor retornou um erro interno. Consulte o log do backend.'
                    : (data?.message || data?.error || 'Erro na requisição'),
                data
            };
        }

        return data;
    } catch (error) {
        console.error('[HTTP ERROR]', error);
        throw error;
    }
}
