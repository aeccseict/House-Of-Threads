<?php
include('../db/connection.php');

$code = strtoupper(trim($_GET['code'] ?? ''));
$today = date('Y-m-d');

$res = mysqli_query($conn, "SELECT * FROM promocodes WHERE code = '$code' AND expiry >= '$today'");
if ($row = mysqli_fetch_assoc($res)) {
  echo json_encode(['valid' => true, 'amount' => (int)$row['amount']]);
} else {
  echo json_encode(['valid' => false]);
}
