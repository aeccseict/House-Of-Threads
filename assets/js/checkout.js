document.addEventListener("DOMContentLoaded", function () {
  const promoBtn = document.querySelector("#apply-promo");
  const promoInput = document.querySelector("#promo-code");
  const promoResult = document.querySelector("#promo-result");

  if (promoBtn && promoInput) {
    promoBtn.addEventListener("click", function () {
      const code = promoInput.value.trim();
      if (code === "") {
        promoResult.textContent = "Please enter a promo code.";
        promoResult.className = "text-red-600 text-sm mt-1";
        return;
      }

      fetch(`/functions/validate_promo.php?code=${code}`)
        .then(res => res.json())
        .then(data => {
          if (data.valid) {
            promoResult.textContent = `Promo applied! ₹${data.amount} off`;
            promoResult.className = "text-green-600 text-sm mt-1";
            document.querySelector("#promo_applied").value = code;
          } else {
            promoResult.textContent = "Invalid or expired code.";
            promoResult.className = "text-red-600 text-sm mt-1";
          }
        });
    });
  }
});
