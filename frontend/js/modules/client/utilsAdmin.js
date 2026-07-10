    function openClient(
      initials,
      name,
      email,
      cadastro,
      pedidos,
      total,
      tag,
    ) {
      document.getElementById("clientAvatarLg").textContent = initials;
      document.getElementById("clientNameLg").textContent = name;
      document.getElementById("clientEmailLg").textContent = email;
      document.getElementById("clientCadastro").textContent = cadastro;
      document.getElementById("clientPedidos").textContent = pedidos;
      document.getElementById("clientTotal").textContent = total;
      const tagEl = document.getElementById("clientTagLg");
      tagEl.textContent = tag;
      tagEl.className =
        "customer-tag " +
        (tag === "VIP" ?
          "tag-vip" :
          tag === "Novo" ?
          "tag-new" :
          "tag-regular");
      document.getElementById("clientModal").classList.add("open");
    }

    function closeClient() {
      document.getElementById("clientModal").classList.remove("open");
    }

    function closeClientOutside(e) {
      if (e.target === e.currentTarget) closeClient();
    }