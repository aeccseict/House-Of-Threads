<?php
include('../db/connection.php');
session_start();

if (!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit;
}

$user_id = $_SESSION['user_id'];

// Fetch cart items with product details
$query = "
  SELECT cart.*, products.name, products.image, products.price 
  FROM cart 
  JOIN products ON cart.product_id = products.id 
  WHERE cart.user_id = $user_id
";
$result = mysqli_query($conn, $query);

// Calculate total
$total = 0;
$items = [];

while ($row = mysqli_fetch_assoc($result)) {
  $subtotal = $row['price'] * $row['quantity'];
  $total += $subtotal;
  $items[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Your Cart - House of Threads</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    function updateQty(pid, qty) {
      fetch(`/functions/cart.php?action=update&id=${pid}&qty=${qty}`)
        .then(res => res.json())
        .then(data => {
          if (data.status === 'updated') {
            location.reload();
          }
        });
    }
    function removeItem(pid) {
      fetch(`/functions/cart.php?action=remove&id=${pid}`)
        .then(res => res.json())
        .then(() => location.reload());
    }
  </script>
</head>
<body class="bg-gray-100 text-gray-800">

  <header class="bg-white shadow p-4">
    <div class="max-w-7xl mx-auto flex justify-between items-center">
      <h1 class="text-3xl font-bold text-pink-600">🛒 Your Cart</h1>
      <a href="products.php" class="text-pink-600 hover:underline">← Continue Shopping</a>
    </div>
  </header>

  <main class="max-w-6xl mx-auto py-8 px-4">
    <?php if (count($items) === 0): ?>
      <div class="text-center text-lg text-gray-500 py-16">
        Your cart is empty. <br>
        <a href="products.php" class="text-pink-600 underline">Browse sarees →</a>
      </div>
    <?php else: ?>
      <!-- Cart Table -->
      <div class="bg-white shadow-md rounded overflow-x-auto">
        <table class="min-w-full text-sm">
          <thead class="bg-pink-100 text-pink-700">
            <tr>
              <th class="px-4 py-3 text-left">Product</th>
              <th class="px-4 py-3 text-left">Image</th>
              <th class="px-4 py-3 text-left">Price</th>
              <th class="px-4 py-3 text-left">Qty</th>
              <th class="px-4 py-3 text-left">Subtotal</th>
              <th class="px-4 py-3 text-left">Action</th>
            </tr>
          </thead>
          <tbody class="text-gray-700">
            <?php foreach ($items as $item): ?>
              <tr class="border-t hover:bg-gray-50 transition">
                <td class="px-4 py-2 font-semibold"><?= htmlspecialchars($item['name']) ?></td>
                <td class="px-4 py-2">
                  <img src="/assets/images/<?= $item['image'] ?>" alt="<?= htmlspecialchars($item['name']) ?>"
                       class="w-20 h-20 object-cover rounded border" />
                </td>
                <td class="px-4 py-2">₹<?= number_format($item['price']) ?></td>
                <td class="p-2 text-center">
                  <input type="number" min="1" value="<?= $item['quantity'] ?>"
                         onchange="updateQty(<?= $item['product_id'] ?>, this.value)"
                         class="w-16 text-center border rounded px-2 py-1 focus:outline-none focus:ring-2 focus:ring-pink-500">
                </td>
                <td class="px-4 py-2 font-bold text-pink-600">
                  ₹<?= number_format($item['price'] * $item['quantity']) ?>
                </td>
                <td class="px-4 py-2">
                  <button onclick="removeItem(<?= $item['product_id'] ?>)"
                          class="text-red-600 hover:text-red-800 font-medium">
                    🗑 Remove
                  </button>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>

      <div class="mt-6 flex justify-between items-center">
        <h2 class="text-xl font-semibold">Total: ₹<?= number_format($total) ?></h2>
        <a href="checkout.php" class="bg-pink-600 text-white px-6 py-3 rounded hover:bg-pink-700 font-semibold">
          Proceed to Checkout →
        </a>
      </div>
    <?php endif; ?>
  </main>

</body>
</html>