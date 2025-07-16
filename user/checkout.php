<?php
include('../db/connection.php');
session_start();

if (!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit;
}

$user_id = $_SESSION['user_id'];

// Fetch cart items
$cart_sql = "
  SELECT c.*, p.name, p.price, p.image 
  FROM cart c 
  JOIN products p ON c.product_id = p.id 
  WHERE c.user_id = $user_id
";
$res = mysqli_query($conn, $cart_sql);
$cart = [];
$total = 0;

while ($row = mysqli_fetch_assoc($res)) {
  $row['subtotal'] = $row['price'] * $row['quantity'];
  $total += $row['subtotal'];
  $cart[] = $row;
}

// Prevent empty checkout
if (empty($cart)) {
  header("Location: cart.php?empty=1");
  exit;
}
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Checkout - House of Threads</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="/assets/js/checkout.js" defer></script>
</head>
<body class="bg-gray-100 text-gray-800">

  <div class="max-w-4xl mx-auto px-4 py-10">
    <h2 class="text-2xl font-bold mb-6 text-pink-600">🧾 Checkout</h2>

    <form action="/functions/save_order.php" method="POST" class="bg-white p-6 rounded shadow space-y-6">
      <div class="grid md:grid-cols-2 gap-4">
        <input type="text" name="name" required placeholder="Full Name" class="border p-2 rounded w-full">
        <input type="email" name="email" required placeholder="Email" class="border p-2 rounded w-full">
        <input type="text" name="phone" required placeholder="Phone Number" class="border p-2 rounded w-full">
        <input type="text" name="address" required placeholder="Shipping Address" class="border p-2 rounded w-full">
      </div>

      <div class="border-t pt-4">
        <h3 class="font-semibold text-lg mb-2">Cart Summary</h3>
        <ul class="space-y-2">
          <?php foreach ($cart as $item): ?>
            <li class="flex justify-between">
              <span><?= $item['name'] ?> × <?= $item['quantity'] ?></span>
              <span>₹<?= number_format($item['subtotal']) ?></span>
            </li>
          <?php endforeach; ?>
        </ul>
        <div class="flex justify-between mt-2 font-bold">
          <span>Total:</span>
          <span id="checkout-total">₹<?= number_format($total) ?></span>
        </div>
      </div>

      <div class="border-t pt-4">
        <label class="block font-medium">Apply Promo Code</label>
        <div class="flex items-center space-x-2 mt-1">
          <input type="text" name="promo_code" id="promo-code" class="border p-2 rounded w-full max-w-xs">
          <button type="button" id="apply-promo" class="bg-pink-600 text-white px-4 py-2 rounded">Apply</button>
        </div>
        <div id="promo-result" class="mt-1 text-sm"></div>
        <input type="hidden" name="promo_applied" id="promo_applied" value="">
      </div>

      <input type="hidden" name="total" value="<?= $total ?>">
      <input type="hidden" name="user_id" value="<?= $user_id ?>">

      <button type="submit" class="mt-6 bg-pink-700 hover:bg-pink-800 text-white font-semibold px-6 py-3 rounded">
        ✅ Confirm Order
      </button>
    </form>
  </div>

</body>
</html>
