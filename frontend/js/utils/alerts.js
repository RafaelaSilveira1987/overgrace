export function alertConfirm(message, type = "alert", title = "Aviso") {
    return new Promise((resolve) => {

        // Remove modal anterior
        const existing = document.getElementById("customModalWrapper");
        if (existing) existing.remove();

        // ===== CONFIG POR TIPO =====
        const config = {
            confirm: {
                icon: "🗑",
                iconClass: "ac-danger",
                confirmText: "Confirmar",
                cancelText: "Cancelar",
                confirmBtnClass: "ac-btn-danger"
            },
            confirm_alert: {
                icon: "ⓘ",
                iconClass: "ac-info",
                confirmText: "Confirmar",
                cancelText: "Cancelar",
                confirmBtnClass: "ac-btn-primary"
            },
            alert: {
                icon: "ⓘ",
                iconClass: "ac-info",
                confirmText: "OK",
                confirmBtnClass: "ac-btn-primary"
            },
            success: {
                icon: "✔",
                iconClass: "ac-success",
                confirmText: "OK",
                confirmBtnClass: "ac-btn-primary"
            },
            warning: {
                icon: "⚠",
                iconClass: "ac-warning",
                confirmText: "OK",
                confirmBtnClass: "ac-btn-primary"
            },
            error: {
                icon: "✕",
                iconClass: "ac-danger",
                confirmText: "OK",
                confirmBtnClass: "ac-btn-danger"
            }
        };

        const cfg = config[type] || config.alert;

        // ===== HTML =====
        const modalHTML = `
        <div class="ac-modal-backdrop" id="customModalWrapper">
          <div class="ac-modal ac-modal-sm">

            <div class="ac-modal-header">
              <div>
                <div class="ac-modal-title">${title}</div>
              </div>
              <button class="ac-modal-close" id="customCloseBtn">✕</button>
            </div>

            <div class="ac-modal-body">
              <div class="ac-confirm-body">
                <div class="ac-confirm-icon ${cfg.iconClass}">${cfg.icon}</div>
                <div class="ac-confirm-title">${title}</div>
                <div class="ac-confirm-msg">${message}</div>
              </div>
            </div>

            <div class="ac-modal-footer">
              ${type === "confirm" || type === "confirm_alert"
                ? `<button class="ac-btn ac-btn-ghost" id="customCancelBtn">${cfg.cancelText}</button>`
                : ""
            }

              <button class="ac-btn ${cfg.confirmBtnClass}" id="customConfirmBtn">
                ${cfg.confirmText}
              </button>
            </div>

          </div>
        </div>
        `;

        // Inserir no DOM
        const wrapper = document.createElement("div");
        wrapper.innerHTML = modalHTML;
        document.body.appendChild(wrapper);

        const modal = document.getElementById("customModalWrapper");

        // ===== ABRIR =====
        setTimeout(() => {
            modal.classList.add("ac-open");
        }, 10);

        // ===== FECHAR =====
        const close = (result) => {
            modal.classList.remove("ac-open");
            setTimeout(() => {
                modal.remove();
                resolve(result);
            }, 200);
        };

        // ===== EVENTOS =====
        const btnConfirm = document.getElementById("customConfirmBtn");
        const btnCancel = document.getElementById("customCancelBtn");
        const btnClose = document.getElementById("customCloseBtn");

        btnConfirm.onclick = () => close(true);

        if (type === "confirm" || type === "confirm_alert") {
            btnCancel.onclick = () => close(false);
        }

        btnClose.onclick = () => close(false);

        // clique fora
        modal.addEventListener("click", (e) => {
            if (e.target === modal) {
                close(false);
            }
        });
    });
}