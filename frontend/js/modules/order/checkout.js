import { pedidoService } from '../../services/pedidoService.js';
import { notify } from '../../utils/notify.js';

document.getElementById('btnFinalizar').addEventListener('click', async () => {
    try {
        await pedidoService.criar();

        notify.success('Pedido realizado com sucesso');

        window.location.href = '/pedidos';

    } catch (e) {
        notify.error(e.message);
    }
});
