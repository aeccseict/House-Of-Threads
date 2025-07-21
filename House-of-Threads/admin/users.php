<?php
include('../db/connection.php');
session_start();

if (!isset($_SESSION['admin_logged_in'])) {
  header("Location: login.php");
  exit;
}

// Delete user
if (isset($_GET['delete'])) {
  $user_id = (int)$_GET['delete'];
  mysqli_query($conn, "DELETE FROM users WHERE id = $user_id");
  header("Location: users.php?deleted=1");
  exit;
}

$users = mysqli_query($conn, "SELECT * FROM users ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin - Users</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">

  <header class="bg-pink-700 text-white p-4 shadow">
    <div class="max-w-7xl mx-auto flex justify-between items-center">
      <h1 class="text-xl font-semibold">Manage Users</h1>
      <a href="logout.php" class="text-white hover:underline">Logout</a>
    </div>
  </header>

  <main class="max-w-6xl mx-auto px-6 py-8">
    <h2 class="text-xl font-bold text-pink-700 mb-4">Registered Users</h2>

    <?php if (isset($_GET['deleted'])): ?>
      <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-2 rounded mb-4">User deleted successfully!</div>
    <?php endif; ?>

    <div class="overflow-x-auto bg-white shadow rounded">
      <table class="min-w-full text-sm text-left">
        <thead class="bg-pink-100 text-pink-800">
          <tr>
            <th class="px-4 py-3">#</th>
            <th class="px-4 py-3">Name</th>
            <th class="px-4 py-3">Email</th>
            <th class="px-4 py-3">Phone</th>
            <th class="px-4 py-3">Registered</th>
            <th class="px-4 py-3">Action</th>
          </tr>
        </thead>
        <tbody class="text-gray-700">
          <?php $i = 1; while ($row = mysqli_fetch_assoc($users)): ?>
            <tr class="border-t">
              <td class="px-4 py-2"><?= $i++ ?></td>
              <td class="px-4 py-2"><?= htmlspecialchars($row['name']) ?></td>
              <td class="px-4 py-2"><?= htmlspecialchars($row['email']) ?></td>
              <td class="px-4 py-2"><?= $row['phone'] ?></td>
              <td class="px-4 py-2"><?= date('d M Y', strtotime($row['created_at'])) ?></td>
              <td class="px-4 py-2">
                <a href="?delete=<?= $row['id'] ?>" onclick="return confirm('Delete this user?')" class="text-red-600 hover:underline">
                  🗑 Delete
                </a>
              </td>
            </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>
  </main>

</body>
</html>
