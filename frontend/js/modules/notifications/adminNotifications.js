const base = window.APP_BASE_PATH || '';
const token = localStorage.getItem('token');
const list = document.getElementById('notificationList');
const unreadCount = document.getElementById('unreadCount');
let currentFilter = 'all';

async function request(path, options = {}) {
  const response = await fetch(`${base}/api${path}`, {
    ...options,
    headers: {
      'Content-Type': 'application/json',
      Authorization: `Bearer ${token}`,
      ...(options.headers || {}),
    },
  });
  const data = await response.json().catch(() => ({}));
  if (!response.ok) throw new Error(data.message || data.error || 'Erro ao processar solicitação');
  return data;
}

function dateTime(value) {
  if (!value) return '';
  return new Date(String(value).replace(' ', 'T')).toLocaleString('pt-BR');
}

function iconFor(type) {
  if (type.includes('payment')) return 'R$';
  if (type.includes('stock')) return '↓';
  if (type.includes('order')) return '#';
  return '•';
}

function render(rows = []) {
  if (!rows.length) {
    list.innerHTML = '<div class="og-empty"><strong>Nenhuma notificação</strong><span>Os eventos importantes da loja aparecerão aqui.</span></div>';
    return;
  }
  list.innerHTML = rows.map(item => `
    <article class="notification-item ${item.read_at ? '' : 'is-unread'}" data-id="${item.id}">
      <div class="notification-icon">${iconFor(item.event_type || '')}</div>
      <div class="notification-content">
        <div class="notification-head">
          <h3>${item.title || 'Notificação'}</h3>
          <time>${dateTime(item.created_at)}</time>
        </div>
        <p>${item.message || ''}</p>
        <div class="notification-actions">
          ${item.action_url ? `<a href="${base}${item.action_url}" class="btn btn-ghost btn-sm">Ver detalhes</a>` : ''}
          ${!item.read_at ? `<button class="btn btn-ghost btn-sm" data-action="read">Marcar como lida</button>` : ''}
        </div>
      </div>
    </article>
  `).join('');
}

async function load() {
  try {
    const data = await request(`/notifications${currentFilter === 'unread' ? '?unread=1' : ''}`);
    unreadCount.textContent = data.unread || 0;
    render(data.data || []);
  } catch (error) {
    list.innerHTML = `<div class="og-empty error">${error.message}</div>`;
  }
}

document.querySelectorAll('[data-filter]').forEach(button => button.addEventListener('click', () => {
  document.querySelectorAll('[data-filter]').forEach(x => x.classList.remove('active'));
  button.classList.add('active');
  currentFilter = button.dataset.filter;
  load();
}));

document.getElementById('refreshNotifications')?.addEventListener('click', load);
document.getElementById('markAllRead')?.addEventListener('click', async () => {
  await request('/notifications/read', { method: 'POST', body: '{}' });
  load();
});
document.getElementById('processQueue')?.addEventListener('click', async (event) => {
  const button = event.currentTarget;
  button.disabled = true;
  button.textContent = 'Processando...';
  try {
    const data = await request('/notifications/process', { method: 'POST', body: '{}' });
    const result = data.result || {};
    button.textContent = `${result.sent || 0} enviado(s)`;
  } catch (error) {
    button.textContent = error.message;
  } finally {
    setTimeout(() => { button.disabled = false; button.textContent = 'Processar e-mails'; }, 1800);
  }
});

list?.addEventListener('click', async (event) => {
  const button = event.target.closest('[data-action="read"]');
  if (!button) return;
  const item = button.closest('[data-id]');
  await request(`/notifications/${item.dataset.id}/read`, { method: 'POST', body: '{}' });
  load();
});

load();
