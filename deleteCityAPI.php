<?php
require "conn.php";



$id = $_GET["id"];



$test=4;

	
	
	if($test==4){

   $sql="DELETE FROM DeliveryZone WHERE DeliveryZoneID =$id";
   if(mysqli_query($con,$sql))
   {
	   
	   
	   
	
	
      $url = 'settings-delivery-zone.php';
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

	
      $url = 'settings-delivery-zone.php';
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