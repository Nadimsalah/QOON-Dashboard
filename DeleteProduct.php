<?php
require "conn.php";


$ProdId = $_GET["ProdId"];
$ShopID = $_GET["shopid"];



$test=4;

	
	
	if($test==4){

   $sql="DELETE FROM Foods WHERE FoodID =$ProdId";
   if(mysqli_query($con,$sql))
   {
	   
	   
	   
	
	
      $url = 'products.php?id='.$ShopID;
      echo '<script>alert(" تم بنجاح ")</script>';
      echo '<script type="text/javascript">';
      echo 'window.location.href="'.$url.'";';
      echo '</script>';
      echo '<noscript>';
      echo '<meta http-equiv="refresh" content="0;url='.$url.'" />';
      echo '</noscript>'; exit;

   }
   else
   {

	
      $url = 'products.php?id='.$ShopID;
      echo '<script>alert(" خطأ ")</script>';
      echo '<script type="text/javascript">';
      echo 'window.location.href="'.$url.'";';
      echo '</script>';
      echo '<noscript>';
      echo '<meta http-equiv="refresh" content="0;url='.$url.'" />';
      echo '</noscript>'; exit;

   }
   
	}
die;
mysqli_close($con);
?>