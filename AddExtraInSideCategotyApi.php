<?php
 require "conn.php";

$Name = $_POST["Name"];
$Price = $_POST["Price"];
$ExtraCategoryID = $_POST["ExtraCategoryID"];



  $sql="INSERT INTO ExtraInSideCategoty (Name,Price,ExtraCategoryID) VALUES ('$Name','$Price','$ExtraCategoryID')";
   



   
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
	header("location: ControlExtraValues.php?id=$ExtraCategoryID"); 
	
   

	
   }
   else
   {
 
	$url = 'ControlExtraValues.php?id='.$ExtraCategoryID;
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

