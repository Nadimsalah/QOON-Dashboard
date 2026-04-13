<?php
 require "conn.php";

$ProdName = $_POST["ProdName"];
$CategoryID = $_POST["CategoryID"];
$Price = $_POST["Price"];
$OfferPrice = $_POST["OfferPrice"];
$Description = $_POST["Description"];
$Extraone = "#";
$Extratwo = "#";
$Extraoneprice = "#";
$Extratwoprice = "#";
$ShopID = $_POST["ShopID"];



  $Carphoto =  $_FILES["Photo"]["tmp_name"];

  $photo1name="w-".rand();

  $path = "photo/$photo1name.png";

 
  $actualpath = "https://qoon.app/$path";
 
 
 $t = explode(",",$ShopLatPosition);
//echo $t[0];
 

  $sql="INSERT INTO Foods (FoodName,FoodCatID,FoodPrice,FoodOfferPrice,FoodDesc,Extraone,ExtraPriceOne,Extratwo,ExtraPriceTwo,FoodPhoto) VALUES
  ('$ProdName','$CategoryID','$OfferPrice','$Price','$Description','$Extraone','$Extraoneprice','$Extratwo','$Extratwoprice','$actualpath')";
   

   
   if(mysqli_query($con,$sql))
   {

   $key['Result'] = "success";

   
   if (move_uploaded_file($_FILES["Photo"]["tmp_name"], $path)) {


      $url = 'products.php?id='.$ShopID;
      echo '<script>alert(" تم بنجاح ")</script>';
      echo '<script type="text/javascript">';
      echo 'window.location.href="'.$url.'";';
      echo '</script>';
      echo '<noscript>';
      echo '<meta http-equiv="refresh" content="0;url='.$url.'" />';
      echo '</noscript>'; exit;


    } else {
      $url = 'products.php?id='.$ShopID;
      echo '<script>alert(" Done ")</script>';
      echo '<script type="text/javascript">';
      echo 'window.location.href="'.$url.'";';
      echo '</script>';
      echo '<noscript>';
      echo '<meta http-equiv="refresh" content="0;url='.$url.'" />';
      echo '</noscript>'; exit;
    }

	
	echo json_encode($key);
   }
   else
   {
 //  echo "UserCode used before";
   $key['Result'] = "UserCode used before";
	echo json_encode($key);
   }
die;
mysqli_close($con);

?>

