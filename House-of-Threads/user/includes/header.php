<?php
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}
?>

<!-- Header -->
<nav class="navbar py-6 px-4 flex justify-between items-center shadow-md bg-white sticky top-0 z-50 h-20">
  <div class="flex items-center space-x-4">
    <?php if (basename($_SERVER['PHP_SELF']) !== 'index.php'): ?>
      <button onclick="window.history.back()" class="text-pink-600 hover:text-pink-800 font-bold text-xl">&larr;</button>
    <?php endif; ?>
    <a href="index.php" class="text-3xl font-extrabold text-pink-700">House of Threads</a>
  </div>

  <div class="space-x-6 text-base font-medium">
    <a href="products.php" class="hover:text-pink-600">Products</a>
    <a href="cart.php" class="hover:text-pink-600">Cart <span id="cart-count" class="cart-count">0</span></a>
    <a href="wishlist.php" class="hover:text-pink-600">Wishlist</a>
    <?php if (isset($_SESSION['user_id'])): ?>
      <a href="user_dashboard.php" class="hover:text-pink-600">Dashboard</a>
      <a href="logout.php" class="text-red-600 hover:underline">Logout</a>
    <?php else: ?>
      <a href="login.php" class="hover:text-pink-600">Login</a>
    <?php endif; ?>
  </div>
</nav>
