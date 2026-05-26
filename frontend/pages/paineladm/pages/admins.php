<!doctype html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administradores — OverGrace</title>

    <link rel="stylesheet" href="/overgrace/frontend/pages/paineladm/paineladm.css">
    <link rel="stylesheet" href="/overgrace/frontend/pages/paineladm/pages/pages-css/admins.css">
</head>

<body>

    <script>
    window.parent.postMessage({
        type: "page",
        name: "admins"
    }, "*");
    </script>

    <div class="shell">
        <?php include 'frontend/pages/paineladm/sidebar.php' ?>

        <div class="main">
            <?php include 'frontend/pages/paineladm/navbar.php' ?>

            <div class="page-content">
                <div class="page-header">
                    <h1>Administradores</h1>
                    <p>Gerenciamento de acessos e equipe</p>
                </div>

                <div class="admins-container">
                    <div class="card">
                        <div class="card-header">
                            <span class="card-title">Equipe do Painel</span>
                            <button class="btn btn-primary" onclick="openCreateModal()">
                                + Novo Administrador
                            </button>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table>
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Nome</th>
                                            <th>Email</th>
                                            <th>Cargo</th>
                                            <th>Status</th>
                                            <th>Ações</th>
                                        </tr>
                                    </thead>
                                    <tbody id="adminsTable">
                                        <!-- Carregado via JS -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Criar/Editar -->
    <div id="adminModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 id="modalTitle">Novo Administrador</h2>
                <span class="close" onclick="closeModal()">&times;</span>
            </div>
            <form id="formAdmin">
                <input type="hidden" id="userId">
                <div class="form-group">
                    <label class="form-label">Nome</label>
                    <input type="text" id="adminNome" class="form-input" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Email</label>
                    <input type="email" id="adminEmail" class="form-input" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Senha</label>
                    <input type="password" id="adminPassword" class="form-input"
                        placeholder="Deixe em branco para manter">
                </div>
                <div class="form-group">
                    <label class="form-label">Cargo</label>
                    <select id="adminRole" class="form-input">
                        <option value="admin">Admin</option>
                        <option value="superadmin">Superadmin</option>
                    </select>
                </div>
                <div style="margin-top: 20px; display: flex; gap: 10px;">
                    <button type="submit" class="btn btn-primary" style="flex: 1;">Salvar</button>
                    <button type="button" class="btn" onclick="closeModal()"
                        style="flex: 1; background: #eee;">Cancelar</button>
                </div>
            </form>
        </div>
    </div>

    <script src="/overgrace/frontend/js/modules/admin/admins.js"></script>

</body>

</html>