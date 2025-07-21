<?php
$host = "localhost";        // Database host
$user = "root";             // Database username
$pass = "";                 // Database password
$db   = "House-of-Threads";  // Your database name

$conn = mysqli_connect($host, $user, $pass, $db);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Optional: set charset
mysqli_set_charset($conn, "utf8");
?>
