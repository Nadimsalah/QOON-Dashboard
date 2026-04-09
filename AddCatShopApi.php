<?php
 require "conn.php";

$CategoryName = $_POST["CategoryName"];
$ShopID 	  = $_POST["ShopID"];



  $sql="INSERT INTO ShopsCategory (ShopID,CategoryName) VALUES ('$ShopID','$CategoryName')";
   



   
   if(mysqli_query($con,$sql))
   {
       
      

   
   
	
	/*$url = 'add-category-shop.php?id='.$ShopID;
      echo '<script>alert(" Done ")</script>';
      echo '<script type="text/javascript">';
      echo 'window.location.href="'.$url.'";';
      echo '</script>';
      echo '<noscript>';
      echo '<meta http-equiv="refresh" content="0;url='.$url.'" />';
      echo '</noscript>'; exit;*/
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

