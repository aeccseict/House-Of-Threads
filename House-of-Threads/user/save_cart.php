<?php
include('../db/connection.php');
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
  echo json_encode(['status' => 'error', 'message' => 'User not logged in']);
  exit;
}

$user_id = $_SESSION['user_id'];
$product_id = isset($_POST['product_id']) ? (int) $_POST['product_id'] : 0;
$quantity = isset($_POST['quantity']) ? (int) $_POST['quantity'] : 1;

if ($product_id <= 0) {
  echo json_encode(['status' => 'error', 'message' => 'Invalid product ID']);
  exit;
}

// Check if item already in cart
$check = mysqli_query($conn, "SELECT * FROM cart WHERE user_id = $user_id AND product_id = $product_id");

if (mysqli_num_rows($check) > 0) {
  // Update quantity
  mysqli_query($conn, "UPDATE cart SET quantity = quantity + $quantity WHERE user_id = $user_id AND product_id = $product_id");
} else {
  // Insert new item
  mysqli_query($conn, "INSERT INTO cart (user_id, product_id, quantity) VALUES ($user_id, $product_id, $quantity)");
}

echo json_encode(['status' => 'success', 'message' => 'Item added to cart']);

