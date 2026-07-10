    const orders = {
      10094: {
        client: "Ana Beatriz Souza",
        status: "enviado"
      },
      10093: {
        client: "Carlos Henrique M.",
        status: "pago"
      },
      10092: {
        client: "Fernanda Oliveira",
        status: "pendente"
      },
      10091: {
        client: "Rafael Teixeira",
        status: "pago"
      },
      10090: {
        client: "Juliana Ferreira",
        status: "cancelado"
      },
      10089: {
        client: "Bruno Alves",
        status: "enviado"
      },
      10088: {
        client: "Larissa Campos",
        status: "pago"
      },
    };

    function openDetail(num) {
      const o = orders[num];
      document.getElementById("modalOrderNum").textContent = "Pedido #" + num;
      document.getElementById("detailClientName").textContent = o.client;
      document.getElementById("orderStatusSelect").value = o.status;
      document.getElementById("orderModal").classList.add("open");
    }

    function closeDetail() {
      document.getElementById("orderModal").classList.remove("open");
    }

    function closeDetailOutside(e) {
      if (e.target === e.currentTarget) closeDetail();
    }