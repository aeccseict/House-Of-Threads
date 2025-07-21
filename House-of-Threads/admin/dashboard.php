<?php
include('../db/connection.php');
session_start();

if (!isset($_SESSION['admin_logged_in'])) {
  header("Location: login.php");
  exit;
}

// Get counts
$products = mysqli_query($conn, "SELECT COUNT(*) AS total FROM products");
$total_products = mysqli_fetch_assoc($products)['total'];

$users = mysqli_query($conn, "SELECT COUNT(DISTINCT email) AS total FROM users");
$total_users = mysqli_fetch_assoc($users)['total'];

$orders = mysqli_query($conn, "SELECT COUNT(*) AS total FROM orders");
$total_orders = mysqli_fetch_assoc($orders)['total'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Dashboard - House of Threads</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 font-sans">

  <header class="bg-pink-700 text-white p-4 shadow">
    <h1 class="text-xl font-semibold">Admin Dashboard</h1>
  </header>

  <main class="max-w-7xl mx-auto px-6 py-8">
    <h2 class="text-2xl font-bold text-pink-700 mb-6">Welcome Admin 🎉</h2>

    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
      
      <div class="bg-white shadow rounded-lg p-6 border-l-4 border-pink-600">
        <h3 class="text-xl font-semibold text-gray-700">🧵 Products</h3>
        <p class="text-3xl mt-2 font-bold text-pink-700"><?= $total_products ?></p>
        <a href="products.php" class="text-sm text-pink-600 hover:underline mt-1 inline-block">Manage Products</a>
      </div>

      <div class="bg-white shadow rounded-lg p-6 border-l-4 border-green-600">
        <h3 class="text-xl font-semibold text-gray-700">👤 Users</h3>
        <p class="text-3xl mt-2 font-bold text-green-700"><?= $total_users ?></p>
        <a href="users.php" class="text-sm text-green-600 hover:underline mt-1 inline-block">View Users</a>
      </div>

      <div class="bg-white shadow rounded-lg p-6 border-l-4 border-blue-600">
        <h3 class="text-xl font-semibold text-gray-700">📦 Orders</h3>
        <p class="text-3xl mt-2 font-bold text-blue-700"><?= $total_orders ?></p>
        <a href="orders.php" class="text-sm text-blue-600 hover:underline mt-1 inline-block">View Orders</a>
      </div>

    </div>

    <div class="mt-10">
      <a href="promocode.php" class="bg-yellow-500 text-white px-4 py-2 rounded hover:bg-yellow-600">🎟 Manage Promo Codes</a>
    </div>

  </main>

</body>
</html>
