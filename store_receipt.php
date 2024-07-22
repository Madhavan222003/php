<?php
include 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $shop_name = $_POST['shop_name'];
    $receipt_date = date('Y-m-d'); // Current date
    $receipt_time = date('H:i:s'); // Current time
    $grand_total = $_POST['grand_total'];
    $product_ids = $_POST['product_id'];
    $prices = $_POST['price'];
    $quantities = $_POST['qty'];
    $totals = $_POST['total'];

    // Insert into receipts table
    $stmt = $con->prepare("INSERT INTO receipts (shop_name, receipt_date, receipt_time, grand_total) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("sssd", $shop_name, $receipt_date, $receipt_time, $grand_total);
    $stmt->execute();
    $receipt_id = $stmt->insert_id;
    $stmt->close();

    // Insert into receipt_items table
    $stmt = $con->prepare("INSERT INTO receipt_items (receipt_id, product_id, price, quantity, total) VALUES (?, ?, ?, ?, ?)");
    for ($i = 0; $i < count($product_ids); $i++) {
        $stmt->bind_param("iidid", $receipt_id, $product_ids[$i], $prices[$i], $quantities[$i], $totals[$i]);
        $stmt->execute();
    }
    $stmt->close();

    // Redirect or display a success message
    echo "Receipt saved successfully!";
}
?>
