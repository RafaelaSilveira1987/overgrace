let stylesInjected = false;

function injectStyles() {
  if (stylesInjected) return;

  const style = document.createElement("style");
  style.dataset.cartFlyVersion = "4";
  style.textContent = `
    .cart-fly-v4 {
      position: fixed !important;
      left: 0 !important;
      top: 0 !important;
      z-index: 2147483647 !important;
      display: block !important;
      margin: 0 !important;
      padding: 0 !important;
      border: 2px solid #fff !important;
      border-radius: 12px !important;
      background: #fff !important;
      object-fit: cover !important;
      pointer-events: none !important;
      opacity: 1;
      box-shadow: 0 16px 38px rgba(0, 0, 0, .38) !important;
      transform-origin: center center !important;
      backface-visibility: hidden;
      will-change: transform, opacity;
      contain: layout paint style;
    }

    .ac-toast-product-image.cart-fly-v4-source {
      opacity: 0 !important;
      transform: scale(.72) !important;
      transition: opacity 180ms ease, transform 220ms ease !important;
    }

    .cart-btn.cart-arrival-v4,
    .cart-icon.cart-arrival-v4,
    #cart.cart-arrival-v4 {
      animation: cart-arrival-v4 620ms cubic-bezier(.2, .85, .2, 1) !important;
    }

    .cart-count.cart-count-v4,
    #cart-count.cart-count-v4,
    #cartCount.cart-count-v4 {
      animation: cart-count-v4 620ms cubic-bezier(.2, .85, .2, 1) !important;
    }

    .cart-impact-v4 {
      position: fixed !important;
      z-index: 2147483646 !important;
      width: 18px;
      height: 18px;
      border: 2px solid #111;
      border-radius: 50%;
      pointer-events: none;
      transform: translate(-50%, -50%) scale(.3);
      opacity: .8;
      animation: cart-impact-v4 500ms ease-out forwards;
    }

    @keyframes cart-arrival-v4 {
      0% { transform: scale(1); }
      38% { transform: scale(1.18) rotate(-3deg); }
      72% { transform: scale(.95) rotate(2deg); }
      100% { transform: scale(1); }
    }

    @keyframes cart-count-v4 {
      0% { transform: scale(1); }
      42% { transform: scale(1.85); }
      72% { transform: scale(.88); }
      100% { transform: scale(1); }
    }

    @keyframes cart-impact-v4 {
      from {
        opacity: .8;
        transform: translate(-50%, -50%) scale(.3);
      }
      to {
        opacity: 0;
        transform: translate(-50%, -50%) scale(3);
      }
    }
  `;

  document.head.appendChild(style);
  stylesInjected = true;
}

function sleep(milliseconds) {
  return new Promise((resolve) => window.setTimeout(resolve, milliseconds));
}

function isVisible(element) {
  if (!element) return false;

  const rect = element.getBoundingClientRect();
  const style = window.getComputedStyle(element);

  return (
    rect.width > 0 &&
    rect.height > 0 &&
    style.display !== "none" &&
    style.visibility !== "hidden"
  );
}

function firstVisible(selectors) {
  for (const selector of selectors) {
    const found = [...document.querySelectorAll(selector)].find(isVisible);
    if (found) return found;
  }

  return null;
}

function getCartElements() {
  const count = firstVisible([".cart-count", "#cart-count", "#cartCount"]);
  const button =
    count?.closest(".cart-btn, .cart-icon, #cart") ||
    firstVisible([".cart-btn", ".cart-icon", "#cart"]);

  return {
    count,
    button,
    target: count || button,
  };
}

function getAllCounters() {
  return [
    ...new Set([
      ...document.querySelectorAll(".cart-count"),
      ...document.querySelectorAll("#cart-count"),
      ...document.querySelectorAll("#cartCount"),
    ]),
  ];
}

function incrementCounter(quantity = 1) {
  const amount = Math.max(1, Number.parseInt(quantity, 10) || 1);

  getAllCounters().forEach((counter) => {
    const current = Number.parseInt(counter.textContent?.trim() || "0", 10);
    counter.textContent = String((Number.isFinite(current) ? current : 0) + amount);
    counter.hidden = false;
    counter.style.removeProperty("display");
  });
}

function restartClass(element, className) {
  if (!element) return;
  element.classList.remove(className);
  void element.offsetWidth;
  element.classList.add(className);
}

function impactAt(rect) {
  const impact = document.createElement("span");
  impact.className = "cart-impact-v4";
  impact.setAttribute("aria-hidden", "true");
  impact.style.left = `${rect.left + rect.width / 2}px`;
  impact.style.top = `${rect.top + rect.height / 2}px`;
  document.documentElement.appendChild(impact);
  window.setTimeout(() => impact.remove(), 550);
}

function pulseCart(button, count, targetRect) {
  impactAt(targetRect);
  restartClass(button, "cart-arrival-v4");
  restartClass(count, "cart-count-v4");

  window.setTimeout(() => {
    button?.classList.remove("cart-arrival-v4");
    count?.classList.remove("cart-count-v4");
  }, 700);
}

async function updateAtArrival({ quantity, onArrival, button, count, targetRect }) {
  incrementCounter(quantity);
  pulseCart(button, count, targetRect);

  if (typeof onArrival === "function") {
    try {
      await onArrival();
    } catch (error) {
      console.error("[CART FLY V4] Falha ao sincronizar o contador:", error);
    }
  }
}

