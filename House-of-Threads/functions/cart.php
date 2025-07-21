<?php
include('../db/connection.php');
session_start();
header('Content-Type: application/json');

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
  echo json_encode(['error' => 'not_logged_in']);
  exit;
}

$user_id = $_SESSION['user_id'];
$action = $_GET['action'] ?? '';
$product_id = (int)($_GET['id'] ?? 0);
$qty = isset($_GET['qty']) ? (int)$_GET['qty'] : 1;
if ($qty < 1) $qty = 1;

// Initialize cart in session (if needed)
if (!isset($_SESSION['cart'])) {
  $_SESSION['cart'] = [];
}

switch ($action) {

  // ➕ Add to Cart
  case 'add':
    $existing = mysqli_query($conn, "SELECT * FROM cart WHERE user_id = $user_id AND product_id = $product_id");
    if (mysqli_num_rows($existing) > 0) {
      mysqli_query($conn, "UPDATE cart SET quantity = quantity + $qty WHERE user_id = $user_id AND product_id = $product_id");
    } else {
      mysqli_query($conn, "INSERT INTO cart (user_id, product_id, quantity) VALUES ($user_id, $product_id, $qty)");
    }

    // Update session cart
    if (isset($_SESSION['cart'][$product_id])) {
      $_SESSION['cart'][$product_id]['quantity'] += $qty;
    } else {
      $_SESSION['cart'][$product_id] = ['product_id' => $product_id, 'quantity' => $qty];
    }

    echo json_encode(['status' => 'added']);
    break;

  // ✏️ Update Quantity
  case 'update':
    mysqli_query($conn, "UPDATE cart SET quantity = $qty WHERE user_id = $user_id AND product_id = $product_id");

    // Update session cart
    if (isset($_SESSION['cart'][$product_id])) {
      $_SESSION['cart'][$product_id]['quantity'] = $qty;
    }

    echo json_encode(['status' => 'updated']);
    break;

  // 🗑 Remove Item
  case 'remove':
    mysqli_query($conn, "DELETE FROM cart WHERE user_id = $user_id AND product_id = $product_id");

    // Remove from session
    unset($_SESSION['cart'][$product_id]);

    echo json_encode(['status' => 'removed']);
    break;

  default:
    echo json_encode(['error' => 'invalid_action']);
    break;
}

// ❌ Invalid action fallback
echo json_encode(['error' => 'invalid_action']);
exit;