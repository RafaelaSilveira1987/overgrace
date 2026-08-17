import { api } from "../../core/api.js?v=10";

const wrapper = document.getElementById("topbarAdminUser");
const nameEl = document.getElementById("topbarAdminName");

function firstName(user) {
  const value = String(user?.nome || user?.email || "Administrador").trim();
  return value.split(/\s+/)[0] || "Administrador";
}

async function hydrateAdminHeader() {
  const token = localStorage.getItem("token");
  const role = localStorage.getItem("role");

  if (!token || role !== "admin") {
    if (wrapper) wrapper.hidden = true;
    return;
  }

  try {
    const user = await api.get("/admin/me");
    if (nameEl) nameEl.textContent = firstName(user);
    if (wrapper) wrapper.hidden = false;
  } catch (error) {
    console.warn("[ADMIN HEADER] Não foi possível carregar o usuário logado.", error);
    if (wrapper) wrapper.hidden = true;
  }
}

hydrateAdminHeader();
