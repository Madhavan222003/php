<?php
  include "config.php";
  $sql="select price from products where pid={$_POST["pid"]}";
  $res=$con->query($sql);
  if($res->num_rows>0){
    $row=$res->fetch_assoc();
    echo $row["price"];
  }
  else{
    echo "0";
  }
?>