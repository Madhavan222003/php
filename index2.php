<!DOCTYPE html>
<html>
<head>
    <title>Get Product Price on Dynamically Added Row in PHP-MySQL</title>
    <script src="https://code.jquery.com/jquery-2.2.4.min.js" integrity="sha256-BbhdlvQf/xTY9gja0Dq3HiwQF8LaCRTXxZKRutelT44=" crossorigin="anonymous"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 20px;
        }
        h2 {
            text-align: center;
            color: #333;
        }
        form {
            max-width: 800px;
            margin: 0 auto;
            background: #fff;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        label {
            display: block;
            margin: 15px 0 5px;
            color: #333;
        }
        input[type="text"],
        input[type="date"],
        input[type="time"],
        select {
            width: calc(100% - 22px);
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        th, td {
            border: 1px solid #ccc;
            padding: 10px;
            text-align: center;
        }
        th {
            background-color: #f4f4f4;
        }
        input[type="button"] {
            padding: 5px 10px;
            border: none;
            background-color: #007BFF;
            color: #fff;
            border-radius: 5px;
            cursor: pointer;
        }
        input[type="button"]:hover {
            background-color: #0056b3;
        }
        input[type="submit"] {
            width: 100%;
            padding: 10px;
            border: none;
            background-color: #28a745;
            color: #fff;
            font-size: 16px;
            border-radius: 5px;
            cursor: pointer;
        }
        input[type="submit"]:hover {
            background-color: #218838;
        }
    </style>
</head>
<body>
    <?php 
    include "config.php";
    $sql = "SELECT pid, pname FROM products";
    $products = "<option>Select</option>";
    $res = $con->query($sql);
    if ($res->num_rows > 0) {
        while ($row = $res->fetch_assoc()) {
            $products .= "<option value='{$row["pid"]}'>{$row["pname"]}</option>";
        }
    }
    ?>
    <h2>Shop Receipt</h2>
    <form id="receiptForm" method="post" action="store_receipt.php">
        <label for="shopName">Shop Name:</label>
        <input type="text" id="shopName" name="shop_name" required><br><br>
        
        <label for="receiptDate">Date:</label>
        <input type="date" id="receiptDate" name="receipt_date" required><br><br>
        
        <label for="receiptTime">Time:</label>
        <input type="time" id="receiptTime" name="receipt_time" required><br><br>
        
        <table class='table table-bordered'>
            <thead>
                <tr> 
                    <th>Product</th>
                    <th>Price</th>
                    <th>Qty</th>
                    <th>Total</th>
                    <th>Add</th>
                    <th>Remove</th>
                </tr>
            </thead>
            <tbody id="tbl">
                <tr>
                    <td><select class='pid' name="product_id[]"><?php echo $products; ?></select></td>
                    <td><input class='price' type='text' name='price[]'></td>
                    <td><input class='qty' type='text' name='qty[]'></td>
                    <td><input class='total' type='text' name='total[]'></td>
                    <td><input type='button' value='+' class='add'></td>
                    <td><input type='button' value='-' class='rmv'></td>
                </tr>
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="3">Grand Total:</th>
                    <td><input id="grandTotal" type="text" name="grand_total" readonly></td>
                    <td colspan="2"></td>
                </tr>
            </tfoot>
        </table>

        <input type="submit" value="Generate Receipt">
    </form>

    <script>
        $(document).ready(function(){
            // Function to calculate grand total
            function calculateGrandTotal() {
                var grandTotal = 0;
                $(".total").each(function() {
                    var total = $(this).val();
                    if (!isNaN(total) && total !== '') {
                        grandTotal += parseFloat(total);
                    }
                });
                $("#grandTotal").val(grandTotal.toFixed(2));
            }

            // Event listener for Add button
            $("body").on("click", ".add", function(){
                var products = "<?php echo $products; ?>";
                $("#tbl").append("<tr> <td><select class='pid' name='product_id[]'>"+products+"</select></td> <td><input class='price' type='text' name='price[]'></td> <td><input class='qty' type='text' name='qty[]'></td> <td><input class='total' type='text' name='total[]'></td> <td><input type='button' value='+' class='add'></td> <td><input type='button' value='-' class='rmv'></td> </tr>");
            });

            // Event listener for Remove button
            $("body").on("click", ".rmv", function(){
                $(this).parents("tr").remove();
                calculateGrandTotal(); // Recalculate grand total after removing row
            });

            // Event listener for Product change
            $("body").on("change", ".pid", function(){
                var pid = $(this).val();
                var input = $(this).parents("tr").find(".price");
                $.ajax({
                    url: "get_price.php",
                    type: "post",
                    data: {pid: pid},
                    success: function(res){
                        $(input).val(res);
                        calculateGrandTotal(); // Recalculate grand total after price change
                    }
                });
            });

            // Event listener for Quantity change
            $("body").on("keyup", ".qty", function(){
                var qty = Number($(this).val());
                var price = Number($(this).parents("tr").find(".price").val());
                $(this).parents("tr").find(".total").val((qty * price).toFixed(2));
                calculateGrandTotal(); // Recalculate grand total after quantity change
            });

            // Calculate initial grand total on page load
            calculateGrandTotal();
        });
    </script>
</body>
</html>
