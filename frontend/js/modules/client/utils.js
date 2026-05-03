    let currentStep = 1;
    let shippingCost = 0;
    let shippingLabel = "Grátis";

    /* ——— NAVEGAÇÃO DE STEPS ——— */
    function goToStep(n) {
      if (n > currentStep + 1) return;
      document
        .querySelectorAll(".panel")
        .forEach((p) => p.classList.remove("active"));
      document.getElementById("panel" + n).classList.add("active");

      document.querySelectorAll(".step-item").forEach((el, i) => {
        el.classList.remove("active", "done");
        if (i + 1 < n) el.classList.add("done");
        if (i + 1 === n) el.classList.add("active");
      });

      currentStep = n;
      window.scrollTo({
        top: 0,
        behavior: "smooth"
      });
    }

    /* ——— FRETE ——— */
    function selectShip(label, cost, name) {
      document
        .querySelectorAll(".ship-option")
        .forEach((o) => o.classList.remove("selected"));
      label.classList.add("selected");
      shippingCost = cost;
      shippingLabel = name;
      const total = 407 + cost;
      document.getElementById("shipLabel").textContent =
        cost === 0 ? "Grátis" : "R$ " + cost.toFixed(2).replace(".", ",");
      document.getElementById("shipLabel").style.color =
        cost === 0 ? "var(--success)" : "";
      document.getElementById("totalFinal").textContent =
        "R$ " + total.toFixed(2).replace(".", ",");
    }

    /* ——— ABAS DE PAGAMENTO ——— */
    function selectPayTab(id, btn) {
      document
        .querySelectorAll(".pay-tab")
        .forEach((t) => t.classList.remove("active"));
      document
        .querySelectorAll(".pay-panel")
        .forEach((p) => p.classList.remove("active"));
      btn.classList.add("active");
      document.getElementById("pay-" + id).classList.add("active");
    }

    /* ——— CONFIRMAÇÃO ——— */
    function confirmarPedido() {
      document.getElementById("checkoutLayout").style.display = "none";
      document.getElementById("stepsBar").style.display = "none";
      const num =
        "FOR-" + String(Math.floor(Math.random() * 99999)).padStart(5, "0");
      document.getElementById("confirmOrderNum").textContent = "#" + num;
      document.getElementById("confirmScreen").classList.add("active");
      window.scrollTo({
        top: 0,
        behavior: "smooth"
      });
    }

    /* ——— MÁSCARAS ——— */
    function maskCPF(el) {
      let v = el.value.replace(/\D/g, "").slice(0, 11);
      if (v.length > 9)
        v = v.replace(/(\d{3})(\d{3})(\d{3})(\d{1,2})/, "$1.$2.$3-$4");
      else if (v.length > 6)
        v = v.replace(/(\d{3})(\d{3})(\d{1,3})/, "$1.$2.$3");
      else if (v.length > 3) v = v.replace(/(\d{3})(\d{1,3})/, "$1.$2");
      el.value = v;
    }

    function maskPhone(el) {
      let v = el.value.replace(/\D/g, "").slice(0, 11);
      if (v.length > 10) v = v.replace(/(\d{2})(\d{5})(\d{4})/, "($1) $2-$3");
      else if (v.length > 6)
        v = v.replace(/(\d{2})(\d{4,5})(\d{0,4})/, "($1) $2-$3");
      else if (v.length > 2) v = v.replace(/(\d{2})(\d+)/, "($1) $2");
      el.value = v;
    }

    function maskCEP(el) {
      let v = el.value.replace(/\D/g, "").slice(0, 8);
      if (v.length > 5) v = v.replace(/(\d{5})(\d{1,3})/, "$1-$2");
      el.value = v;
    }

    async function fetchCEP(cep) {
      const c = cep.replace(/\D/g, "");
      if (c.length !== 8) return;
      try {
        const r = await fetch("https://viacep.com.br/ws/" + c + "/json/");
        const d = await r.json();
        if (!d.erro) {
          document.getElementById("endereco").value = d.logradouro || "";
          document.getElementById("bairro").value = d.bairro || "";
          document.getElementById("cidade").value = d.localidade || "";
          document.getElementById("estado").value = d.uf || "";
        }
      } catch (e) {}
    }

    function maskCard(el) {
      let v = el.value.replace(/\D/g, "").slice(0, 16);
      v = v.replace(/(\d{4})(?=\d)/g, "$1 ");
      el.value = v;
      const brand = v.startsWith("4") ?
        "VISA" :
        v.startsWith("5") ?
        "MASTERCARD" :
        v.startsWith("3") ?
        "AMEX" :
        "••••";
      document.getElementById("cardBrand").textContent = brand;
      document.getElementById("cardNumPreview").textContent =
        v || "•••• &nbsp;•••• &nbsp;•••• &nbsp;••••";
    }

    function maskExp(el) {
      let v = el.value.replace(/\D/g, "").slice(0, 4);
      if (v.length > 2) v = v.replace(/(\d{2})(\d{1,2})/, "$1/$2");
      el.value = v;
      document.getElementById("cardExpPreview").textContent = v || "MM/AA";
    }

    function copyPix() {
      const code = document.getElementById("pixCode").textContent;
      navigator.clipboard.writeText(code).then(() => {
        const btn = event.target;
        btn.textContent = "✓ Copiado!";
        setTimeout(() => (btn.textContent = "Copiar código"), 2000);
      });
    }

    /* Gera barras de código de barras simulado */
    const bc = document.getElementById("barcodeSvg");
    if (bc) {
      for (let i = 0; i < 80; i++) {
        const bar = document.createElement("span");
        bar.style.width = (Math.random() > 0.5 ? 2 : 1) + "px";
        bar.style.background = "#1a1814";
        bc.appendChild(bar);
      }
    }