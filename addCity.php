<?php
 require "conn.php";

$CityName 		= $_POST["CityName"];
$Coordinates 	= $_POST["Coordinates"];
$Deliveryzone  	= $_POST["Deliveryzone"];
$CountryID      = $_POST["CountryID"];


  $t = explode(",",$Coordinates);
 


  $sql="INSERT INTO DeliveryZone (CountryID,CityName,CityLat,CityLongt,Deliveryzone) VALUES ('$CountryID','$CityName','$t[0]','$t[1]','$Deliveryzone')";
   



   
   if(mysqli_query($con,$sql))
   {
       
      

   
   
	
	$url = 'settings-delivery-zone.php';
      echo '<script>alert(" Done ")</script>';
      echo '<script type="text/javascript">';
      echo 'window.location.href="'.$url.'";';
      echo '</script>';
      echo '<noscript>';
      echo '<meta http-equiv="refresh" content="0;url='.$url.'" />';
      echo '</noscript>'; exit;
//	header("location: shop.php"); 
	
   

	
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
die;
mysqli_close($con);

?>

