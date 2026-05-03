import { authService } from '../../services/authService.js?v=7';

const loginLink = document.getElementById('loginLink');
const accountLink = document.getElementById('accountLink');
const logoutButton = document.getElementById('logoutButton');
const customerSummary = document.getElementById('customerSummary');
const customerName = document.getElementById('customerName');
const customerEmail = document.getElementById('customerEmail');

function resetHeader() {
    if (loginLink) loginLink.hidden = false;
    if (accountLink) accountLink.hidden = true;
    if (logoutButton) logoutButton.hidden = true;
    if (customerSummary) customerSummary.hidden = true;
}

function setHeader(user) {
    if (user.role !== 'client') {
        resetHeader();
        return;
    }

    if (loginLink) loginLink.hidden = true;
    if (accountLink) accountLink.hidden = false;
    if (logoutButton) logoutButton.hidden = false;
    if (customerSummary) customerSummary.hidden = false;

    const displayName = user.name || user.email;

    if (customerName) customerName.textContent = `Ola, ${displayName.split(' ')[0]}`;
    if (customerEmail) customerEmail.textContent = user.email;
}

async function hydrateHeader() {
    const token = localStorage.getItem('token');
    const role = localStorage.getItem('role');

    if (!token || role !== 'client') {
        resetHeader();
        return;
    }

    try {
        const user = await authService.getUser();
        setHeader(user);
    } catch (err) {
        authService.logout();
        resetHeader();
    }
}

if (logoutButton) {
    logoutButton.addEventListener('click', () => {
        authService.logout();
        resetHeader();
        window.location.href = '/overgrace/';
    });
}

hydrateHeader();
