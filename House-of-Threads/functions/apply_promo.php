<?php
include('../db/connection.php');

$code = $_GET['code'] ?? '';
$code = trim($code);

if (!$code) {
  echo json_encode(['valid' => false]);
  exit;
}

$query = "SELECT * FROM promo_codes WHERE code = '$code' AND active = 1 AND (expiry_date IS NULL OR expiry_date >= NOW())";
$res = mysqli_query($conn, $query);

if (mysqli_num_rows($res) > 0) {
  $promo = mysqli_fetch_assoc($res);
  echo json_encode(['valid' => true, 'discount' => (int)$promo['discount']]);
} else {
  echo json_encode(['valid' => false]);
}
