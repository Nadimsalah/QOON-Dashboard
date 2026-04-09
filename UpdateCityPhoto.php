<?php
 require "conn.php";

$id = $_POST["id"];


// echo $ShopID . ' shop ' . $FoodID . 'food;';
// die;


  $Carphoto =  $_FILES["Photo"]["tmp_name"];

  $photo1name="w-".rand();

  $path = "db/db/photo/$photo1name.png";

 
  $actualpath = "https://www.jibler.app/$path";
 
  $path = "photo/$photo1name.png";
 
  if($_FILES["Photo"]["tmp_name"]==""){
	  
  }else{
	  $photofound = "yes";
  }
  
//  echo $ID;
   

   $sql = "UPDATE DeliveryZone SET Photo='$actualpath' WHERE DeliveryZoneID=$id";

   if(mysqli_query($con,$sql)){


  
   if (move_uploaded_file($_FILES["Photo"]["tmp_name"], $path)) {
      //  echo "The file ". basename( $_FILES["Photo"]["name"]). " has been uploaded.";
		
	  $url = 'settings-delivery-zone.php';
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

