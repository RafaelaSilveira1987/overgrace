<!doctype html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>OverGrace Admin – Conteúdo do Site</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
        href="https://fonts.googleapis.com/css2?family=Jost:wght@300;400;500;600&family=Playfair+Display:wght@700&display=swap"
        rel="stylesheet" />

    <link rel="stylesheet" href="/overgrace/frontend/pages/paineladm/paineladm.css?v=22" />
    <link rel="stylesheet" href="/overgrace/frontend/pages/paineladm/pages/pages-css/site-content.css" />
    <link rel="stylesheet" href="/overgrace/frontend/css/utils.css" />
</head>

<body>
    <div class="shell">
        <?php include 'frontend/pages/paineladm/sidebar.php' ?>

        <div class="main">
            <?php include 'frontend/pages/paineladm/navbar.php' ?>

            <div class="page-content">
                <div class="page-header cms-page-header">
                    <div>
                        <h2>Conteúdo do site</h2>
                        <p>Altere textos e imagens fixas da loja sem mexer no código.</p>
                    </div>

                    <button class="btn btn-primary" id="saveSiteContent" form="siteContentForm" type="submit">
                        Salvar alterações
                    </button>
                </div>

                <div id="siteContentAlert" class="cms-alert" hidden></div>

                <form id="siteContentForm" enctype="multipart/form-data">
                    <div id="siteContentSections"></div>
                </form>
            </div>
        </div>
    </div>

    <script type="module" src="/overgrace/frontend/js/modules/site-content/admin.js?v=2"></script>
</body>

</html>
