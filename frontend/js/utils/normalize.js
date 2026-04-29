/*

USO DAS FUNÇÕES

// DATA
dataUtil(1714060800); // "2024-04-25"
dataUtil('2024-04-25 10:00:00', 'format', 'd/m/Y'); // "25/04/2024"
dataUtil('2024-01-01', 'diff', 'dias');
dataUtil(null, 'hoje', 'd/m/Y');

// VALOR
valorUtil('1.234,56'); // "1.234,56"
valorUtil(1000);       // "1.000,00"
valorUtil('1.234,56', 'number'); // 1234.56

*/




// ==============================
// 🗓️ DATA UTIL
// ==============================
export function dataUtil(valor = null, tipo = 'format', formato = 'Y-m-d') {
    const now = new Date();

    function parseDate(v) {
        if (!v || v === '0000-00-00 00:00:00') return null;

        // timestamp
        if (!isNaN(v)) {
            return new Date(Number(v) * 1000);
        }

        // datetime/date string
        return new Date(v);
    }

    function formatDate(date, format) {
        if (!date) return '';

        const y = date.getFullYear();
        const m = String(date.getMonth() + 1).padStart(2, '0');
        const d = String(date.getDate()).padStart(2, '0');

        if (format === 'Y-m-d') return `${y}-${m}-${d}`;
        if (format === 'd/m/Y') return `${d}/${m}/${y}`;

        return date;
    }

    function diff(date, tipo) {
        if (!date) return null;

        const diffMs = now - date;

        if (tipo === 'dias') {
            return Math.floor(diffMs / (1000 * 60 * 60 * 24));
        }

        if (tipo === 'meses') {
            return (now.getFullYear() - date.getFullYear()) * 12 +
                   (now.getMonth() - date.getMonth());
        }

        if (tipo === 'anos') {
            return now.getFullYear() - date.getFullYear();
        }

        return null;
    }

    if (tipo === 'hoje') {
        return formatDate(now, formato);
    }

    const date = parseDate(valor);

    if (tipo === 'format') {
        return formatDate(date, formato);
    }

    if (tipo === 'diff') {
        return diff(date, formato); // formato = dias|meses|anos
    }

    return null;
}


// ==============================
// 💰 VALOR UTIL
// ==============================
export function valorUtil(valor, tipo = 'format') {
    if (valor === null || valor === undefined || valor === '') return '0,00';

    let num = valor;

    if (typeof valor === 'string') {
        num = valor
            .replace(/\./g, '')   // remove milhar
            .replace(',', '.');  // vírgula → ponto
    }

    num = parseFloat(num);

    if (isNaN(num)) return '0,00';

    if (tipo === 'format') {
        return num.toLocaleString('pt-BR', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
    }

    if (tipo === 'number') {
        return num;
    }

    return num;
}
