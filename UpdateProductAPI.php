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
$ShopID = $_POST["shopid"];
$FoodID = $_POST["ProdId"];

// echo $ShopID . ' shop ' . $FoodID . 'food;';
// die;


  $Carphoto =  $_FILES["Photo"]["tmp_name"];

  $photo1name="w-".rand();

  $path = "photo/$photo1name.png";

 
  $actualpath = "https://qoon.app/$path";
 
  if($_FILES["Photo"]["tmp_name"]==""){
	  
  }else{
	  $photofound = "yes";
  }
  
//  echo $ID;
   

if($photofound == "yes"){
   $sql = "UPDATE Foods SET FoodName='$ProdName',FoodDesc='$Description',FoodPrice='$Price',FoodPhoto='$actualpath',FoodCatID='$CategoryID',FoodOfferPrice='$OfferPrice' WHERE FoodID=$FoodID";
}else{
    $sql = "UPDATE Foods SET FoodName='$ProdName',FoodDesc='$Description',FoodPrice='$Price',FoodCatID='$CategoryID',FoodOfferPrice='$OfferPrice' WHERE FoodID=$FoodID";
}
   if(mysqli_query($con,$sql)){

   $key['Result'] = "success";

   if($photofound == "yes"){
   if (move_uploaded_file($_FILES["Photo"]["tmp_name"], $path)) {
        echo "The file ". basename( $_FILES["Photo"]["name"]). " has been uploaded.";
		
	  $url = 'products.php?id='.$ShopID;
      echo '<script>alert(" تم بنجاح ")</script>';
      echo '<script type="text/javascript">';
      echo 'window.location.href="'.$url.'";';
      echo '</script>';
      echo '<noscript>';
      echo '<meta http-equiv="refresh" content="0;url='.$url.'" />';
      echo '</noscript>'; exit;
		
		
    } else {
        echo "Sorry, there was an error uploading your file.";
    }
    
    
    }else{
	  $url = 'products.php?id='.$ShopID;
      echo '<script>alert(" تم بنجاح ")</script>';
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
	  $url = 'products.php?id='.$ShopID;
      echo '<script>alert(" خطأ ")</script>';
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

