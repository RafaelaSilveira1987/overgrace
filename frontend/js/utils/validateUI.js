export function marcarErro(input) {
    if (!input) return;

    input.classList.add('input-error');

    const remover = () => {
        input.classList.remove('input-error');
        input.removeEventListener('input', remover);
        input.removeEventListener('blur', remover);
    };

    input.addEventListener('input', remover);
    input.addEventListener('blur', remover);
}


export function validarCamposObrigatorios(campos = []) {
    let erro = false;

    campos.forEach(campo => {
        if (!campo || !campo.value) {
            marcarErro(campo);
            erro = true;
        }
    });

    return erro;
}
