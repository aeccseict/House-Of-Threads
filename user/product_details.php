<?php
include('../db/connection.php');
session_start();

$product_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($product_id <= 0) {
  die("Invalid product.");
}

// Fetch product
$query = mysqli_query($conn, "SELECT * FROM products WHERE id = $product_id");
if (mysqli_num_rows($query) == 0) {
  die("Product not found.");
}
$product = mysqli_fetch_assoc($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title><?= htmlspecialchars($product['name']) ?> - House of Threads</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="/assets/js/main.js" defer></script>
</head>
<body class="bg-gray-100 text-gray-800">

  <!-- Navbar -->
  <nav class="navbar p-4 flex justify-between items-center shadow-md bg-white">
    <a href="index.php" class="text-xl font-bold text-pink-700">House of Threads</a>
    <div class="space-x-4">
      <a href="products.php">Products</a>
      <a href="cart.php">Cart <span id="cart-count">0</span></a>
      <a href="wishlist.php">Wishlist</a>
      <?php if (isset($_SESSION['user_id'])): ?>
        <a href="user_dashboard.php">Dashboard</a>
      <?php else: ?>
        <a href="login.php">Login</a>
      <?php endif; ?>
    </div>
  </nav>

  <!-- Product Details -->
  <main class="max-w-6xl mx-auto py-10 px-4">
    <div class="grid md:grid-cols-2 gap-8 bg-white p-6 rounded shadow">
      <div>
        <img src="/assets/images/<?= $product['image'] ?>" alt="<?= htmlspecialchars($product['name']) ?>"
             class="w-full h-auto rounded shadow object-cover">
      </div>
      <div>
        <h1 class="text-3xl font-bold text-pink-700 mb-2"><?= htmlspecialchars($product['name']) ?></h1>
        <p class="text-xl text-gray-800 font-semibold mb-4">₹<?= number_format($product['price']) ?></p>

        <div class="flex space-x-3 mb-6">
          <button class="add-to-cart bg-pink-600 hover:bg-pink-700 text-white px-4 py-2 rounded"
                  data-id="<?= $product['id'] ?>">
            Add to Cart
          </button>
          <button class="add-to-wishlist border border-pink-600 text-pink-600 px-4 py-2 rounded"
                  data-id="<?= $product['id'] ?>">
            ❤ Wishlist
          </button>
        </div>

        <p class="text-gray-700 leading-relaxed">
          <?= nl2br(htmlspecialchars($product['description'] ?? "Elegant designer saree perfect for all occasions.")) ?>
        </p>
      </div>
    </div>
  </main>

</body>
</html>