function createFlyer({ sourceImage, imageSrc, sourceRect }) {
  const flyer = document.createElement("img");
  flyer.className = "cart-fly-v4";
  flyer.src = imageSrc || sourceImage?.currentSrc || sourceImage?.src || "";
  flyer.alt = "";
  flyer.setAttribute("aria-hidden", "true");
  flyer.draggable = false;
  flyer.style.width = `${sourceRect.width}px`;
  flyer.style.height = `${sourceRect.height}px`;
  flyer.style.transform = `translate3d(${sourceRect.left}px, ${sourceRect.top}px, 0) scale(1) rotate(0deg)`;

  document.documentElement.appendChild(flyer);
  return flyer;
}

async function animateWithWebAnimations({ flyer, sourceRect, targetRect, duration }) {
  const finalSize = Math.max(10, Math.min(targetRect.width, targetRect.height, 16));
  const endLeft = targetRect.left + targetRect.width / 2 - finalSize / 2;
  const endTop = targetRect.top + targetRect.height / 2 - finalSize / 2;

  const deltaX = endLeft - sourceRect.left;
  const deltaY = endTop - sourceRect.top;
  const scaleX = finalSize / Math.max(sourceRect.width, 1);
  const scaleY = finalSize / Math.max(sourceRect.height, 1);
  const finalScale = Math.min(scaleX, scaleY);

  // Ponto intermediário mais alto cria a sensação clara de o produto "subir".
  const curveX = deltaX * 0.56;
  const curveY = deltaY * 0.48 - Math.min(110, Math.max(45, Math.abs(deltaY) * 0.12));

  const keyframes = [
    {
      transform: `translate3d(${sourceRect.left}px, ${sourceRect.top}px, 0) scale(1) rotate(0deg)`,
      opacity: 1,
      offset: 0,
    },
    {
      transform: `translate3d(${sourceRect.left + curveX}px, ${sourceRect.top + curveY}px, 0) scale(.78) rotate(-7deg)`,
      opacity: 1,
      offset: 0.55,
    },
    {
      transform: `translate3d(${endLeft}px, ${endTop}px, 0) scale(${finalScale}) rotate(12deg)`,
      opacity: 0.18,
      offset: 1,
    },
  ];

  if (typeof flyer.animate === "function") {
    const animation = flyer.animate(keyframes, {
      duration,
      easing: "cubic-bezier(.18, .74, .22, 1)",
      fill: "forwards",
    });

    try {
      await animation.finished;
    } catch {
      // A remoção antecipada do nó pode cancelar a Promise da animação.
    }
    return;
  }

  // Fallback para navegadores sem Web Animations API.
  flyer.style.transition = `transform ${duration}ms cubic-bezier(.18,.74,.22,1), opacity ${duration}ms ease`;
  void flyer.offsetWidth;

  await new Promise((resolve) => {
    requestAnimationFrame(() => {
      flyer.style.transform = `translate3d(${endLeft}px, ${endTop}px, 0) scale(${finalScale}) rotate(12deg)`;
      flyer.style.opacity = "0.18";
    });

    window.setTimeout(resolve, duration + 80);
  });
}

/**
 * Anima a miniatura da notificação até o contador do carrinho.
 * A atualização da quantidade ocorre somente quando o clone chega ao destino.
 */
export async function animateProductToCart({
  toast,
  imageSrc,
  quantity = 1,
  onArrival,
  delay = 260,
  duration = 1150,
} = {}) {
  injectStyles();

  const { button, count, target } = getCartElements();

  if (!target) {
    console.warn("[CART FLY V4] Carrinho não encontrado na página.");
    if (typeof onArrival === "function") await onArrival();
    return false;
  }

  await sleep(delay);
  await new Promise((resolve) => requestAnimationFrame(() => requestAnimationFrame(resolve)));

  const sourceImage = toast?.querySelector?.(".ac-toast-product-image") || null;
  const sourceBox = sourceImage || toast;

  if (!sourceBox || !document.documentElement.contains(sourceBox)) {
    console.warn("[CART FLY V4] A origem da animação não foi encontrada.");
    const targetRect = target.getBoundingClientRect();
    await updateAtArrival({ quantity, onArrival, button, count, targetRect });
    return false;
  }

  const rawSourceRect = sourceBox.getBoundingClientRect();
  const width = Math.max(44, Math.min(rawSourceRect.width || 48, 64));
  const height = Math.max(44, Math.min(rawSourceRect.height || 48, 64));

  const sourceRect = {
    left: sourceImage
      ? rawSourceRect.left
      : rawSourceRect.right - width - 14,
    top: sourceImage
      ? rawSourceRect.top
      : rawSourceRect.top + Math.max(0, (rawSourceRect.height - height) / 2),
    width,
    height,
  };

  const targetRect = target.getBoundingClientRect();
  const flyer = createFlyer({ sourceImage, imageSrc, sourceRect });

  // Garante que o clone seja pintado antes de esconder a miniatura original.
  await new Promise((resolve) => requestAnimationFrame(resolve));
  sourceImage?.classList.add("cart-fly-v4-source");

  console.debug("[CART FLY V4] Iniciando animação", {
    sourceRect,
    targetRect,
    imageSrc: flyer.src,
  });

  await animateWithWebAnimations({ flyer, sourceRect, targetRect, duration });
  flyer.remove();

  await updateAtArrival({ quantity, onArrival, button, count, targetRect });
  return true;
}
