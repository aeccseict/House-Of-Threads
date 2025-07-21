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

if (empty($cart)) {
  header("Location: cart.php?empty=1");
  exit;
}

// Handle promo code
$promo_success = false;
$discount = 0;
$discount_msg = "";
$promo_code = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['apply_promo'])) {
  $promo_code = strtoupper(trim(mysqli_real_escape_string($conn, $_POST['promo_code'])));

  $check = mysqli_query($conn, "
    SELECT * FROM promo_codes 
    WHERE code = '$promo_code' 
      AND (expires_at IS NULL OR expires_at >= NOW()) 
      AND (usage_limit IS NULL OR used_count < usage_limit)
  ");

  if (mysqli_num_rows($check) > 0) {
    $promo = mysqli_fetch_assoc($check);
    $discount = round(($total * $promo['discount_percentage']) / 100);
    $total -= $discount;
    $promo_success = true;
    $discount_msg = "Promo applied! ₹$discount off ({$promo['discount_percentage']}%).";
  } else {
    $discount_msg = "<span class='text-red-600'>Invalid or expired promo code.</span>";
  }
}
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Checkout - House of Threads</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 text-gray-800">

<div class="max-w-4xl mx-auto px-4 py-10">
  <h2 class="text-2xl font-bold mb-6 text-pink-600">📦 Checkout</h2>

  <form method="POST" class="bg-white p-6 rounded shadow space-y-6">
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
        <input type="text" name="promo_code" class="border p-2 rounded w-full max-w-xs"
               value="<?= htmlspecialchars($promo_code) ?>">
        <button name="apply_promo" value="1" class="bg-pink-600 text-white px-4 py-2 rounded">Apply</button>
      </div>
      <div class="mt-1 text-sm text-green-600">
        <?= $discount_msg ?>
      </div>
    </div>

    <input type="hidden" name="discount" value="<?= $discount ?>">
    <input type="hidden" name="final_total" value="<?= $total ?>">
    <input type="hidden" name="promo_used" value="<?= $promo_success ? $promo_code : '' ?>">

    <button type="submit" name="confirm_order" class="mt-6 bg-pink-700 hover:bg-pink-800 text-white font-semibold px-6 py-3 rounded">
      ✅ Confirm Order
    </button>
  </form>

  <?php
  if (isset($_POST['confirm_order'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $final_total = (int)$_POST['final_total'];
    $promo = mysqli_real_escape_string($conn, $_POST['promo_code'] ?? null);
    $promo_used = $_POST['promo_used'];

    $insert_order = mysqli_query($conn, "
      INSERT INTO orders (user_id, total, name, email, phone, address, promo_code, status, created_at) 
      VALUES ($user_id, $final_total, '$name', '$email', '$phone', '$promo', '$address', 'Pending', NOW())
    ");

    if ($insert_order) {
      $order_id = mysqli_insert_id($conn);

      foreach ($cart as $item) {
        $pid = $item['product_id'];
        $qty = $item['quantity'];
        $price = $item['price'];
        mysqli_query($conn, "INSERT INTO order_items (order_id, product_id, quantity, price) VALUES ($order_id, $pid, $qty, $price)");
      }

      mysqli_query($conn, "DELETE FROM cart WHERE user_id = $user_id");

      // update promo usage count
      if (!empty($promo_used)) {
        mysqli_query($conn, "UPDATE promo_codes SET used_count = used_count + 1 WHERE code = '$promo_used'");
      }

      echo "<p class='mt-4 text-green-700 font-medium'>🎉 Order placed successfully! Thank you for shopping.</p>";
    } else {
      echo "<p class='mt-4 text-red-600 font-medium'>❌ Failed to place order. Please try again.</p>";
    }
  }
  ?>

</div>

</body>
</html>
