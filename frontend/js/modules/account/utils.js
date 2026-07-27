function setMobileMenuState(isOpen) {
    document.body.classList.toggle("menu-open", isOpen);
    const toggle = document.querySelector(".menu-toggle");
    const menu = document.getElementById("mobileMenu");
    if (toggle) toggle.setAttribute("aria-expanded", isOpen ? "true" : "false");
    if (menu) menu.setAttribute("aria-hidden", isOpen ? "false" : "true");
}

function toggleMobileMenu() {
    setMobileMenuState(!document.body.classList.contains("menu-open"));
}

function closeMobileMenu() {
    setMobileMenuState(false);
}

/* --- NAVEGA��O --- */
function switchPanel(id, btn) {
    document
        .querySelectorAll(".panel")
        .forEach((p) => p.classList.remove("active"));
    document
        .querySelectorAll(".snav-item")
        .forEach((b) => b.classList.remove("active"));
    document.getElementById("panel-" + id).classList.add("active");
    if (btn) btn.classList.add("active");
    window.scrollTo({
        top: 0,
        behavior: "smooth"
    });
}

/* --- ACCORDION PEDIDOS --- */
function toggleOrder(id) {
    const detail = document.getElementById(id);
    const isOpen = detail.classList.contains("open");
    document
        .querySelectorAll(".order-detail")
        .forEach((d) => d.classList.remove("open"));
    if (!isOpen) detail.classList.add("open");
}

/* --- EDI��O DE PERFIL --- */
function toggleEdit(section) {
    const fields = document.querySelectorAll(`#form-${section} input`);
    const btns = document.getElementById(`btns-${section}`);
    fields.forEach((f) => (f.disabled = false));
    btns.style.display = "flex";
}

function cancelEdit(section) {
    const fields = document.querySelectorAll(`#form-${section} input`);
    const btns = document.getElementById(`btns-${section}`);
    fields.forEach((f) => (f.disabled = true));
    btns.style.display = "none";
}

function saveEdit(section) {
    const fields = document.querySelectorAll(`#form-${section} input`);
    const btns = document.getElementById(`btns-${section}`);
    fields.forEach((f) => (f.disabled = true));
    btns.style.display = "none";
    // Feedback visual
    const btn = btns.querySelector(".save-btn");
    const orig = btn.textContent;
    btn.textContent = "? Salvo!";
    setTimeout(() => (btn.textContent = orig), 2000);
}

/* --- FAVORITOS --- */
function removeFav(id, e) {
    e.stopPropagation();
    const card = document.getElementById(id);
    card.style.opacity = "0";
    card.style.transform = "scale(.96)";
    card.style.transition = "opacity .25s, transform .25s";
    setTimeout(() => {
        card.remove();
        const remaining = document.querySelectorAll(".fav-card").length;
        if (remaining === 0) {
            document.getElementById("favGrid").innerHTML = `
          <div class="empty-state" style="grid-column:1/-1">
            <div class="empty-icon">?</div>
            <h3 class="empty-title">Nenhum favorito ainda</h3>
            <p class="empty-sub">Salve pe�as que voc� ama para encontr�-las facilmente.</p>
            <a href="/overgrace/colecoes" class="empty-cta">Ver colecoes</a>
          </div>`;
        }
    }, 250);
}

/* --- NOTIFICA��ES --- */
function dismissNotif(id) {
    const item = document.getElementById(id);
    item.style.opacity = "0";
    item.style.maxHeight = item.offsetHeight + "px";
    item.style.transition =
        "opacity .2s, max-height .3s .1s, padding .3s .1s";
    requestAnimationFrame(() => {
        item.style.maxHeight = "0";
        item.style.padding = "0";
    });
    setTimeout(() => item.remove(), 450);
}

/* --- LOGOUT --- */
function confirmLogout() {
    if (confirm("Tem certeza que deseja sair da conta?")) {
        localStorage.removeItem("token");
        localStorage.removeItem("role");
        window.location.href = "/overgrace/";
    }
}

window.addEventListener("resize", () => {
    if (window.innerWidth > 992) closeMobileMenu();
});

document.addEventListener("keydown", (event) => {
    if (event.key === "Escape") closeMobileMenu();
});



function initials(nameOrEmail) {
    return nameOrEmail
        .split(/\s+/)
        .filter(Boolean)
        .slice(0, 2)
        .map((part) => part[0])
        .join("")
        .toUpperCase();
}

function formatCpf(value) {
    const digits = String(value || "").replace(/\D/g, "");
    return digits.replace(/(\d{3})(\d{3})(\d{3})(\d{2})/, "$1.$2.$3-$4");
}

function formatPhone(value) {
    const digits = String(value || "").replace(/\D/g, "");
    return digits.length === 11 ?
        digits.replace(/(\d{2})(\d{5})(\d{4})/, "($1) $2-$3") :
        digits.replace(/(\d{2})(\d{4})(\d{4})/, "($1) $2-$3");
}

