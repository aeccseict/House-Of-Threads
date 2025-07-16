<?php
include('../db/connection.php');
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
  echo json_encode(['status' => 'error', 'message' => 'Please login to add to wishlist.']);
  exit;
}

$user_id = $_SESSION['user_id'];
$product_id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;

if ($product_id <= 0) {
  echo json_encode(['status' => 'error', 'message' => 'Invalid product ID.']);
  exit;
}

// Check if already exists
$check = mysqli_query($conn, "SELECT * FROM wishlist WHERE user_id = $user_id AND product_id = $product_id");

if (mysqli_num_rows($check) > 0) {
  echo json_encode(['status' => 'info', 'message' => 'Already in wishlist.']);
  exit;
}

// Insert into wishlist
$insert = mysqli_query($conn, "INSERT INTO wishlist (user_id, product_id) VALUES ($user_id, $product_id)");

if ($insert) {
  echo json_encode(['status' => 'success', 'message' => 'Added to wishlist.']);
} else {
  echo json_encode(['status' => 'error', 'message' => 'Failed to add to wishlist.']);
}
