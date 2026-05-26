<!doctype html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil — OverGrace </title>
    <link rel="stylesheet" href="/overgrace/frontend/pages/paineladm/pages/pages-css/perfil.css">
    <link rel="stylesheet" href="/overgrace/frontend/pages/paineladm/paineladm.css">
</head>

<body>
    <script>
    window.parent.postMessage({
        type: "page",
        name: "perfil"
    }, "*");
    </script>
    <div class="shell">

        <?php include 'frontend/pages/paineladm/sidebar.php' ?>

        <div class="main">

            <?php include 'frontend/pages/paineladm/navbar.php' ?>

            <div class="page-content">

                <div class="page-header">

                    <h1>
                        Meu Perfil
                    </h1>

                    <p>
                        Informações da sua conta administrativa
                    </p>

                </div>

                <div class="perfil-grid">

                    <!-- CARD PERFIL -->

                    <div class="card perfil-card">

                        <div class="perfil-avatar" id="avatar">

                            —
                        </div>

                        <div class="perfil-info">

                            <div class="perfil-item">

                                <label>
                                    Nome
                                </label>

                                <span id="perfilNome">
                                    —
                                </span>

                            </div>

                            <div class="perfil-item">

                                <label>
                                    E-mail
                                </label>

                                <span id="perfilEmail">
                                    —
                                </span>

                            </div>

                            <div class="perfil-item">

                                <label>
                                    Cargo
                                </label>

                                <span class="cargo-badge" id="perfilCargo">

                                    —
                                </span>

                            </div>

                        </div>

                    </div>

                    <!-- ALTERAR SENHA -->

                    <div class="card">

                        <div class="card-header">

                            <span class="card-title">
                                Segurança
                            </span>

                        </div>

                        <div class="card-body">

                            <form id="formSenha">

                                <div class="form-group">

                                    <label class="form-label">
                                        Nova senha
                                    </label>

                                    <input type="password" class="form-input" id="novaSenha" required>

                                </div>

                                <div class="form-group">

                                    <label class="form-label">
                                        Confirmar senha
                                    </label>

                                    <input type="password" class="form-input" id="confirmarSenha" required>

                                </div>

                                <button type="submit" class="btn btn-primary">

                                    Alterar senha

                                </button>

                            </form>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

    <script>
    const authToken =
        localStorage.getItem("token");

    if (!authToken) {

        location.href =
            "/overgrace/admin-login";
    }

    async function loadProfile() {

        try {

            const response =
                await fetch(
                    '/overgrace/api/me', {
                        headers: {
                            Authorization: `Bearer ${authToken}`
                        }
                    }
                );

            if (!response.ok) {

                localStorage.removeItem("token");

                location.href =
                    "/overgrace/admin-login";

                return;
            }

            const user =
                await response.json();

            fillProfile(user);

            // Armazenar role para controle de UI se necessário
            localStorage.setItem("userRole", user.role);

        } catch (error) {

            console.error(error);

            alert(
                "Erro ao carregar perfil"
            );
        }
    }

    function fillProfile(user) {

        document.getElementById(
                "perfilNome"
            ).textContent =
            user.nome || '—';

        document.getElementById(
                "perfilEmail"
            ).textContent =
            user.email || '—';

        document.getElementById(
                "perfilCargo"
            ).textContent =
            user.role || 'admin';

        const avatar =
            document.getElementById(
                "avatar"
            );

        avatar.textContent =
            user.nome ?
            user.nome
            .substring(0, 2)
            .toUpperCase() :
            'AD';
    }

    document
        .getElementById("formSenha")
        .addEventListener(
            "submit",
            async function(e) {

                e.preventDefault();

                const novaSenha =
                    document.getElementById(
                        "novaSenha"
                    ).value;

                const confirmarSenha =
                    document.getElementById(
                        "confirmarSenha"
                    ).value;

                if (
                    novaSenha !==
                    confirmarSenha
                ) {

                    alert(
                        "Senhas não coincidem"
                    );

                    return;
                }

                try {

                    const response =
                        await fetch(
                            '/overgrace/api/change-password', {
                                method: 'POST',

                                headers: {
                                    'Content-Type': 'application/json',

                                    Authorization: `Bearer ${authToken}`
                                },

                                body: JSON.stringify({
                                    nova_senha: novaSenha
                                })
                            }
                        );

                    const result =
                        await response.json();

                    if (!response.ok) {

                        alert(
                            result.error ||
                            "Erro"
                        );

                        return;
                    }

                    alert(
                        "Senha alterada com sucesso"
                    );

                    this.reset();

                } catch (error) {

                    console.error(error);

                    alert(
                        "Erro ao alterar senha"
                    );
                }
            }
        );

    loadProfile();
    </script>

</body>

</html>