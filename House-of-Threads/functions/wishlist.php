<?php
include('../db/connection.php');
session_start();

if (!isset($_SESSION['user_id'])) {
  echo json_encode(['error' => 'not_logged_in']);
  exit;
}

$user_id = $_SESSION['user_id'];
$action = $_GET['action'] ?? '';
$product_id = (int)($_GET['id'] ?? 0);

if ($action === 'add' && $product_id > 0) {
  $exists = mysqli_query($conn, "SELECT * FROM wishlist WHERE user_id = $user_id AND product_id = $product_id");
  if (mysqli_num_rows($exists) == 0) {
    mysqli_query($conn, "INSERT INTO wishlist (user_id, product_id) VALUES ($user_id, $product_id)");
  }
  echo json_encode(['status' => 'added']);
}
elseif ($action === 'remove' && $product_id > 0) {
  mysqli_query($conn, "DELETE FROM wishlist WHERE user_id = $user_id AND product_id = $product_id");
  echo json_encode(['status' => 'removed']);
}
else {
  echo json_encode(['error' => 'invalid']);
}
