<?php
 require "conn.php";

$Notes = $_POST["Notes"];
$DriverID 	  = $_POST["DriverID"];



  $sql="INSERT INTO DriverNotes (Notes,DriverID) VALUES ('$Notes','$DriverID')";
   



   
   if(mysqli_query($con,$sql))
   {
       
      

   
   
	
	$url = 'driver-profile.php?id='.$DriverID;
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
 
	$url = 'driver-profile.php?id='.$DriverID;
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

