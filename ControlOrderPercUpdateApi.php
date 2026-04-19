<?php
 require "conn.php";

$Premium         = isset($_POST["Premium"]) ? floatval($_POST["Premium"]) : 0;
$PremiumPlus     = isset($_POST["PremiumPlus"]) ? floatval($_POST["PremiumPlus"]) : 0;
$DriverCommesion = isset($_POST["DriverCommesion"]) ? floatval($_POST["DriverCommesion"]) : 0;
$MoneyStopNumber = isset($_POST["MoneyStopNumber"]) ? floatval($_POST["MoneyStopNumber"]) : 0;
$subscription    = isset($_POST["subscription"]) ? floatval($_POST["subscription"]) : 0;
$SendMoneyPerc   = isset($_POST["SendMoneyPerc"]) ? floatval($_POST["SendMoneyPerc"]) : 0;
$getMoneyPerc    = isset($_POST["getMoneyPerc"]) ? floatval($_POST["getMoneyPerc"]) : 0;
$disUser         = isset($_POST["disUser"]) ? floatval($_POST["disUser"]) : 0;

  $sql="Update Bakat set Price = '$Premium' WHERE BakatID=2";
   if(mysqli_query($con,$sql))
   {
	   
	    $sql="Update Bakat set Price = '$PremiumPlus' WHERE BakatID=3";
		   if(mysqli_query($con,$sql))
		   {}
	   
	    
	   $sql="Update MoneyStop set DriverCommesion = '$DriverCommesion',MoneyStopNumber='$MoneyStopNumber',subscription='$subscription'";
		   if(mysqli_query($con,$sql))
		   {}
	   $sql="Update OrdersJiblerpercentage set SendMoneyPerc = '$SendMoneyPerc'";
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

