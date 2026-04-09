<?php
 require "conn.php";

$dis = $_POST["dis"];
$disUser  = $_POST["disUser"];

   


   $sql = "UPDATE OrdersJiblerpercentage SET percent = '$dis',disUser='$disUser'";

   if(mysqli_query($con,$sql)){


  
		
	  $url = 'apps.php';
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
 //  echo "UserCode used before";
     $url = 'apps.php';
      echo '<script>alert(" Error ")</script>';
      echo '<script type="text/javascript">';
      echo 'window.location.href="'.$url.'";';
      echo '</script>';
      echo '<noscript>';
      echo '<meta http-equiv="refresh" content="0;url='.$url.'" />';
      echo '</noscript>'; exit;

	echo json_encode($key);
   }
die;
mysqli_close($con);

?>

