import { authService } from "../../services/authService.js?v=11";
import { notify } from "../../utils/notify.js";

const form = document.getElementById("formResetPassword");

const submitButton = form?.querySelector('button[type="submit"]');

const params = new URLSearchParams(window.location.search);

const token = params.get("token");
const isAdmin = params.get("mode") === "admin";

if (!token) {
  notify.error("Token de recuperação inválido ou ausente");

  if (submitButton) {
    submitButton.disabled = true;
  }
}

form?.addEventListener("submit", async (event) => {
  event.preventDefault();

  const password = document.getElementById("password")?.value || "";

  const passwordConfirmation =
    document.getElementById("passwordConfirmation")?.value || "";

  if (!token) {
    notify.error("Token de recuperação inválido");
    return;
  }

  if (password.length < 6) {
    notify.error("A senha deve ter pelo menos 6 caracteres");
    return;
  }

  if (password !== passwordConfirmation) {
    notify.error("As senhas não coincidem");
    return;
  }

  try {
    if (submitButton) {
      submitButton.disabled = true;
      submitButton.textContent = "Alterando senha...";
    }

    const response = isAdmin
      ? await authService.resetAdminPassword({
          token,
          password,
          passwordConfirmation,
        })
      : await authService.resetPassword({
          token,
          password,
          passwordConfirmation,
        });

    notify.success(response?.message || "Senha alterada com sucesso");

    setTimeout(() => {
      const base = window.APP_BASE_PATH || "";

      window.location.href = isAdmin
        ? `${base}/admin-login`
        : `${base}/login`;
    }, 1200);
  } catch (error) {
    console.error("[RESET PASSWORD ERROR]", error);

    notify.error(error?.message || "Não foi possível alterar a senha");
  } finally {
    if (submitButton) {
      submitButton.disabled = false;
      submitButton.textContent = "Alterar senha";
    }
  }
});
