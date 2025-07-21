<?php
include('../db/connection.php');
session_start();

if (!isset($_SESSION['admin_logged_in'])) {
  header("Location: login.php");
  exit;
}

// Handle create
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_product'])) {
  $name = $_POST['name'];
  $price = $_POST['price'];
  $category = $_POST['category'];
  $image = $_FILES['image'];

  $imageName = uniqid() . '_' . $image['name'];
  $imagePath = '../assets/images/' . $imageName;
  move_uploaded_file($image['tmp_name'], $imagePath);

  mysqli_query($conn, "INSERT INTO products (name, price, category, image) VALUES ('$name', '$price', '$category', '$imageName')");
  header("Location: products.php?success=1");
  exit;
}

// Handle delete
if (isset($_GET['delete'])) {
  $id = (int)$_GET['delete'];
  mysqli_query($conn, "DELETE FROM products WHERE id = $id");
  header("Location: products.php?deleted=1");
  exit;
}

// Fetch all products
$result = mysqli_query($conn, "SELECT * FROM products ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin - Products</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">

  <header class="bg-pink-700 text-white p-4 shadow">
    <div class="max-w-7xl mx-auto flex justify-between items-center">
      <h1 class="text-xl font-semibold">Manage Products</h1>
      <a href="logout.php" class="text-white hover:underline">Logout</a>
    </div>
  </header>

  <main class="max-w-6xl mx-auto px-6 py-8">
    <h2 class="text-xl font-bold text-pink-700 mb-4">Add New Product</h2>

    <?php if (isset($_GET['success'])): ?>
      <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-2 mb-4 rounded">Product added!</div>
    <?php elseif (isset($_GET['deleted'])): ?>
      <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-2 mb-4 rounded">Product deleted!</div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data" class="bg-white shadow rounded p-6 mb-8 grid grid-cols-1 sm:grid-cols-2 gap-6">
      <input type="hidden" name="add_product" value="1" />
      <div>
        <label class="block mb-1 font-medium">Product Name</label>
        <input type="text" name="name" required class="w-full border rounded px-3 py-2" />
      </div>
      <div>
        <label class="block mb-1 font-medium">Price (₹)</label>
        <input type="number" name="price" required class="w-full border rounded px-3 py-2" />
      </div>
      <div>
        <label class="block mb-1 font-medium">Category</label>
        <input type="text" name="category" required class="w-full border rounded px-3 py-2" />
      </div>
      <div>
        <label class="block mb-1 font-medium">Image</label>
        <input type="file" name="image" accept="image/*" required class="w-full" />
      </div>
      <div class="col-span-full">
        <button type="submit" class="bg-pink-600 text-white px-4 py-2 rounded hover:bg-pink-700">➕ Add Product</button>
      </div>
    </form>

    <h2 class="text-xl font-bold text-pink-700 mb-4">All Products</h2>

    <div class="overflow-x-auto bg-white shadow rounded">
      <table class="min-w-full text-sm">
        <thead class="bg-pink-100 text-pink-800">
          <tr>
            <th class="px-4 py-3">Image</th>
            <th class="px-4 py-3">Name</th>
            <th class="px-4 py-3">Price</th>
            <th class="px-4 py-3">Category</th>
            <th class="px-4 py-3">Action</th>
          </tr>
        </thead>
        <tbody class="text-gray-700">
          <?php while ($row = mysqli_fetch_assoc($result)): ?>
            <tr class="border-t">
              <td class="px-4 py-2">
                <img src="../assets/images/<?= $row['image'] ?>" alt="Product" class="w-16 h-16 object-cover rounded">
              </td>
              <td class="px-4 py-2"><?= htmlspecialchars($row['name']) ?></td>
              <td class="px-4 py-2">₹<?= $row['price'] ?></td>
              <td class="px-4 py-2"><?= $row['category'] ?></td>
              <td class="px-4 py-2">
                <!-- Edit can be added later -->
                <a href="products.php?delete=<?= $row['id'] ?>" onclick="return confirm('Delete this product?')" class="text-red-600 hover:underline">🗑 Delete</a>
              </td>
            </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>

  </main>
</body>
</html>
