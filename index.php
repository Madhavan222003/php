<html>
  <head>
    <title>Get Product Price on Dynamically Added Row in PHP-MySQL</title>
    <script src="https://code.jquery.com/jquery-2.2.4.min.js" integrity="sha256-BbhdlvQf/xTY9gja0Dq3HiwQF8LaCRTXxZKRutelT44=" crossorigin="anonymous"></script>
    <style>
      table{
        border-collapse:collapse;
      }
      td,th{
        border:1px solid #ccc;
        padding:10px;
      }
    </style>
  </head>
  <?php 
    include "config.php";
    $sql="select pid,pname from products";
    $products="<option>Select</option>";
    $res=$con->query($sql);
    if($res->num_rows>0){
      while($row=$res->fetch_assoc()){
        $products.="<option value='{$row["pid"]}'>{$row["pname"]}</option>";
      }
    }
  ?>
  <body>
    <table class='table table-bordered' >
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
          <td><select class='pid'><?php echo $products; ?></select></td>
          <td><input class='price' type='text' name=''></td>
          <td><input class='qty' type='text' name=''></td>
          <td><input class='total' type='text' name=''></td>
          <td><input type='button' value='+' class='add' ></td>
          <td><input type='button' value='-' class='rmv'></td>
        </tr>
      </tbody>
    </table>
    <script>
      $(document).ready(function(){
        
        
        $("body").on("click",".add",function(){
          var products="<?php echo $products; ?>";
          $("#tbl").append("<tr> <td><select class='pid'>"+products+"</select></td> <td><input class='price' type='text' name=''></td> <td><input class='qty' type='text' name=''></td> <td><input class='total' type='text' name=''></td> <td><input type='button' value='+' class='add' ></td> <td><input type='button' value='-' class='rmv'></td> </tr>");
        });
        
        
        $("body").on("click",".rmv",function(){
          $(this).parents("tr").remove();
        });
        
        
        $("body").on("change",".pid",function(){
          var pid=$(this).val();
          var input=$(this).parents("tr").find(".price");
          $.ajax({
            url:"get_price.php",
            type:"post",
            data:{pid:pid},
            success:function(res){
              $(input).val(res);
            }
          });
        });
        
      
        $("body").on("keyup",".qty",function(){
          var qty=Number($(this).val());
          var price=Number($(this).parents("tr").find(".price").val());
          $(this).parents("tr").find(".total").val(qty*price);
        });
      });
    </script>
  </body>
</html>