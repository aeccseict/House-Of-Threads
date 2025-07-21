<?php
include('../db/connection.php');
session_start();

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $email = mysqli_real_escape_string($conn, $_POST['email']);
  $password = $_POST['password'];

  $query = mysqli_query($conn, "SELECT * FROM users WHERE email = '$email' LIMIT 1");
  $user = mysqli_fetch_assoc($query);

  if ($user && password_verify($password, $user['password'])) {
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_name'] = $user['name'];
    header("Location: user_dashboard.php");
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
  <title>Login - House of Threads</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">

  <div class="bg-white p-8 rounded shadow max-w-md w-full">
    <h2 class="text-2xl font-bold text-pink-600 mb-4">User Login</h2>

    <?php if ($error): ?>
      <div class="bg-red-100 text-red-700 p-2 mb-4 rounded"><?= $error ?></div>
    <?php endif; ?>

    <form method="POST" class="space-y-4">
      <input type="email" name="email" required placeholder="Email"
             class="w-full border p-2 rounded" />
      <input type="password" name="password" required placeholder="Password"
             class="w-full border p-2 rounded" />

      <button type="submit"
              class="bg-pink-600 hover:bg-pink-700 text-white w-full py-2 rounded font-semibold">
        Login
      </button>
    </form>

    <p class="mt-4 text-sm text-gray-600">
      Don't have an account?
      <a href="register.php" class="text-pink-600 hover:underline">Register here</a>
    </p>
  </div>

</body>
</html>
