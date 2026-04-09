<?php
 require "conn.php";

$BoostPricesID = $_GET["BoostPricesID"];
$BoostPricesStatus = $_GET["BoostPricesStatus"];





   $sql = "UPDATE BoostPrices SET BoostPricesStatus = '$BoostPricesStatus' WHERE BoostPricesID=$BoostPricesID";

   if(mysqli_query($con,$sql)){


  
	/*	
	  $url = 'apps.php';
      echo '<script>alert(" Done ")</script>';
      echo '<script type="text/javascript">';
      echo 'window.location.href="'.$url.'";';
      echo '</script>';
      echo '<noscript>';
      echo '<meta http-equiv="refresh" content="0;url='.$url.'" />';
      echo '</noscript>'; exit;
		
		
		*/
    
      header("location: ControlBoostPrices.php");	 
   }
   else
   {
 //  echo "UserCode used before";
 /*
      $url = 'apps.php';
      echo '<script>alert(" Error ")</script>';
      echo '<script type="text/javascript">';
      echo 'window.location.href="'.$url.'";';
      echo '</script>';
      echo '<noscript>';
      echo '<meta http-equiv="refresh" content="0;url='.$url.'" />';
      echo '</noscript>'; exit;

	echo json_encode($key);
	
	*/
	 header("location: ControlBoostPrices.php");
	
   }
die;
mysqli_close($con);

?>

