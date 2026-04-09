<?php
 require "conn.php";

$id = $_GET["id"];
$ShopID 	  = $_GET["CatID"];



  $sql="DELETE FROM KinzMadintySmallProducts WHERE KinzMadintySmallProductsID= $id";
   



   
   if(mysqli_query($con,$sql))
   {
       
      

   
   
	
/*	$url = 'add-category-shop.php?id='.$ShopID;
      echo '<script>alert(" Done ")</script>';
      echo '<script type="text/javascript">';
      echo 'window.location.href="'.$url.'";';
      echo '</script>';
      echo '<noscript>';
      echo '<meta http-equiv="refresh" content="0;url='.$url.'" />';
      echo '</noscript>'; exit;  */
	header("location: controlkinzcategory.php?id=$ShopID"); 
	
   

	
   }
   else
   {
 
	$url = 'controlkinzcategory.php?id='.$ShopID;
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

