import { getHeaders } from './config.js';

export async function request(url, options = {}) {

    const isFormData = options.body instanceof FormData;

    const headers = isFormData
        ? {
            ...getHeaders(true) // 🔥 AGORA SIM SEM Content-Type
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

    try {
        const response = await fetch(url, config);

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
