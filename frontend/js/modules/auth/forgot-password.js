import { authService } from "../../services/authService.js?v=12";
import { notify } from "../../utils/notify.js";

const form = document.getElementById("formForgotPassword");
const submitButton = form?.querySelector('button[type="submit"]');
const developmentResetLink = document.getElementById("developmentResetLink");
const params = new URLSearchParams(window.location.search);
const isAdmin = params.get("mode") === "admin";

form?.addEventListener("submit", async (event) => {
  event.preventDefault();

  const email = document.getElementById("email")?.value.trim();

  if (!email) {
    notify.error("Informe seu e-mail");
    return;
  }

  try {
    if (submitButton) {
      submitButton.disabled = true;
      submitButton.textContent = "Enviando...";
    }

    if (developmentResetLink) {
      developmentResetLink.innerHTML = "";
    }

    const response = isAdmin
      ? await authService.forgotAdminPassword(email)
      : await authService.forgotPassword(email);

    notify.success(
      response?.message ||
        "Se o e-mail estiver cadastrado, você receberá as instruções para redefinir a senha.",
    );

    // O backend só devolve este link quando APP_ENV não é production.
    // Em produção, o token deve ser enviado por e-mail e nunca exibido na tela.
    if (developmentResetLink && response?.development_reset_url) {
      const resetUrl = String(response.development_reset_url);
      const link = document.createElement("a");
      link.href = resetUrl;
      link.textContent = "Clique aqui para redefinir a senha";

      const box = document.createElement("div");
      box.className = "development-reset";

      const title = document.createElement("strong");
      title.textContent = "Ambiente local";

      const text = document.createElement("p");
      text.textContent =
        "O envio por e-mail ainda não está configurado. Use o link abaixo para testar o fluxo.";

      box.append(title, text, link);
      developmentResetLink.appendChild(box);
    }
  } catch (error) {
    console.error("[FORGOT PASSWORD ERROR]", error);
    notify.error(error?.message || "Não foi possível solicitar a recuperação");
  } finally {
    if (submitButton) {
      submitButton.disabled = false;
      submitButton.textContent = "Enviar instruções";
    }
  }
});
