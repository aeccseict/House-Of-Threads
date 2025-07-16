<?php
include('../db/connection.php');
session_start();

$error = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $email = $_POST['email'];
  $password = md5($_POST['password']); // using md5 for comparison

  $query = "SELECT * FROM admin WHERE email = '$email' AND password = '$password'";
  $result = mysqli_query($conn, $query);

  if (mysqli_num_rows($result) === 1) {
    $admin = mysqli_fetch_assoc($result);
    $_SESSION['admin_id'] = $admin['id'];
    $_SESSION['admin_name'] = $admin['email'];
    header("Location: dashboard.php");
    exit;
  } else {
    $error = "Invalid email or password.";
  }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Login - House of Threads</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-pink-100 h-screen flex items-center justify-center">

  <div class="bg-white shadow-md rounded px-8 py-6 w-full max-w-md">
    <h2 class="text-2xl font-bold text-pink-700 mb-6 text-center">Admin Login</h2>

    <?php if (!empty($error)): ?>
      <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-2 rounded mb-4">
        <?= htmlspecialchars($error) ?>
      </div>
    <?php endif; ?>

    <form method="POST" action="">
      <div class="mb-4">
        <label for="email" class="block text-sm font-medium text-gray-700">Email / Username</label>
        <input type="text" id="email" name="email" required
               class="mt-1 block w-full px-3 py-2 border rounded shadow-sm focus:outline-none focus:ring-pink-500 focus:border-pink-500">
      </div>

      <div class="mb-6">
        <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
        <input type="password" id="password" name="password" required
               class="mt-1 block w-full px-3 py-2 border rounded shadow-sm focus:outline-none focus:ring-pink-500 focus:border-pink-500">
      </div>

      <button type="submit"
              class="w-full bg-pink-600 text-white py-2 px-4 rounded hover:bg-pink-700 font-semibold">
        Login
      </button>
    </form>
  </div>

</body>
</html>
