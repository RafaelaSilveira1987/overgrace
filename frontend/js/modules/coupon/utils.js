function openCoupon(mode = "new") {
  document.getElementById("couponModal").classList.add("open");

  if (mode === "new") {
    document.getElementById("couponModalTitle").textContent = "Novo cupom";
    //clearCouponForm();
  } else {
    document.getElementById("couponModalTitle").textContent = "Editar cupom";
  }
}

function closeCoupon() {
  document.getElementById("couponModal").classList.remove("open");
}

function clearCouponForm() {
  document.getElementById("couponCode").value = "";
  document.getElementById("couponValue").value = "";
  document.getElementById("couponMin").value = "";
  document.getElementById("couponLimit").value = "";
  document.getElementById("couponDate").value = "";
}

const input = document.getElementById('couponCode');
input.addEventListener('input', (e) => {
  e.target.value = e.target.value.toUpperCase();
});
