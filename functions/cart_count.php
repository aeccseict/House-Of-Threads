<?php
include('../db/connection.php');
session_start();

$user_id = $_SESSION['user_id'] ?? 0;

if ($user_id) {
  $res = mysqli_query($conn, "SELECT SUM(quantity) AS count FROM cart WHERE user_id = $user_id");
  $row = mysqli_fetch_assoc($res);
  echo json_encode(['count' => (int)$row['count']]);
} else {
  echo json_encode(['count' => 0]);
}
