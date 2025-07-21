// ========= Cart & Wishlist Count Update =========
document.addEventListener("DOMContentLoaded", function () {
  updateCartCount();
  updateWishlistCount();
});

function updateCartCount() {
  fetch("/functions/cart_count.php")
    .then(res => res.json())
    .then(data => {
      const cartBadge = document.querySelector("#cart-count");
      if (cartBadge) cartBadge.textContent = data.count || 0;
    });
}

function updateWishlistCount() {
  fetch("/functions/wishlist_count.php")
    .then(res => res.json())
    .then(data => {
      const wishBadge = document.querySelector("#wishlist-count");
      if (wishBadge) wishBadge.textContent = data.count || 0;
    });
}

// ========= Toast Alert =========
function showToast(message, type = 'success') {
  const toast = document.createElement('div');
  toast.className = `fixed top-5 right-5 z-50 px-4 py-2 rounded shadow-md text-white text-sm ${
    type === 'success' ? 'bg-green-600' : 'bg-red-600'
  }`;
  toast.textContent = message;
  document.body.appendChild(toast);
  setTimeout(() => toast.remove(), 3000);
}

// ========= Add to Cart / Wishlist Buttons =========
document.addEventListener("click", function (e) {
  if (e.target.classList.contains("add-to-cart")) {
    const pid = e.target.dataset.id;
    fetch(`/functions/cart.php?action=add&id=${pid}`)
      .then(res => res.json())
      .then(data => {
        showToast("Added to cart!");
        updateCartCount();
      });
  }

  if (e.target.classList.contains("add-to-wishlist")) {
    const pid = e.target.dataset.id;
    fetch(`/functions/wishlist.php?action=add&id=${pid}`)
      .then(res => res.json())
      .then(data => {
        showToast("Added to wishlist!");
        updateWishlistCount();
      });
  }
});

// =========Wishlist save==========
document.querySelectorAll('.add-to-wishlist').forEach(btn => {
  btn.addEventListener('click', () => {
    const productId = btn.dataset.id;

    fetch('/user/save_wishlist.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: `product_id=${productId}`
    })
    .then(res => res.json())
    .then(data => {
      alert(data.message);
    });
  });
});


// Save cart items
document.querySelectorAll('.add-to-cart').forEach(button => {
  button.addEventListener('click', () => {
    const productId = button.dataset.id;
    fetch('/user/save_cart.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: `product_id=${productId}&quantity=1`
    })
    .then(res => res.json())
    .then(data => {
      if (data.status === 'success') {
        updateCartCount(); // Optional function
        alert('Added to cart!');
      } else {
        alert(data.message);
      }
    });
  });
});


// ========= Mobile Navbar Toggle =========
const navToggle = document.querySelector("#nav-toggle");
const navMenu = document.querySelector("#nav-menu");

if (navToggle && navMenu) {
  navToggle.addEventListener("click", () => {
    navMenu.classList.toggle("hidden");
  });
}


// ========= Increase the quantity ==========
function updateQty(pid, qty) {
  if (qty < 1) return;
  fetch(`/functions/cart.php?action=update&id=${pid}&qty=${qty}`)
    .then(() => location.reload());
}

function removeItem(pid) {
  fetch(`/functions/cart.php?action=remove&id=${pid}`)
    .then(() => location.reload());
}

