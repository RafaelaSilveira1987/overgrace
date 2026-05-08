const menuToggle = document.getElementById("mobileMenuToggle");
const mobileMenu = document.getElementById("mobileMenu");
const menuOverlay = document.getElementById("menuOverlay");
const closeMenu = document.getElementById("closeMenu");

function abrirMenu() {

  mobileMenu.classList.add("active");
  menuOverlay.classList.add("active");

  document.body.style.overflow = "hidden";
}

function fecharMenu() {

  mobileMenu.classList.remove("active");
  menuOverlay.classList.remove("active");

  document.body.style.overflow = "";
}

if (menuToggle) {
  menuToggle.addEventListener("click", abrirMenu);
}

if (closeMenu) {
  closeMenu.addEventListener("click", fecharMenu);
}

if (menuOverlay) {
  menuOverlay.addEventListener("click", fecharMenu);
}