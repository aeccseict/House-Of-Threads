/user/checkout.php
<?php include('../db/connection.php'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Checkout - SareeStyle</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="../assets/css/style.css" />
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 text-gray-800 font-sans">

  <!-- Navbar -->
  <nav class="flex justify-between items-center p-4 bg-pink-100 shadow-md">
    <h1 class="text-2xl font-bold text-pink-700">SareeStyle</h1>
    <ul class="flex space-x-4 text-pink-700 font-medium">
      <li><a href="index.php">Home</a></li>
      <li><a href="cart.php">Cart</a></li>
    </ul>
  </nav>

  <!-- Checkout Section -->
  <section class="max-w-5xl mx-auto p-6 grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
    
    <!-- Shipping Details -->
    <div class="bg-white p-6 rounded shadow">
      <h2 class="text-2xl font-bold text-pink-700 mb-4">Shipping Information</h2>
      <form id="checkoutForm">
        <input type="text" placeholder="Full Name" required class="w-full border px-3 py-2 mb-3 rounded" id="name" />
        <input type="email" placeholder="Email Address" required class="w-full border px-3 py-2 mb-3 rounded" id="email" />
        <input type="text" placeholder="Phone Number" required class="w-full border px-3 py-2 mb-3 rounded" id="phone" />
        <textarea placeholder="Shipping Address" required class="w-full border px-3 py-2 mb-3 rounded" id="address"></textarea>
        
        <!-- Add Promo Code Section -->
        <div class="mb-4">
          <label class="block font-medium text-sm text-gray-700 mb-1">Promo Code</label>
          <div class="flex space-x-2">
            <input id="promoCode" class="flex-1 px-3 py-2 border rounded" placeholder="Enter code" />
            <button type="button" onclick="applyPromo()" class="bg-pink-600 text-white px-4 py-2 rounded hover:bg-pink-700">Apply</button>
          </div>
          <p id="promoMsg" class="text-sm mt-1"></p>
        </div>
      </form>
    </div>

    


    <!-- Order Summary -->
    <div class="bg-white p-6 rounded shadow">
      <h2 class="text-2xl font-bold text-pink-700 mb-4">Order Summary</h2>
      <div id="summaryItems" class="space-y-3"></div>
      <div class="mt-4 text-right text-lg font-semibold">
        Total: ₹<span id="orderTotal">0</span>
      </div>
      <button onclick="placeOrder()" class="mt-6 w-full bg-green-600 text-white py-2 rounded hover:bg-green-700">Place Order</button>
    </div>
  </section>

  <script>
    let discount = 0;

    function applyPromo() {
      const code = document.getElementById('promoCode').value;
      const total = parseInt(document.getElementById('orderTotal').innerText);

      if (!code) {
        document.getElementById('promoMsg').innerText = "Please enter a code.";
        return;
      }

      fetch("../functions/validate_promo.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: new URLSearchParams({ code, total })
      })
      .then(res => res.json())
      .then(data => {
        const msg = document.getElementById('promoMsg');
        if (data.status === "valid") {
          discount = data.discount;
          document.getElementById('orderTotal').innerText = data.newTotal;
          msg.innerText = data.message;
          msg.className = "text-green-600 text-sm mt-1";
        } else {
          discount = 0;
          msg.innerText = data.message;
          msg.className = "text-red-600 text-sm mt-1";
        }
      });
    }

    let cart = JSON.parse(localStorage.getItem("cart")) || [];
    const summary = document.getElementById("summaryItems");
    const totalDisplay = document.getElementById("orderTotal");

    function renderSummary() {
      summary.innerHTML = "";
      let total = 0;

      if (cart.length === 0) {
        summary.innerHTML = `<p class="text-gray-500">Your cart is empty.</p>`;
        document.querySelector("button").disabled = true;
        return;
      }

      cart.forEach(item => {
        total += item.price * item.qty;
        const div = document.createElement("div");
        div.innerHTML = `
          <div class="flex justify-between">
            <span>${item.name} × ${item.qty}</span>
            <span>₹${item.price * item.qty}</span>
          </div>
        `;
        summary.appendChild(div);
      });

      const promo = document.getElementById("promo").value.trim();
      if (promo === "SAVE10") {
        total = total * 0.9;
      }

      totalDisplay.innerText = Math.round(total);
    }

    function placeOrder() {
      const name = document.getElementById("name").value.trim();
      const email = document.getElementById("email").value.trim();
      const phone = document.getElementById("phone").value.trim();
      const address = document.getElementById("address").value.trim();
      const promo = document.getElementById("promo").value.trim();

      if (!name || !email || !phone || !address) {
        alert("Please fill all required fields.");
        return;
      }

      const order = {
        name, email, phone, address,
        promo,
        cart,
        total: document.getElementById("orderTotal").innerText,
        date: new Date().toLocaleString()
      };

      // Save to localStorage for now (will send to DB later)
      let orders = JSON.parse(localStorage.getItem("orders")) || [];
      orders.push(order);
      localStorage.setItem("orders", JSON.stringify(orders));
      localStorage.removeItem("cart");

      alert("Order placed successfully!");
      window.location.href = "index.php";
    }

    // Update total on promo change
    document.getElementById("promo").addEventListener("input", renderSummary);

    renderSummary();
  </script>
</body>
</html>
