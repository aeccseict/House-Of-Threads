<!-- /user/user_dashboard.php -->
<?php
include('../db/connection.php');
session_start();

if (!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit;
}

$userName = $_SESSION['user_name'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Dashboard - SareeStyle</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 text-gray-800 font-sans">

  <!-- Navbar -->
  <nav class="flex justify-between items-center p-4 bg-pink-100 shadow-md">
    <h1 class="text-2xl font-bold text-pink-700">SareeStyle</h1>
    <ul class="flex space-x-4 text-pink-700 font-medium">
      <li><a href="index.php">Home</a></li>
      <li><a href="products.php">Products</a></li>
      <li><a href="cart.php">Cart</a></li>
      <li><a href="wishlist.php">Wishlist</a></li>
      <li><a href="logout.php" class="text-red-600">Logout</a></li>
    </ul>
  </nav>

  <!-- Dashboard -->
  <section class="max-w-4xl mx-auto mt-10 bg-white p-6 rounded shadow">
    <h2 class="text-3xl font-bold text-pink-700 mb-4">Welcome, <?= htmlspecialchars($userName) ?>!</h2>
    <p class="text-gray-700">This is your personal dashboard. You can manage your account, view orders (coming soon), and more.</p>

    <div class="mt-6 grid grid-cols-2 gap-4">
      <a href="cart.php" class="bg-green-600 text-white p-4 rounded text-center hover:bg-green-700">🛒 View Cart</a>
      <a href="wishlist.php" class="bg-yellow-500 text-white p-4 rounded text-center hover:bg-yellow-600">❤️ View Wishlist</a>
    </div>
    <?php
      include('../db/connection.php');
      session_start();

      $userEmail = $_SESSION['user_email'] ?? '';

      if (!$userEmail) {
        header("Location: login.php");
        exit;
      }

      $result = mysqli_query($conn, "SELECT * FROM orders WHERE email = '$userEmail' ORDER BY created_at DESC");
    ?>

  </section>
  <h2 class="text-xl font-semibold text-pink-700 mb-4">Your Orders</h2>

  <table class="min-w-full bg-white shadow-md rounded mb-6">
    <thead class="bg-pink-100 text-pink-800 text-sm">
      <tr>
        <th class="py-2 px-4">Order ID</th>
        <th class="py-2 px-4">Items</th>
        <th class="py-2 px-4">Total</th>
        <th class="py-2 px-4">Date</th>
        <th class="py-2 px-4">Status</th>
      </tr>
    </thead>
    <tbody class="text-gray-700 text-sm">
      <?php while ($order = mysqli_fetch_assoc($result)): ?>
        <?php
          $items = json_decode($order['items'], true);
          $itemList = "";
          foreach ($items as $item) {
            $itemList .= "{$item['name']} (x{$item['qty']}), ";
          }
          $itemList = rtrim($itemList, ", ");
        ?>
        <tr class="border-t">
          <td class="py-2 px-4"><?= $order['id'] ?></td>
          <td class="py-2 px-4"><?= $itemList ?></td>
          <td class="py-2 px-4">₹<?= $order['total'] ?></td>
          <td class="py-2 px-4"><?= date("d M Y", strtotime($order['created_at'])) ?></td>
          <td class="py-2 px-4">
            <span class="px-2 py-1 rounded text-xs font-medium
              <?= $order['status'] === 'Shipped' ? 'bg-green-100 text-green-700' :
                  ($order['status'] === 'Cancelled' ? 'bg-red-100 text-red-700' :
                  'bg-yellow-100 text-yellow-800') ?>">
              <?= $order['status'] ?>
            </span>
          </td>
        </tr>
      <?php endwhile; ?>
    </tbody>
  </table>

</body>
</html>
