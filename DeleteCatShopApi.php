<?php
 require "conn.php";

$id = $_GET["id"];
$ShopID 	  = $_GET["ShopID"];



  $sql="DELETE FROM ShopsCategory WHERE CategoryShopID= $id";
   



   
   if(mysqli_query($con,$sql))
   {
       
      

   
   
	
/*	$url = 'add-category-shop.php?id='.$ShopID;
      echo '<script>alert(" Done ")</script>';
      echo '<script type="text/javascript">';
      echo 'window.location.href="'.$url.'";';
      echo '</script>';
      echo '<noscript>';
      echo '<meta http-equiv="refresh" content="0;url='.$url.'" />';
      echo '</noscript>'; exit;  */
	header("location: add-category-shop.php?id=$ShopID"); 
	
   

	
   }
   else
   {
 
	$url = 'add-category-shop.php?id='.$ShopID;
      echo '<script>alert(" خطأ ")</script>';
      echo '<script type="text/javascript">';
      echo 'window.location.href="'.$url.'";';
      echo '</script>';
      echo '<noscript>';
      echo '<meta http-equiv="refresh" content="0;url='.$url.'" />';
      echo '</noscript>'; exit;
   }
die;
mysqli_close($con);

?>

