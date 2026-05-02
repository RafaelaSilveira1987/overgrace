function trocarImagem(el) {
    document.getElementById("mainProductImage").src = el.src;
}

const sizesContainer = document.getElementById('sizes');
sizesContainer.addEventListener('click', function (e) {
    if (e.target.classList.contains('size-btn')) {

        // remove seleção anterior
        document.querySelectorAll('.size-btn').forEach(btn => {
            btn.classList.remove('active');
        });

        // adiciona no clicado
        e.target.classList.add('active');
    }
});
