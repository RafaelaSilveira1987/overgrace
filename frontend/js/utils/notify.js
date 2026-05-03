/* ── TOASTS ── */
let toastCount = 0;
let styleInjected = false;

function injectStyles() {
  if (styleInjected) return;

  const style = document.createElement('style');
  style.innerHTML = `
    .ac-toast-area {
        position: fixed;
        bottom: 24px;
        right: 24px;
        z-index: 9999999;
        display: flex;
        flex-direction: column;
        gap: 10px;
        pointer-events: none;
    }

    .ac-toast {
        display: flex;
        align-items: flex-start;
        gap: 10px;
        padding: 12px 14px;
        border-radius: 10px;
        min-width: 280px;
        max-width: 380px;
        background: #fff;
        border: 1px solid #ddd;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.12);
        pointer-events: all;
        cursor: pointer;
        transform: translateX(120%);
        opacity: 0;
        transition: transform 0.25s cubic-bezier(0.34, 1.2, 0.64, 1), opacity 0.2s;
    }

    .ac-toast.ac-show {
        transform: translateX(0);
        opacity: 1;
    }

    .ac-toast.ac-hide {
        transform: translateX(120%);
        opacity: 0;
        transition: transform 0.2s ease-in, opacity 0.2s;
    }

    .ac-toast-icon {
        width: 28px;
        height: 28px;
        border-radius: 50%;
        flex-shrink: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 13px;
        margin-top: 1px;
    }

    .ac-toast.ac-success .ac-toast-icon {
        background: #d4edda;
        color: #155724;
    }

    .ac-toast.ac-error .ac-toast-icon {
        background: #f8d7da;
        color: #721c24;
    }

    .ac-toast.ac-warning .ac-toast-icon {
        background: #fff3cd;
        color: #856404;
    }

    .ac-toast.ac-info .ac-toast-icon {
        background: #d1ecf1;
        color: #0c5460;
    }

    .ac-toast-content {
        flex: 1;
        min-width: 0;
    }

    .ac-toast-title {
        font-size: 13px;
        font-weight: 600;
        color: #212529;
    }

    .ac-toast-msg {
        font-size: 12px;
        color: #6c757d;
        margin-top: 2px;
        line-height: 1.4;
    }

    .ac-toast-close {
        background: none;
        border: none;
        cursor: pointer;
        padding: 0;
        color: #6c757d;
        font-size: 15px;
        line-height: 1;
        flex-shrink: 0;
        margin-top: 1px;
    }

    .ac-toast-close:hover {
        color: #000;
    }

    .ac-toast-progress {
        height: 2px;
        border-radius: 0 0 10px 10px;
        margin: 8px -14px -12px;
        overflow: hidden;
    }

    .ac-toast-progress-bar {
        height: 100%;
        width: 100%;
        border-radius: 2px;
        transform-origin: left;
    }

    .ac-toast.ac-success .ac-toast-progress-bar {
        background: #28a745;
    }

    .ac-toast.ac-error .ac-toast-progress-bar {
        background: #dc3545;
    }

    .ac-toast.ac-warning .ac-toast-progress-bar {
        background: #ffc107;
    }

    .ac-toast.ac-info .ac-toast-progress-bar {
        background: #17a2b8;
    }
  `;

  document.head.appendChild(style);
  styleInjected = true;
}

function getToastArea() {
  injectStyles();

  let area = document.querySelector('.ac-toast-area');

  if (!area) {
    area = document.createElement('div');
    area.className = 'ac-toast-area';
    document.body.appendChild(area);
  }

  return area;
}

function dismissToast(id) {
  const el = document.getElementById(id);
  if (!el) return;

  el.classList.remove('ac-show');
  el.classList.add('ac-hide');

  setTimeout(() => el.remove(), 250);
}

function show(msg, type = 'info', duration = 4000) {
  const area = getToastArea();

  const id = 'toast-' + (++toastCount);

  const icons = {
    success: '✓',
    error: '✕',
    warning: '⚠',
    info: 'ⓘ'
  };

  const labelSub = {
    success: 'Concluído com sucesso',
    error: 'Erro ao executar ação',
    warning: 'Atenção',
    info: 'Informação'
  };

  const el = document.createElement('div');
  el.className = `ac-toast ac-${type}`;
  el.id = id;

  el.innerHTML = `
    <div class="ac-toast-icon">${icons[type]}</div>
    <div class="ac-toast-content">
      <div class="ac-toast-title">${msg}</div>
      <div class="ac-toast-msg">${labelSub[type]}</div>
      <div class="ac-toast-progress">
        <div class="ac-toast-progress-bar" id="bar-${id}"></div>
      </div>
    </div>
    <button class="ac-toast-close">✕</button>
  `;

  el.querySelector('.ac-toast-close').onclick = () => dismissToast(id);
  el.onclick = () => dismissToast(id);

  area.appendChild(el);

  requestAnimationFrame(() => {
    el.classList.add('ac-show');

    const bar = document.getElementById(`bar-${id}`);
    if (bar) {
      bar.style.transition = `transform ${duration}ms linear`;
      bar.style.transform = 'scaleX(0)';
    }
  });

  setTimeout(() => dismissToast(id), duration);
}

export const notify = {
  success: (msg, duration) => show(msg, 'success', duration),
  error: (msg, duration) => show(msg, 'error', duration),
  warning: (msg, duration) => show(msg, 'warning', duration),
  info: (msg, duration) => show(msg, 'info', duration),
};
