<?php
 require "conn.php";

$CategoryName = $_POST["CategoryName"];
$ShopID 	  = $_POST["ShopID"];



  $sql="INSERT INTO KinzMadintySmallProducts (CategoryId,KinzMadintySmallProductsName) VALUES ('$ShopID','$CategoryName')";
   



   
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
	header("location: controlkinzcategory.php?id=$ShopID"); 
	
   

	
   }
   else
   {
 
	$url = 'controlkinzcategory.php?id='.$ShopID;
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

