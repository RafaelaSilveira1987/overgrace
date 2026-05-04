export const BASE_URL = '/overgrace/api';

export function getHeaders(isFormData = false) {
    const token =
        localStorage.getItem('token') ||           // token adm
        localStorage.getItem('token_client');      // token client

    const headers = {};

    if (token) {
        headers.Authorization = `Bearer ${token}`;
    }

    if (!isFormData) {
        headers['Content-Type'] = 'application/json';
    }

    return headers;
}


