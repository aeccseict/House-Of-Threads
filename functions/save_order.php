<?php
include('../db/connection.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') 
  {
    // Sanitize inputs
    $name    = mysqli_real_escape_string($conn, $_POST['name']);
    $email   = mysqli_real_escape_string($conn, $_POST['email']);
    $phone   = mysqli_real_escape_string($conn, $_POST['phone']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $promo   = mysqli_real_escape_string($conn, $_POST['promo']);
    $total   = mysqli_real_escape_string($conn, $_POST['total']);

    // Decode cart JSON string into PHP array
    $items = json_decode($_POST['items'], true);
    if (!is_array($items) || count($items) === 0) {
      http_response_code(400);
      echo json_encode(["status" => "error", "message" => "Invalid cart data"]);
      exit;
    }

    $items_json = mysqli_real_escape_string($conn, json_encode($items));

    // Insert order into DB
    $query = "INSERT INTO orders (customer_name, email, phone, address, promo, total, items)
              VALUES ('$name', '$email', '$phone', '$address', '$promo', '$total', '$items_json')";

    if (mysqli_query($conn, $query)) {
      echo json_encode(["status" => "success", "message" => "Order saved"]);
    } else {
      http_response_code(500);
      echo json_encode(["status" => "error", "message" => "Database error"]);
    }
  } 
  else {
    http_response_code(405);
    echo json_encode(["status" => "error", "message" => "Method not allowed"]);
  }

  // Automatically generating a PDF invoice
  //Attaching it to the admin email
  //Only when order total is above a certain limit (e.g. ₹5000)
  require_once('../vendor/tcpdf/tcpdf.php');

  if (mysqli_query($conn, $query)) {

    // ✅ Generate PDF if total ≥ ₹5000
    if ($total >= 5000) {
      require_once('../vendor/tcpdf/tcpdf.php');

      $pdf = new TCPDF();
      $pdf->AddPage();
      $pdf->SetFont('dejavusans', '', 10);

      $html = "<h2>Invoice - SareeStyle</h2>
        <p><strong>Customer:</strong> $name<br>
        <strong>Email:</strong> $email<br>
        <strong>Phone:</strong> $phone<br>
        <strong>Address:</strong> $address<br>
        <strong>Promo:</strong> $promo<br>
        <strong>Total:</strong> ₹$total</p>
        <table border='1' cellspacing='0' cellpadding='4'>
          <thead><tr><th>Item</th><th>Qty</th><th>Price</th></tr></thead>
          <tbody>";

      foreach ($items as $item) {
        $html .= "<tr>
          <td>{$item['name']}</td>
          <td>{$item['qty']}</td>
          <td>₹" . ($item['qty'] * $item['price']) . "</td>
        </tr>";
      }
      $html .= "</tbody></table>";

      $pdf->writeHTML($html);
      $pdfPath = "../invoices/invoice_" . time() . ".pdf";
      $pdf->Output($pdfPath, 'F');

      // ✅ Send Email with PDF to Admin
      $adminEmail = "youradmin@email.com";
      $boundary = md5(time());
      $headers = "MIME-Version: 1.0\r\n";
      $headers .= "From: SareeStyle <no-reply@sareestyle.com>\r\n";
      $headers .= "Content-Type: multipart/mixed; boundary=$boundary\r\n";

      $message = "--$boundary\r\n";
      $message .= "Content-Type: text/html; charset=UTF-8\r\n\r\n";
      $message .= "<p>New Order with Invoice attached.</p>";

      $file = chunk_split(base64_encode(file_get_contents($pdfPath)));
      $filename = basename($pdfPath);

      $message .= "--$boundary\r\n";
      $message .= "Content-Type: application/pdf; name=$filename\r\n";
      $message .= "Content-Transfer-Encoding: base64\r\n";
      $message .= "Content-Disposition: attachment; filename=$filename\r\n\r\n";
      $message .= $file . "\r\n";
      $message .= "--$boundary--";

      mail($adminEmail, "New Order with Invoice", $message, $headers);
    }

    // ✅ Final API response
    echo json_encode(["status" => "success", "message" => "Order saved"]);

  } else {
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => "Database error"]);
  }


  // Send Email to Admin
  $adminEmail = "youradmin@email.com";  // replace this

  $subject = "New Saree Order from $name";
  $message = "
    <h2>New Order Received</h2>
    <p><strong>Name:</strong> $name</p>
    <p><strong>Email:</strong> $email</p>
    <p><strong>Phone:</strong> $phone</p>
    <p><strong>Address:</strong> $address</p>
    <p><strong>Promo Code:</strong> $promo</p>
    <p><strong>Total:</strong> ₹$total</p>
    <p><strong>Items:</strong></p>
    <ul>
  ";

  foreach ($items as $item) {
    $itemName = htmlspecialchars($item['name']);
    $itemQty = (int)$item['qty'];
    $itemPrice = (int)$item['price'];
    $message .= "<li>$itemName x $itemQty = ₹" . ($itemQty * $itemPrice) . "</li>";
  }
  $message .= "</ul>";

  $headers = "MIME-Version: 1.0\r\n";
  $headers .= "Content-Type:text/html;charset=UTF-8\r\n";
  $headers .= "From: House of Threads Salem <no-reply@houseofthreadssalem.com>\r\n";

  mail($adminEmail, $subject, $message, $headers);


?>

<!-- JS to Call save_order.php -->

<!-- function placeOrder() {
  const order = {
    name: document.getElementById("name").value,
    email: document.getElementById("email").value,
    phone: document.getElementById("phone").value,
    address: document.getElementById("address").value,
    promo: document.getElementById("promoCode").value,
    total: document.getElementById("orderTotal").innerText,
    items: JSON.parse(localStorage.getItem("cart"))
  };

  fetch("../functions/save_order.php", {
    method: "POST",
    headers: { "Content-Type": "application/x-www-form-urlencoded" },
    body: new URLSearchParams({
      name: order.name,
      email: order.email,
      phone: order.phone,
      address: order.address,
      promo: order.promo,
      total: order.total,
      items: JSON.stringify(order.items)
    })
  })
  .then(res => res.json())
  .then(data => {
    if (data.status === "success") {
      alert("Order placed successfully!");
      localStorage.removeItem("cart");
      window.location.href = "index.php";
    } else {
      alert("Error placing order: " + data.message);
    }
  });
} -->
