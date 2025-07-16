<?php
include('../db/connection.php');
session_start();

// Only allow admins
if (!isset($_SESSION['admin_logged_in'])) {
  header("Location: login.php");
  exit;
}

// Handle POST status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id'], $_POST['status'])) {
  $order_id = (int)$_POST['order_id'];
  $status = mysqli_real_escape_string($conn, $_POST['status']);

  // Validate status value
  $valid_statuses = ['Pending', 'Shipped', 'Cancelled'];
  if (!in_array($status, $valid_statuses)) {
    header("Location: orders.php?error=invalid_status");
    exit;
  }

  // Update DB
  mysqli_query($conn, "UPDATE orders SET status = '$status' WHERE id = $order_id");

  header("Location: orders.php?updated=1");
  exit;
} else {
  header("Location: orders.php?error=missing_data");
  exit;
}
