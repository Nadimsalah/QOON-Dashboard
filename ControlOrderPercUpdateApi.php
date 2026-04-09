<?php
 require "conn.php";

$Premium         = $_POST["Premium"];
$PremiumPlus     = $_POST["PremiumPlus"];
$DriverCommesion = $_POST["DriverCommesion"];
$MoneyStopNumber = $_POST["MoneyStopNumber"];
$subscription = $_POST["subscription"];
$SendMoneyPerc   = $_POST["SendMoneyPerc"];
$getMoneyPerc    = $_POST["getMoneyPerc"];
$disUser         = $_POST["disUser"];





  $sql="Update Bakat set Price = '$Premium' WHERE BakatID=2";
   if(mysqli_query($con,$sql))
   {
	   
	    $sql="Update Bakat set Price = '$PremiumPlus' WHERE BakatID=3";
		   if(mysqli_query($con,$sql))
		   {}
	   
	    
	   $sql="Update MoneyStop set DriverCommesion = '$DriverCommesion',MoneyStopNumber='$MoneyStopNumber',subscription='$subscription'";
		   if(mysqli_query($con,$sql))
		   {}
	   $sql="Update OrdersJiblerpercentage set SendMoneyPerc = '$SendMoneyPerc '";
		   if(mysqli_query($con,$sql))
		   {}
	   $sql="Update OrdersJiblerpercentage set getMoneyPerc = '$getMoneyPerc'";
		   if(mysqli_query($con,$sql))
		   {}
	   $sql="Update OrdersJiblerpercentage set disUser = '$disUser'";
		   if(mysqli_query($con,$sql))
		   {}
       
	  
	   
	   
	  $url = 'ControlOdersPerc.php';
      echo '<script type="text/javascript">';
      echo 'window.location.href="'.$url.'";';
      echo '</script>';
      echo '<noscript>';
      echo '<meta http-equiv="refresh" content="0;url='.$url.'" />';
      echo '</noscript>'; exit;
       

   
 
	
	
	
	
    } else {
        echo "Sorry, there was an error uploading your file.";
    }

	
  
 //  echo "UserCode used before";
  
die;
mysqli_close($con);

?>

