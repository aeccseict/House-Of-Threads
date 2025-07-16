<?php
include('../db/connection.php');

// Set headers to trigger file download
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="orders_export_' . date("Y-m-d") . '.csv"');

$output = fopen("php://output", "w");

// Column headers
fputcsv($output, ['ID', 'Name', 'Email', 'Phone', 'Address', 'Promo', 'Total', 'Items', 'Status', 'Date']);

$result = mysqli_query($conn, "SELECT * FROM orders ORDER BY created_at DESC");

while ($row = mysqli_fetch_assoc($result)) {
  $items = json_decode($row['items'], true);
  $itemList = '';
  foreach ($items as $item) {
    $itemList .= "{$item['name']} (x{$item['qty']} ₹{$item['price']}), ";
  }
  $itemList = rtrim($itemList, ", ");

  fputcsv($output, [
    $row['id'],
    $row['customer_name'],
    $row['email'],
    $row['phone'],
    $row['address'],
    $row['promo'],
    $row['total'],
    $itemList,
    $row['status'],
    $row['created_at']
  ]);
}

fclose($output);
exit;
