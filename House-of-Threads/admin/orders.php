<?php 
include('../db/connection.php');

// Pagination setup
$limit = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start = ($page - 1) * $limit;

$totalQuery = mysqli_query($conn, "SELECT COUNT(*) AS total FROM orders");
$totalOrders = mysqli_fetch_assoc($totalQuery)['total'];
$totalPages = ceil($totalOrders / $limit);

// Fetch paginated orders
$result = mysqli_query($conn, "SELECT * FROM orders ORDER BY created_at DESC LIMIT $start, $limit");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin - Orders</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">

  <header class="bg-pink-700 text-white p-4 shadow">
    <div class="max-w-7xl mx-auto flex justify-between items-center">
      <h1 class="text-xl font-semibold">Admin Panel - Orders</h1>
      <a href="logout.php" class="text-white hover:underline">Logout</a>
    </div>
  </header>

  <main class="max-w-7xl mx-auto px-6 py-8">
    <div class="flex justify-between items-center mb-6">
      <h2 class="text-xl font-bold text-pink-700">All Orders</h2>
      <a href="export_orders.php" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded">
        📥 Export Orders
      </a>
    </div>

    <div class="overflow-x-auto bg-white shadow rounded-lg">
      <table class="min-w-full text-sm text-left">
        <thead class="bg-pink-100 text-pink-800">
          <tr>
            <th class="px-4 py-3">#</th>
            <th class="px-4 py-3">Customer</th>
            <th class="px-4 py-3">Email</th>
            <th class="px-4 py-3">Phone</th>
            <th class="px-4 py-3">Promo</th>
            <th class="px-4 py-3">Total</th>
            <th class="px-4 py-3">Address</th>
            <th class="px-4 py-3">Items</th>
            <th class="px-4 py-3">Status</th>
            <th class="px-4 py-3">Date</th>
          </tr>
        </thead>
        <tbody class="text-gray-700">
          <?php while ($row = mysqli_fetch_assoc($result)): ?>
            <?php
            // Fetch items for each order using a JOIN
            $order_id = $row['id'];
            $items_q = mysqli_query($conn, "
              SELECT oi.quantity, p.name 
              FROM order_items oi 
              JOIN products p ON oi.product_id = p.id 
              WHERE oi.order_id = $order_id
            ");
            $itemList = "";
            while ($item = mysqli_fetch_assoc($items_q)) {
              $itemList .= "{$item['name']} (x{$item['quantity']}), ";
            }
            $itemList = rtrim($itemList, ", ");
            ?>
            <tr class="border-t">
              <td class="px-4 py-2"><?= $row['id'] ?></td>
              <td class="px-4 py-2"><?= htmlspecialchars($row['name']) ?></td>
              <td class="px-4 py-2"><?= $row['email'] ?></td>
              <td class="px-4 py-2"><?= $row['phone'] ?></td>
              <td class="px-4 py-2"><?= htmlspecialchars($row['address']) ?></td>
              <td class="px-4 py-2">₹<?= $row['total'] ?></td>
              <td class="px-4 py-2"><?= $row['promo_code'] ?: '-' ?></td>
              <td class="px-4 py-2"><?= $itemList ?></td>
              <td class="px-4 py-2">
                <form method="POST" action="update_status.php" class="flex items-center space-x-2">
                  <input type="hidden" name="order_id" value="<?= $row['id'] ?>">
                  <select name="status" class="border px-2 py-1 rounded text-sm">
                    <?php foreach (['Pending', 'Shipped', 'Cancelled'] as $status): ?>
                      <option value="<?= $status ?>" <?= $row['status'] === $status ? 'selected' : '' ?>>
                        <?= $status ?>
                      </option>
                    <?php endforeach; ?>
                  </select>
                  <button type="submit" class="text-xs bg-pink-600 text-white px-2 py-1 rounded">Update</button>
                </form>
              </td>
              <td class="px-4 py-2"><?= date('d M Y h:i A', strtotime($row['created_at'])) ?></td>
            </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>

    <!-- Pagination -->
    <div class="flex justify-center mt-6 space-x-2">
      <?php for ($i = 1; $i <= $totalPages; $i++): ?>
        <a href="?page=<?= $i ?>"
           class="px-3 py-1 border rounded <?= ($i == $page) ? 'bg-pink-600 text-white' : 'bg-white text-pink-700 hover:bg-pink-100' ?>">
          <?= $i ?>
        </a>
      <?php endfor; ?>
    </div>
  </main>

</body>
</html>
