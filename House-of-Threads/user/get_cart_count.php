<?php
session_start();
include('../db/connection.php');

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
  echo json_encode(['count' => 0]);
  exit;
}

$user_id = $_SESSION['user_id'];
$result = mysqli_query($conn, "SELECT SUM(quantity) as total FROM cart WHERE user_id = $user_id");
$data = mysqli_fetch_assoc($result);
echo json_encode(['count' => (int)$data['total']]);
