<?php
 require "conn.php";

$ExtraCategoryName = $_POST["ExtraCategoryName"];
$prodid 	  = $_POST["prodid"];



  $sql="INSERT INTO ExtraCategory (ProductID,ExtraCategoryName) VALUES ('$prodid','$ExtraCategoryName')";
   



   
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
	header("location: controlExtra.php?prodid=$prodid"); 
	
   

	
   }
   else
   {
 
	$url = 'controlExtra.php?prodid='.$prodid;
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

