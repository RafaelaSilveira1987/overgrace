import { getHeaders } from './config.js';
import { BASE_URL } from './config.js';

async function refreshAccessToken() {

    const refreshToken =
        localStorage.getItem('refresh_token') ||
        localStorage.getItem('refresh_token_client');

    if (!refreshToken) {
        throw new Error('Sem refresh token');
    }

    const response = await fetch(BASE_URL + '/refresh', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            refresh_token: refreshToken
        })
    });

    if (!response.ok) {
        throw new Error('Refresh expirado');
    }

    const data = await response.json();

    // cliente
    if (localStorage.getItem('refresh_token_client')) {

        localStorage.setItem('token_client', data.token);

    } else {

        // admin
        localStorage.setItem('token', data.token);
    }

    return data.token;
}

export async function request(url, options = {}) {

    const makeRequest = async () => {

        const isFormData = options.body instanceof FormData;

        const headers = isFormData
            ? {
                ...getHeaders(true)
            }
            : {
                ...getHeaders(),
                ...(options.headers || {})
            };

        const config = {
            method: 'GET',
            ...options,
            headers
        };

        return fetch(url, config);
    };

    try {

        let response = await makeRequest();

        // token expirou
        if (response.status === 401) {

            try {

                await refreshAccessToken();

                // tenta novamente automaticamente
                response = await makeRequest();

            } catch (refreshError) {

                // logout total
                localStorage.removeItem('token');
                localStorage.removeItem('token_client');

                localStorage.removeItem('refresh_token');
                localStorage.removeItem('refresh_token_client');

                throw refreshError;
            }
        }

        let data;

        const contentType = response.headers.get('content-type');

        if (contentType && contentType.includes('application/json')) {
            data = await response.json();
        } else {
            data = await response.text();
        }

        if (!response.ok) {
            throw {
                status: response.status,
                message: data?.message || 'Erro na requisição',
                data
            };
        }

        return data;

    } catch (error) {

        console.error('[HTTP ERROR]', error);

        throw error;
    }
}