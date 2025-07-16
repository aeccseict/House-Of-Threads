<?php
include('../db/connection.php');
session_start();

// Fetch latest 6 products
$latest = mysqli_query($conn, "SELECT * FROM products ORDER BY created_at DESC LIMIT 6");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>House of Threads</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
  <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet" />
  <link rel="stylesheet" href="/assets/css/style.css" />
  <script src="/assets/js/main.js" defer></script>
</head>
<body class="bg-gray-100">

  <!-- Navbar -->
  <nav class="navbar p-4 flex justify-between items-center shadow-md bg-white sticky top-0 z-50">
    <a href="index.php" class="text-xl font-bold text-pink-700">House of Threads</a>
    <div class="space-x-4">
      <a href="products.php">Products</a>
      <a href="cart.php">Cart <span id="cart-count" class="cart-count">0</span></a>
      <a href="wishlist.php">Wishlist</a>
      <?php if (isset($_SESSION['user_id'])): ?>
        <a href="user_dashboard.php">Dashboard</a>
      <?php else: ?>
        <a href="login.php">Login</a>
      <?php endif; ?>
    </div>
  </nav>

  <!-- Hero -->
  <section class="bg-pink-100 py-16 px-4 text-center" data-aos="fade-down">
    <h1 class="text-4xl md:text-5xl font-bold text-pink-700 mb-4">Elegant Sarees for Every Occasion</h1>
    <p class="text-lg text-gray-700 mb-6">Explore our premium collection crafted with love and tradition.</p>
    <a href="products.php" class="btn">Shop Now</a>
  </section>

  <!-- Categories -->
  <section class="max-w-6xl mx-auto py-12 px-4">
    <h2 class="text-2xl font-bold mb-6 text-pink-700">Featured Categories</h2>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
      <div class="bg-white p-4 rounded shadow text-center" data-aos="fade-up">
        <img src="/assets/images/category1.jpg" class="w-full h-48 object-cover rounded" />
        <h3 class="mt-3 font-semibold text-lg">Wedding Sarees</h3>
      </div>
      <div class="bg-white p-4 rounded shadow text-center" data-aos="fade-up" data-aos-delay="100">
        <img src="/assets/images/category2.jpg" class="w-full h-48 object-cover rounded" />
        <h3 class="mt-3 font-semibold text-lg">Party Wear</h3>
      </div>
      <div class="bg-white p-4 rounded shadow text-center" data-aos="fade-up" data-aos-delay="200">
        <img src="/assets/images/category3.jpg" class="w-full h-48 object-cover rounded" />
        <h3 class="mt-3 font-semibold text-lg">Casual Collections</h3>
      </div>
    </div>
  </section>

  <!-- Latest Products -->
  <section class="max-w-6xl mx-auto py-12 px-4">
    <h2 class="text-2xl font-bold mb-6 text-pink-700">New Arrivals</h2>
    <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-6">
      <?php while ($p = mysqli_fetch_assoc($latest)): ?>
        <div class="product-card fade-in" data-aos="zoom-in">
          <a href="product_details.php?id=<?= $p['id'] ?>">
            <img src="/assets/images/<?= $p['image'] ?>" class="product-image" alt="<?= htmlspecialchars($p['name']) ?>">
            <div class="p-4">
              <h3 class="font-semibold text-lg"><?= htmlspecialchars($p['name']) ?></h3>
              <p class="text-pink-600 font-bold mt-2">₹<?= number_format($p['price']) ?></p>
              <button class="add-to-cart mt-2 text-sm bg-pink-600 text-white px-3 py-1 rounded" data-id="<?= $p['id'] ?>">
                Add to Cart
              </button>
            </div>
          </a>
        </div>
      <?php endwhile; ?>
    </div>
  </section>

  <!-- Footer -->
  <footer class="footer">
    <p>&copy; <?= date("Y") ?> House of Threads. All rights reserved.</p>
  </footer>

  <script>
    AOS.init({ once: true, duration: 700 });
  </script>
</body>
</html>
