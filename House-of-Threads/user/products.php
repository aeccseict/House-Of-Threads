<?php
include('../db/connection.php');
session_start();

// Handle filters
$search = $_GET['search'] ?? '';
$category = $_GET['category'] ?? '';
$sort = $_GET['sort'] ?? '';
$price = $_GET['price'] ?? 20000;

$filter = "WHERE price <= $price";

if ($category !== '') {
  $filter .= " AND category = '$category'";
}
if ($search !== '') {
  $filter .= " AND name LIKE '%$search%'";
}

$orderBy = "ORDER BY created_at DESC";
if ($sort === 'price_asc') $orderBy = "ORDER BY price ASC";
if ($sort === 'price_desc') $orderBy = "ORDER BY price DESC";

$query = "SELECT * FROM products $filter $orderBy";
$products = mysqli_query($conn, $query);
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


    <!-- Page Layout: Sidebar + Content -->
    <div class="max-w-7xl mx-auto px-4 py-8 flex flex-col md:flex-row gap-6">
      
      <!-- Sidebar: Filters -->
      <aside class="w-full md:w-1/4 bg-white p-4 rounded shadow">
        <h3 class="text-xl font-semibold text-pink-700 mb-4">Filters</h3>
        
        <form method="GET" class="space-y-4">
          <!-- Search -->
          <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="🔍 Search..."
                class="p-2 border rounded w-full focus:ring-pink-500 focus:border-pink-500">

          <!-- Category -->
          <select name="category" class="p-2 border rounded w-full focus:ring-pink-500 focus:border-pink-500">
            <option value="">All Categories</option>
            <option value="Wedding" <?= $category === 'Wedding' ? 'selected' : '' ?>>Wedding</option>
            <option value="Party" <?= $category === 'Party' ? 'selected' : '' ?>>Party</option>
            <option value="Casual" <?= $category === 'Casual' ? 'selected' : '' ?>>Casual</option>
          </select>

          <!-- Sort -->
          <select name="sort" class="p-2 border rounded w-full focus:ring-pink-500 focus:border-pink-500">
            <option value="">Newest</option>
            <option value="price_asc" <?= $sort === 'price_asc' ? 'selected' : '' ?>>Price: Low to High</option>
            <option value="price_desc" <?= $sort === 'price_desc' ? 'selected' : '' ?>>Price: High to Low</option>
          </select>

          <!-- Price Slider -->
          <div class="w-full">
            <label for="priceRange" class="block text-sm font-medium text-gray-700 mb-1">
              Max Price: ₹<span id="rangeValue"><?= $price ?></span>
            </label>
            <input type="range" min="100" max="20000" step="100" name="price" id="priceRange"
                  value="<?= $price ?>" class="w-full accent-pink-600">
          </div>

          <button type="submit" class="w-full bg-pink-600 text-white py-2 rounded hover:bg-pink-700">
            Apply Filters
          </button>
        </form>
      </aside>

      <!-- Product Grid -->
      <main class="w-full md:w-3/4">
        <h2 class="text-2xl font-bold text-pink-700 mb-6">Products</h2>

        <?php if (mysqli_num_rows($products) === 0): ?>
          <p class="text-gray-600">No products found.</p>
        <?php else: ?>
          <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php while ($p = mysqli_fetch_assoc($products)): ?>
              <div class="bg-white rounded shadow p-4">
                <a href="product_details.php?id=<?= $p['id'] ?>">
                  <img src="../assets/images/<?= $p['image'] ?>" alt="<?= $p['name'] ?>" class="w-full h-48 object-cover rounded">
                  <h3 class="mt-2 font-semibold text-lg"><?= htmlspecialchars($p['name']) ?></h3>
                  <p class="text-pink-600 font-bold mt-1">₹<?= number_format($p['price']) ?></p>
                  <div class="mt-2 flex gap-2">
                    <!-- Add to Cart -->
                    <form method="POST" action="save_cart.php">
                      <input type="hidden" name="product_id" value="<?= $p['id'] ?>">
                      <button type="submit"
                              class="bg-pink-600 text-white px-3 py-1 rounded text-sm hover:bg-pink-700">
                        Add to Cart
                      </button>
                    </form>

                    <!-- Wishlist -->
                    <form method="POST" action="save_wishlist.php">
                      <input type="hidden" name="product_id" value="<?= $p['id'] ?>">
                      <button type="submit"
                              class="border border-pink-600 text-pink-600 px-3 py-1 rounded text-sm hover:bg-pink-100">
                        ❤
                      </button>
                    </form>
                  </div>
                </a>
              </div>
            <?php endwhile; ?>
          </div>
        <?php endif; ?>
      </main>

    </div>

  </div>

  <!-- Toast Notification Container -->
  <div id="toast-container" class="fixed bottom-4 right-4 space-y-2 z-50"></div>


  <script>
    AOS.init({ once: true });

    const priceSlider = document.getElementById('priceRange');
    const rangeValue = document.getElementById('rangeValue');

    priceSlider?.addEventListener('input', function () {
      rangeValue.innerText = this.value;
    });
    
      // AJAX "Add to Cart" Handler
      document.querySelectorAll('form[action="save_cart.php"]').forEach(form => {
        form.addEventListener('submit', function (e) {
          e.preventDefault();

          const formData = new FormData(this);
          fetch('save_cart.php', {
            method: 'POST',
            body: formData
          })
          .then(res => res.json())
          .then(data => {
            showToast(data.message, data.status);
            updateCartCount(); // Optional
          })
          .catch(() => showToast('Something went wrong', 'error'));
        });
      });

      // Show Toast Function
      function showToast(message, type = 'success') {
        const toast = document.createElement('div');
        toast.className = `p-4 rounded shadow text-white text-sm animate-slide-in bg-${type === 'success' ? 'green' : 'red'}-600`;
        toast.innerText = message;

        document.getElementById('toast-container').appendChild(toast);

        setTimeout(() => {
          toast.classList.add('opacity-0');
          setTimeout(() => toast.remove(), 500);
        }, 3000);
      }

      // (Optional) Update Cart Count in Navbar
      function updateCartCount() {
        fetch('get_cart_count.php') // You need to implement this
          .then(res => res.json())
          .then(data => {
            document.getElementById('cart-count').innerText = data.count || 0;
          });
      }
    </script>

    <style>
      /* Slide In Animation */
      .animate-slide-in {
        animation: slide-in 0.3s ease-out forwards;
      }

      @keyframes slide-in {
        from {
          transform: translateX(100%);
          opacity: 0;
        }
        to {
          transform: translateX(0);
          opacity: 1;
        }
      }
    </style>


</body>
</html>
