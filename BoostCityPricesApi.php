<?php
 require "conn.php";

$DeliveryZoneID 	= $_POST["DeliveryZoneID"];
$BoostPrice = $_POST["BoostPrice"];
$Type		= $_POST["Type"];

	
  if($Type==""){	
	$sql="UPDATE DeliveryZone SET BoostPrice='$BoostPrice' WHERE DeliveryZoneID = $DeliveryZoneID";
  }else{
	$sql="UPDATE Countries SET BoostPrice='$BoostPrice' WHERE CountryID = $DeliveryZoneID";  
  }



   
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
	header("location: ControlCitiesPrices.php"); 
	
   

	
   }
   else
   {
 
	$url = 'ControlCitiesPrices.php?prodid='.$prodid;
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

