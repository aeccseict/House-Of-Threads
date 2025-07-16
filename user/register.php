<?php
include('../db/connection.php');
session_start();

$success = "";
$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $name = mysqli_real_escape_string($conn, $_POST['name']);
  $email = mysqli_real_escape_string($conn, $_POST['email']);
  $password = $_POST['password'];
  $confirm = $_POST['confirm'];

  // Check if email already exists
  $check = mysqli_query($conn, "SELECT * FROM users WHERE email = '$email'");
  if (mysqli_num_rows($check) > 0) {
    $error = "Email already registered.";
  } elseif ($password !== $confirm) {
    $error = "Passwords do not match.";
  } else {
    $hashed = password_hash($password, PASSWORD_DEFAULT);
    $insert = mysqli_query($conn, "INSERT INTO users (name, email, password) VALUES ('$name', '$email', '$hashed')");

    if ($insert) {
      $success = "Registered successfully! Please login.";
      header("refresh:2;url=login.php");
    } else {
      $error = "Registration failed. Try again.";
    }
  }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Register - House of Threads</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">

  <div class="bg-white p-8 rounded shadow max-w-md w-full">
    <h2 class="text-2xl font-bold text-pink-600 mb-4">Create Account</h2>

    <?php if ($error): ?>
      <div class="bg-red-100 text-red-700 p-2 mb-4 rounded"><?= $error ?></div>
    <?php elseif ($success): ?>
      <div class="bg-green-100 text-green-700 p-2 mb-4 rounded"><?= $success ?></div>
    <?php endif; ?>

    <form method="POST" class="space-y-4">
      <input type="text" name="name" required placeholder="Full Name"
             class="w-full border p-2 rounded" />
      <input type="email" name="email" required placeholder="Email"
             class="w-full border p-2 rounded" />
      <input type="password" name="password" required placeholder="Password"
             class="w-full border p-2 rounded" />
      <input type="password" name="confirm" required placeholder="Confirm Password"
             class="w-full border p-2 rounded" />

      <button type="submit"
              class="bg-pink-600 hover:bg-pink-700 text-white w-full py-2 rounded font-semibold">
        Register
      </button>
    </form>

    <p class="mt-4 text-sm text-gray-600">
      Already have an account?
      <a href="login.php" class="text-pink-600 hover:underline">Login here</a>
    </p>
  </div>

</body>
</html>
