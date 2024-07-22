<?php
require 'vendor/autoload.php';
use Dompdf\Dompdf;

include 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $shop_name = $_POST['shop_name'];
    $grand_total = $_POST['grand_total'];
    $product_ids = $_POST['product_id'];
    $prices = $_POST['price'];
    $quantities = $_POST['qty'];
    $totals = $_POST['total'];

    // Insert into receipts table
    $stmt = $con->prepare("INSERT INTO receipts (shop_name, grand_total) VALUES (?, ?)");
    $stmt->bind_param("sd", $shop_name, $grand_total);
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

    // Generate the HTML content
    $html = "<html><head><title>Receipt</title></head><body>";
    $html .= "<h2>Receipt from $shop_name</h2>";
    $html .= "<p>Receipt ID: $receipt_id</p>";
    $html .= "<p>Grand Total: $" . number_format($grand_total, 2) . "</p>";
    $html .= "<table border='1'><tr><th>Product ID</th><th>Price</th><th>Quantity</th><th>Total</th></tr>";

    for ($i = 0; $i < count($product_ids); $i++) {
        $html .= "<tr><td>{$product_ids[$i]}</td><td>{$prices[$i]}</td><td>{$quantities[$i]}</td><td>{$totals[$i]}</td></tr>";
    }

    $html .= "</table>";
    $html .= "</body></html>";

    // Generate the PDF
    $dompdf = new Dompdf();
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();
    $dompdf->stream("receipt_$receipt_id.pdf", array("Attachment" => true));
}
?>
