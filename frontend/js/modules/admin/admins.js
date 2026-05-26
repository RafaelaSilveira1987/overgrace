/* admins.js */
const authToken = localStorage.getItem("token");

if (!authToken) {
  location.href = "/overgrace/admin-login";
}

async function loadAdmins() {
  console.log("Carregando administradores...");
  try {
    const response = await fetch("/overgrace/api/users", {
      headers: {
        Authorization: `Bearer ${authToken}`,
      },
    });

    if (!response.ok) throw new Error("Erro ao buscar usuários");

    const users = await response.json();
    const tbody = document.getElementById("adminsTable");
    tbody.innerHTML = "";

    users.forEach((user) => {
      const row = document.createElement("tr");
      row.innerHTML = `
                <td>${user.id}</td>
                <td>${user.nome || user.name}</td>
                <td>${user.email}</td>
                <td><span class="badge badge-role">${user.cargo}</span></td>
                <td>
                    <span class="badge ${user.status === "ativo" ? "badge-active" : "badge-inactive"}">
                        ${user.status === "ativo" ? "Ativo" : "Inativo"}
                    </span>
                </td>
                <td>
                    <button class="btn btn-sm btn-edit" onclick="openEditModal(${JSON.stringify(user).replace(/"/g, "&quot;")})">Editar</button>
                    <button class="btn btn-sm btn-inactive" onclick="toggleUserStatus(${user.id}, '${user.status}')">
                        ${user.status === "ativo" ? "Inativar" : "Ativar"}
                    </button>
                </td>
            `;
      tbody.appendChild(row);
    });
  } catch (error) {
    console.error("Erro:", error);
    alert("Erro ao carregar administradores");
  }
}

// Modal functions
function openCreateModal() {
  document.getElementById("modalTitle").innerText = "Novo Administrador";
  document.getElementById("formAdmin").reset();
  document.getElementById("userId").value = "";
  document.getElementById("adminPassword").required = true;
  document.getElementById("adminModal").style.display = "block";
}

function openEditModal(user) {
  document.getElementById("modalTitle").innerText = "Editar Administrador";
  document.getElementById("userId").value = user.id;
  document.getElementById("adminNome").value = user.nome || user.name;
  document.getElementById("adminEmail").value = user.email;
  document.getElementById("adminRole").value = user.cargo;
  document.getElementById("adminPassword").required = false;
  document.getElementById("adminModal").style.display = "block";
}

function closeModal() {
  document.getElementById("adminModal").style.display = "none";
}

document
  .getElementById("formAdmin")
  .addEventListener("submit", async function (e) {
    e.preventDefault();

    const id = document.getElementById("userId").value;
    const nome = document.getElementById("adminNome").value;
    const email = document.getElementById("adminEmail").value;
    const password = document.getElementById("adminPassword").value;
    const role = document.getElementById("adminRole").value;

    const url = id ? `/overgrace/api/users/${id}` : "/overgrace/api/users";
    const method = "POST"; // Baseado no exemplo do usuário que usa POST para update também

    const body = {
      nome,
      email,
      password,
      cargo: role,
    };

    try {
      const response = await fetch(url, {
        method: method,
        headers: {
          "Content-Type": "application/json",
          Authorization: `Bearer ${authToken}`,
        },
        body: JSON.stringify(body),
      });

      if (response.ok) {
        alert(id ? "Usuário atualizado!" : "Usuário criado!");
        closeModal();
        loadAdmins();
      } else {
        const err = await response.json();
        alert(err.error || "Erro na operação");
      }
    } catch (error) {
      console.error("Erro:", error);
    }
  });

async function toggleUserStatus(id, currentStatus) {
  const isAtivo = currentStatus === "ativo";

  const action = isAtivo ? "inativar" : "ativar";
  if (!confirm(`Deseja ${action} este usuário?`)) return;

  const endpoint = isAtivo
    ? `/overgrace/api/users/${id}/inactive`
    : `/overgrace/api/users/${id}/active`;

  try {
    const response = await fetch(endpoint, {
      method: "POST",
      headers: { Authorization: `Bearer ${authToken}` },
    });

    if (response.ok) {
      loadAdmins();
    } else {
      alert("Erro ao alterar status");
    }
  } catch (error) {
    console.error("Erro:", error);
  }
}

// Close modal when clicking outside
window.onclick = function (event) {
  const modal = document.getElementById("adminModal");
  if (event.target == modal) {
    closeModal();
  }
};

// Initial load
loadAdmins();
