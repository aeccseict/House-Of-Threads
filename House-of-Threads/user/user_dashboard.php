<?php
include('../db/connection.php');
session_start();

if (!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit;
}

$user_id = $_SESSION['user_id'];
$message = "";

// Fetch user info
$user = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM users WHERE id = $user_id"));
$user_name = $user['name'];
$user_email = $user['email'];
$user_phone = $user['phone'] ?? '';
$user_address = $user['address'] ?? '';

// Update Info
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $name = mysqli_real_escape_string($conn, $_POST['name']);
  $phone = mysqli_real_escape_string($conn, $_POST['phone']);
  $address = mysqli_real_escape_string($conn, $_POST['address']);

  $update = mysqli_query($conn, "UPDATE users SET name = '$name', phone = '$phone', address = '$address' WHERE id = $user_id");

  if ($update) {
    $_SESSION['user_name'] = $name;
    $message = "Profile updated successfully!";
    // Refresh values
    $user_name = $name;
    $user_phone = $phone;
    $user_address = $address;
  } else {
    $message = "Failed to update profile.";
  }
}

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
<div class="max-w-5xl mx-auto mt-10 p-6 bg-white shadow rounded">
  <h2 class="text-2xl font-bold mb-4 text-pink-700">Welcome, <?= htmlspecialchars($user_name) ?>!</h2>

  <?php if ($message): ?>
    <div class="mb-4 p-3 rounded text-white <?= strpos($message, 'success') !== false ? 'bg-green-500' : 'bg-red-500' ?>">
      <?= htmlspecialchars($message) ?>
    </div>
  <?php endif; ?>

  <div class="grid md:grid-cols-2 gap-8 mb-10">
    <!-- User Info Form -->
    <div>
      <h3 class="text-xl font-semibold mb-2">Your Profile</h3>
      <form method="POST" class="space-y-4">
        <div>
          <label class="block text-sm font-medium">Name</label>
          <input type="text" name="name" value="<?= htmlspecialchars($user_name) ?>"
                 class="w-full border rounded p-2" required>
        </div>
        <div>
          <label class="block text-sm font-medium">Phone</label>
          <input type="text" name="phone" value="<?= htmlspecialchars($user_phone) ?>"
                 class="w-full border rounded p-2">
        </div>
        <div>
          <label class="block text-sm font-medium">Address</label>
          <textarea name="address" rows="3" class="w-full border rounded p-2"><?= htmlspecialchars($user_address) ?></textarea>
        </div>
        <button type="submit" class="bg-pink-600 text-white px-4 py-2 rounded hover:bg-pink-700">
          Save Changes
        </button>
      </form>
    </div>

    <!-- Recent Orders -->
    <div>
      <h3 class="text-xl font-semibold mb-2">Recent Orders</h3>
      <?php if (mysqli_num_rows($orders) == 0): ?>
        <p class="text-gray-500">You have no orders yet.</p>
      <?php else: ?>
        <div class="overflow-x-auto mt-4">
          <table class="min-w-full table-auto border text-sm">
            <thead>
              <tr class="bg-gray-100">
                <th class="text-left p-2 border">Items</th>
                <th class="text-left p-2 border">Amount</th>
                <th class="text-left p-2 border">Date</th>
                <th class="text-left p-2 border">Status</th>
              </tr>
            </thead>
            <tbody>
              <?php while ($order = mysqli_fetch_assoc($orders)): ?>
                <?php
                  // Fetch order items
                  $order_id = $order['id'];
                  $items_result = mysqli_query($conn, "
                    SELECT p.name, oi.quantity 
                    FROM order_items oi
                    JOIN products p ON oi.product_id = p.id
                    WHERE oi.order_id = $order_id
                  ");
                  $itemList = '';
                  while ($item = mysqli_fetch_assoc($items_result)) {
                    $itemList .= "{$item['name']} (x{$item['quantity']}), ";
                  }
                  $itemList = rtrim($itemList, ', ');
                ?>
                <tr class="hover:bg-gray-50">
                  <td class="p-2 border"><?= htmlspecialchars($itemList) ?></td>
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
  </div>
</div>

</body>
</html>
