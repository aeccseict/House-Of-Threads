<?php
include('../db/connection.php');
session_start();

// Handle filters
$search = $_GET['search'] ?? '';
$category = $_GET['category'] ?? '';
$sort = $_GET['sort'] ?? '';

$sql = "SELECT * FROM products WHERE 1";

if ($search !== '') {
  $search = mysqli_real_escape_string($conn, $search);
  $sql .= " AND name LIKE '%$search%'";
}

if ($category !== '') {
  $category = mysqli_real_escape_string($conn, $category);
  $sql .= " AND category = '$category'";
}

if ($sort === 'price_asc') {
  $sql .= " ORDER BY price ASC";
} elseif ($sort === 'price_desc') {
  $sql .= " ORDER BY price DESC";
} else {
  $sql .= " ORDER BY created_at DESC";
}

$products = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Products - House of Threads</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
  <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet" />
  <script src="/assets/js/main.js" defer></script>
</head>
<body class="bg-gray-100">

  <!-- Navbar -->
  <nav class="navbar p-4 flex justify-between items-center bg-white shadow sticky top-0 z-50">
    <a href="index.php" class="text-xl font-bold text-pink-700">House of Threads</a>
    <div class="space-x-4">
      <a href="cart.php">Cart <span id="cart-count">0</span></a>
      <a href="wishlist.php">Wishlist</a>
      <?php if (isset($_SESSION['user_id'])): ?>
        <a href="user_dashboard.php">Dashboard</a>
      <?php else: ?>
        <a href="login.php">Login</a>
      <?php endif; ?>
    </div>
  </nav>

  <div class="max-w-7xl mx-auto py-10 px-4">
    <h2 class="text-3xl font-bold text-pink-700 mb-4">All Sarees</h2>

    <!-- Filters -->
    <form method="GET" class="flex flex-wrap gap-4 mb-6">
      <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Search..."
             class="p-2 border rounded w-full sm:w-1/3">

      <select name="category" class="p-2 border rounded w-full sm:w-1/4">
        <option value="">All Categories</option>
        <option value="Wedding" <?= $category === 'Wedding' ? 'selected' : '' ?>>Wedding</option>
        <option value="Party" <?= $category === 'Party' ? 'selected' : '' ?>>Party</option>
        <option value="Casual" <?= $category === 'Casual' ? 'selected' : '' ?>>Casual</option>
      </select>

      <select name="sort" class="p-2 border rounded w-full sm:w-1/4">
        <option value="">Newest</option>
        <option value="price_asc" <?= $sort === 'price_asc' ? 'selected' : '' ?>>Price: Low to High</option>
        <option value="price_desc" <?= $sort === 'price_desc' ? 'selected' : '' ?>>Price: High to Low</option>
      </select>

      <button type="submit" class="bg-pink-600 text-white px-4 py-2 rounded">Filter</button>
    </form>

    <!-- Product Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
      <?php if (mysqli_num_rows($products) == 0): ?>
        <p class="text-gray-500">No products found.</p>
      <?php else: ?>
        <?php while ($p = mysqli_fetch_assoc($products)): ?>
          <div class="bg-white rounded shadow overflow-hidden hover:shadow-lg transition" data-aos="fade-up">
            <a href="product_details.php?id=<?= $p['id'] ?>">
              <img src="/assets/images/<?= $p['image'] ?>" alt="<?= $p['name'] ?>" class="w-full h-52 object-cover">
            </a>
            <div class="p-4">
              <h3 class="font-semibold text-lg"><?= htmlspecialchars($p['name']) ?></h3>
              <p class="text-pink-600 font-bold mt-1">₹<?= number_format($p['price']) ?></p>
              <div class="mt-2 flex space-x-2">
                <button class="add-to-cart text-sm bg-pink-600 text-white px-3 py-1 rounded" data-id="<?= $p['id'] ?>">
                  Add to Cart
                </button>
                <button class="add-to-wishlist text-sm border border-pink-600 text-pink-600 px-3 py-1 rounded"
                        data-id="<?= $p['id'] ?>">❤
                </button>
              </div>
            </div>
          </div>
        <?php endwhile; ?>
      <?php endif; ?>
    </div>
  </div>

  <script>
    AOS.init({ once: true });
  </script>

</body>
</html>
