<?php
include('../db/connection.php');
session_start();

if (!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit;
}

$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['user_name'];

// Fetch recent orders
$orders = mysqli_query($conn, "SELECT * FROM orders WHERE user_id = $user_id ORDER BY created_at DESC LIMIT 5");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>User Dashboard - House of Threads</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 text-gray-800">

  <!-- Navbar -->
  <nav class="bg-white shadow p-4 flex justify-between items-center">
    <a href="index.php" class="text-xl font-bold text-pink-700">House of Threads</a>
    <div class="space-x-4">
      <a href="cart.php">Cart</a>
      <a href="wishlist.php">Wishlist</a>
      <a href="logout.php" class="text-red-600 hover:underline">Logout</a>
    </div>
  </nav>

  <!-- Dashboard -->
  <div class="max-w-4xl mx-auto mt-10 p-6 bg-white shadow rounded">
    <h2 class="text-2xl font-bold mb-4">Welcome, <?= htmlspecialchars($user_name) ?>!</h2>

    <h3 class="text-xl font-semibold mb-2">Your Recent Orders</h3>
    <?php if (mysqli_num_rows($orders) == 0): ?>
      <p class="text-gray-500">You have no orders yet.</p>
    <?php else: ?>
      <div class="overflow-x-auto">
        <table class="min-w-full table-auto border">
          <thead>
            <tr class="bg-gray-200">
              <th class="text-left p-2 border">Order ID</th>
              <th class="text-left p-2 border">Amount</th>
              <th class="text-left p-2 border">Date</th>
              <th class="text-left p-2 border">Status</th>
            </tr>
          </thead>
          <tbody>
            <?php while ($order = mysqli_fetch_assoc($orders)): ?>
              <tr class="hover:bg-gray-50">
                <td class="p-2 border">#<?= $order['id'] ?></td>
                <td class="p-2 border">₹<?= number_format($order['total']) ?></td>
                <td class="p-2 border"><?= date('d M Y', strtotime($order['created_at'])) ?></td>
                <td class="p-2 border">
                  <?php
                    $status = $order['status'];
                    $color = $status === 'Shipped' ? 'green' : ($status === 'Cancelled' ? 'red' : 'yellow');
                  ?>
                  <span class="text-<?= $color ?>-600 font-semibold"><?= $status ?></span>
                </td>
              </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
      </div>
    <?php endif; ?>
  </div>

</body>
</html>
