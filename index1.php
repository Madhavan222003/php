<?php
// include your database configuration file
include 'config.php';

// Fetch product details from the database
$products = "";
$sql = "SELECT pid, pname FROM products";
$res = $con->query($sql);

if ($res->num_rows > 0) {
    while ($row = $res->fetch_assoc()) {
        $products .= "<option value='{$row["pid"]}'>{$row["pname"]}</option>";
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
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

    // Redirect or display a success message
    echo "Receipt saved successfully!";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Generate Receipt</title>
</head>
<body>
    <h1>Generate Receipt</h1>
    <form action="index1.php" method="post">
        <label for="shop_name">Shop Name:</label>
        <input type="text" id="shop_name" name="shop_name" required><br><br>

        <label for="grand_total">Grand Total:</label>
        <input type="text" id="grand_total" name="grand_total" required><br><br>

        <div id="products">
            <div class="product">
                <label for="product_id[]">Product:</label>
                <select name="product_id[]" class='pid'>
                    <?php echo $products; ?>
                </select><br><br>
                <label for="price[]">Price:</label>
                <input type="text" name="price[]" class='price' required><br><br>
                <label for="qty[]">Quantity:</label>
                <input type="text" name="qty[]" class='qty' required><br><br>
                <label for="total[]">Total:</label>
                <input type="text" name="total[]" class='total' required><br><br>
            </div>
        </div>

        <button type="button" onclick="addProduct()">Add Another Product</button><br><br>

        <button type="submit">Generate Receipt</button>
    </form>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        function addProduct() {
            var products = "<?php echo addslashes($products); ?>";
            var newProductDiv = document.createElement('div');
            newProductDiv.className = 'product';
            newProductDiv.innerHTML = `
                <label for="product_id[]">Product:</label>
                <select name="product_id[]" class='pid'>${products}</select><br><br>
                <label for="price[]">Price:</label>
                <input type="text" name="price[]" class='price' required><br><br>
                <label for="qty[]">Quantity:</label>
                <input type="text" name="qty[]" class='qty' required><br><br>
                <label for="total[]">Total:</label>
                <input type="text" name="total[]" class='total' required><br><br>
            `;
            document.getElementById('products').appendChild(newProductDiv);
        }

        $(document).ready(function(){
            $("body").on("change", ".pid", function(){
                var pid = $(this).val();
                var input = $(this).parents("div.product").find(".price");
                $.ajax({
                    url: "get_price.php",
                    type: "post",
                    data: {pid: pid},
                    success: function(res){
                        $(input).val(res);
                    }
                });
            });

            $("body").on("keyup", ".qty", function(){
                var qty = Number($(this).val());
                var price = Number($(this).parents("div.product").find(".price").val());
                $(this).parents("div.product").find(".total").val(qty * price);
            });
        });
    </script>
</body>
</html>
