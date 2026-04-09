<?php
 require "conn.php";

$MoneyStopNumber = $_POST["MoneyStopNumber"];
$subscription    = $_POST["subscription"];
$DriverCommesion = $_POST["DriverCommesion"];
$Type = $_POST["Type"];

   


   $sql = "UPDATE MoneyStop SET MoneyStopNumber = '$MoneyStopNumber' , subscription = '$subscription',DriverCommesion='$DriverCommesion'";

   if(mysqli_query($con,$sql)){


  
		
	  header("location: driver.php?notif=updated"); 
		
		
    
   }
   else
   {
 //  echo "UserCode used before";
     header("location: driver.php?notif=error"); 

   }
die;
mysqli_close($con);

?>

