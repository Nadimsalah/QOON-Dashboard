<?php
require "conn.php";



$id = $_GET["id"];



$test=4;

	
	
	if($test==4){

   $sql="DELETE FROM Shops WHERE ShopID =$id";
   if(mysqli_query($con,$sql))
   {
	   
	   
	   
	
	
      $url = 'shop-profile.php?id='.$id;
      echo '<script>alert(" Done")</script>';
      echo '<script type="text/javascript">';
      echo 'window.location.href="'.$url.'";';
      echo '</script>';
      echo '<noscript>';
      echo '<meta http-equiv="refresh" content="0;url='.$url.'" />';
      echo '</noscript>'; exit;

   }
   else
   {

	
      $url = 'shop-profile.php?id='.$id;
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