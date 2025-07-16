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
  $existing = mysqli_query($conn, "SELECT * FROM cart WHERE user_id = $user_id AND product_id = $product_id");
  if (mysqli_num_rows($existing) > 0) {
    mysqli_query($conn, "UPDATE cart SET quantity = quantity + 1 WHERE user_id = $user_id AND product_id = $product_id");
  } else {
    mysqli_query($conn, "INSERT INTO cart (user_id, product_id, quantity) VALUES ($user_id, $product_id, 1)");
  }
  echo json_encode(['status' => 'added']);
}
elseif ($action === 'remove' && $product_id > 0) {
  mysqli_query($conn, "DELETE FROM cart WHERE user_id = $user_id AND product_id = $product_id");
  echo json_encode(['status' => 'removed']);
}
else {
  echo json_encode(['error' => 'invalid']);
}

$qty = isset($_GET['qty']) ? (int)$_GET['qty'] : 1;
if ($qty < 1) $qty = 1;

// Inside action === 'add'
if (mysqli_num_rows($existing) > 0) {
  mysqli_query($conn, "UPDATE cart SET quantity = $qty WHERE user_id = $user_id AND product_id = $product_id");
} else {
  mysqli_query($conn, "INSERT INTO cart (user_id, product_id, quantity) VALUES ($user_id, $product_id, $qty)");
}
