<?php
include('../db/connection.php');
session_start();

if (!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit;
}

$user_id = $_SESSION['user_id'];

// Fetch wishlist items
$query = mysqli_query($conn, "
  SELECT p.*, w.id AS wishlist_id
  FROM wishlist w
  JOIN products p ON w.product_id = p.id
  WHERE w.user_id = $user_id
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>My Wishlist - House of Threads</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="/assets/js/main.js" defer></script>
</head>
<body class="bg-gray-100 text-gray-800">

<!-- Navbar -->
<nav class="bg-white shadow p-4 flex justify-between items-center">
  <a href="index.php" class="text-xl font-bold text-pink-700">House of Threads</a>
  <div class="space-x-4">
    <a href="products.php">Products</a>
    <a href="cart.php">Cart</a>
    <a href="user_dashboard.php">Dashboard</a>
    <a href="logout.php" class="text-red-600 hover:underline">Logout</a>
  </div>
</nav>

<!-- Wishlist -->
<div class="max-w-6xl mx-auto py-10 px-4">
  <h2 class="text-2xl font-bold text-pink-700 mb-6">My Wishlist</h2>

  <?php if (mysqli_num_rows($query) == 0): ?>
    <p class="text-gray-500">Your wishlist is empty.</p>
  <?php else: ?>
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
      <?php while ($product = mysqli_fetch_assoc($query)): ?>
        <div class="bg-white rounded shadow p-4">
          <img src="/assets/images/<?= $product['image'] ?>" alt="<?= $product['name'] ?>" class="w-full h-52 object-cover rounded">
          <h3 class="font-semibold text-lg mt-2"><?= htmlspecialchars($product['name']) ?></h3>
          <p class="text-pink-600 font-bold">₹<?= number_format($product['price']) ?></p>
          <div class="flex space-x-2 mt-3">
            <button class="add-to-cart bg-pink-600 text-white px-3 py-1 rounded text-sm"
                    data-id="<?= $product['id'] ?>">Add to Cart</button>
            <form method="POST" action="remove_wishlist.php">
              <input type="hidden" name="wishlist_id" value="<?= $product['wishlist_id'] ?>">
              <button type="submit" class="text-red-600 border border-red-600 px-3 py-1 rounded text-sm">Remove</button>
            </form>
          </div>
        </div>
      <?php endwhile; ?>
    </div>
  <?php endif; ?>
</div>

</body>
</html>
