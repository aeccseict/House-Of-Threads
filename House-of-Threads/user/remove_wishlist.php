<?php
include('../db/connection.php');
session_start();

if (!isset($_SESSION['user_id']) || !isset($_POST['wishlist_id'])) {
  header("Location: wishlist.php");
  exit;
}

$wishlist_id = (int)$_POST['wishlist_id'];
$user_id = $_SESSION['user_id'];

// Remove wishlist item
mysqli_query($conn, "DELETE FROM wishlist WHERE id = $wishlist_id AND user_id = $user_id");

header("Location: wishlist.php");
exit;
