<!-- /user/products.php -->
<?php include('../db/connection.php'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>All Sarees - SareeStyle</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="../assets/css/style.css" />
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://unpkg.com/aos@2.3.4/dist/aos.js"></script>
  <link href="https://unpkg.com/aos@2.3.4/dist/aos.css" rel="stylesheet">
</head>
<body class="bg-gray-50">

  <!-- Navbar -->
  <nav class="flex justify-between items-center p-4 bg-pink-100 shadow-md">
    <h1 class="text-2xl font-bold text-pink-700">SareeStyle</h1>
    <ul class="flex space-x-4 text-pink-700 font-medium">
      <li><a href="index.php">Home</a></li>
      <li><a href="wishlist.php">Wishlist</a></li>
      <li><a href="cart.php">Cart</a></li>
      <li><a href="login.php">Login</a></li>
    </ul>
  </nav>

  <!-- Filters -->
  <div class="p-4 bg-white shadow-sm">
    <label class="mr-2 font-semibold">Category:</label>
    <select id="filterCategory" class="border px-2 py-1 rounded">
      <option value="All">All</option>
      <option value="Silk">Silk</option>
      <option value="Cotton">Cotton</option>
      <option value="Banarasi">Banarasi</option>
    </select>
  </div>

  <!-- Product Grid -->
  <section class="p-4 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6" id="productGrid">
    <!-- JS dynamically adds products here -->
  </section>

  <footer class="bg-pink-100 text-center py-4 mt-10 text-pink-700">
    &copy; 2025 SareeStyle. All rights reserved.
  </footer>

  <script src="../assets/js/products.js"></script>
  <script>AOS.init();</script>
</body>
</html>


