function carregarUsuarioSidebar() {

  const user = JSON.parse(localStorage.getItem("user"));

  if (!user) return;

  // nome
  const nome = user.email || user.name || "Administrador";

  document.getElementById("sidebarUserName").innerText = nome;

  // avatar automático
  const iniciais = nome
    .split(" ")
    .map(n => n[0])
    .slice(0, 2)
    .join("")
    .toUpperCase();

  document.getElementById("sidebarAvatar").innerText = iniciais;
}

function configurarLogout() {

  const logoutButton = document.getElementById("logoutButton");

  if (!logoutButton) return;

  logoutButton.addEventListener("click", () => {

    localStorage.removeItem("token");
    localStorage.removeItem("user");

    window.location.href = "/overgrace/login";
  });
}

function marcarPaginaAtiva() {

  const path = window.location.pathname;

  const page = path.split("/").pop().replace(".html", "");

  const itens = document.querySelectorAll(".nav-item");

  itens.forEach(item => {

    if (item.getAttribute("href") === page) {
      item.classList.add("active");
    }
  });
}

function iniciarSidebar() {

  carregarUsuarioSidebar();

  configurarLogout();

  marcarPaginaAtiva();
}

document.addEventListener("DOMContentLoaded", iniciarSidebar);