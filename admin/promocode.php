<?php
include('../db/connection.php');
session_start();

if (!isset($_SESSION['admin_logged_in'])) {
  header("Location: login.php");
  exit;
}

// Add new promocode
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_promo'])) {
  $code = strtoupper(trim($_POST['code']));
  $amount = (int)$_POST['amount'];
  $expiry = $_POST['expiry'];

  mysqli_query($conn, "INSERT INTO promocodes (code, amount, expiry) VALUES ('$code', $amount, '$expiry')");
  header("Location: promocode.php?success=1");
  exit;
}

// Delete promocode
if (isset($_GET['delete'])) {
  $id = (int)$_GET['delete'];
  mysqli_query($conn, "DELETE FROM promocodes WHERE id = $id");
  header("Location: promocode.php?deleted=1");
  exit;
}

$result = mysqli_query($conn, "SELECT * FROM promocodes ORDER BY expiry ASC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin - Promo Codes</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">

  <header class="bg-pink-700 text-white p-4 shadow">
    <div class="max-w-7xl mx-auto flex justify-between items-center">
      <h1 class="text-xl font-semibold">Manage Promo Codes</h1>
      <a href="logout.php" class="text-white hover:underline">Logout</a>
    </div>
  </header>

  <main class="max-w-5xl mx-auto px-6 py-8">
    <h2 class="text-xl font-bold text-pink-700 mb-4">Add Promo Code</h2>

    <?php if (isset($_GET['success'])): ?>
      <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-2 mb-4 rounded">Promo code added!</div>
    <?php elseif (isset($_GET['deleted'])): ?>
      <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-2 mb-4 rounded">Promo code deleted!</div>
    <?php endif; ?>

    <form method="POST" class="bg-white shadow rounded p-6 grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
      <input type="hidden" name="add_promo" value="1" />
      <div>
        <label class="block mb-1 font-medium">Code</label>
        <input type="text" name="code" required placeholder="e.g., SAREE10"
               class="w-full border rounded px-3 py-2 uppercase" />
      </div>
      <div>
        <label class="block mb-1 font-medium">Discount (₹)</label>
        <input type="number" name="amount" required placeholder="e.g., 100"
               class="w-full border rounded px-3 py-2" />
      </div>
      <div>
        <label class="block mb-1 font-medium">Expiry Date</label>
        <input type="date" name="expiry" required class="w-full border rounded px-3 py-2" />
      </div>
      <div class="md:col-span-3">
        <button type="submit" class="bg-pink-600 text-white px-4 py-2 rounded hover:bg-pink-700">➕ Add Promo</button>
      </div>
    </form>

    <h2 class="text-xl font-bold text-pink-700 mb-4">All Promo Codes</h2>

    <div class="overflow-x-auto bg-white shadow rounded">
      <table class="min-w-full text-sm text-left">
        <thead class="bg-pink-100 text-pink-800">
          <tr>
            <th class="px-4 py-3">Code</th>
            <th class="px-4 py-3">Discount</th>
            <th class="px-4 py-3">Expiry</th>
            <th class="px-4 py-3">Action</th>
          </tr>
        </thead>
        <tbody class="text-gray-700">
          <?php while ($row = mysqli_fetch_assoc($result)): ?>
            <tr class="border-t">
              <td class="px-4 py-2 font-semibold uppercase"><?= htmlspecialchars($row['code']) ?></td>
              <td class="px-4 py-2">₹<?= $row['amount'] ?></td>
              <td class="px-4 py-2"><?= date('d M Y', strtotime($row['expiry'])) ?></td>
              <td class="px-4 py-2">
                <a href="?delete=<?= $row['id'] ?>" class="text-red-600 hover:underline"
                   onclick="return confirm('Delete promo code?')">🗑 Delete</a>
              </td>
            </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>
  </main>

</body>
</html>
