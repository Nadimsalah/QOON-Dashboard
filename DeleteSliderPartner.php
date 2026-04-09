<?php
require "conn.php";


$SliderPartnerID = $_GET["SliderPartnerID"];



$test=4;

	
	
	if($test==4){
	    
	    

   $sql="Delete FROM SliderPartner WHERE SliderPartnerID =$SliderPartnerID";
   if(mysqli_query($con,$sql))
   {
	   
	    $url = 'apps.php';
        echo '<script>alert(" SliderPartner has been deleted successfully , we will direct you to Cities List page now :) ")</script>';
        echo '<script type="text/javascript">';
        echo 'window.location.href="'.$url.'";';
        echo '</script>';
        echo '<noscript>';
        echo '<meta http-equiv="refresh" content="0;url='.$url.'" />';
        echo '</noscript>'; exit; 
	   
	

   }
   else
   {

	    $url = 'apps.php';
        echo '<script>alert(" Error , Please Check your Network :) ")</script>';
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