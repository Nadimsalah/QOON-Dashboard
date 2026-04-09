<?php
 require "conn.php";

$Arabic = $_POST["Arabic"];
$English = $_POST["English"];
$French = $_POST["French"];
$ID = $_POST["ID"];
$Priority = $_POST["Priority"];
$Pro   = $_POST["Pro"]; 

$DeliveryZoneIDs = $_POST["DeliveryZoneID"];

  $Carphoto =  $_FILES["Photo"]["tmp_name"];

  $photo1name="w-".rand();

  $path = "db/db/photo/$photo1name.png";

 
  $actualpath = "https://jibler.app/$path";
 
  $path = "photo/$photo1name.png";
 
 
// echo $ID . ' ' . $Arabic  . ' ' . $English . ' ' . $French . ' ' . $Priority;
// die;

  $sql="INSERT INTO Categories (ArabCategory,EnglishCategory,FrenchCategory,Photo) VALUES
  ('$Arabic','$English','$French','$actualpath')";
  
  
  $photofound = "no";
 
  if($_FILES["Photo"]["tmp_name"]==""){
	  
  }else{
	  $photofound = "yes";
  }
  
//  echo $ID;
   

if($photofound == "yes"){
   $sql = "UPDATE Categories SET Pro = '$Pro', ArabCategory='$Arabic',EnglishCategory='$English',FrenchCategory='$French',Photo='$actualpath',priority=$Priority
    WHERE CategoryId=$ID";
}else{
    $sql = "UPDATE Categories SET Pro = '$Pro', ArabCategory='$Arabic',EnglishCategory='$English',FrenchCategory='$French',priority=$Priority
    WHERE CategoryId=$ID";
}
   if(mysqli_query($con,$sql)){
	   
	   $sql = "DELETE FROM CategoriesAndDeliveryZone WHERE CategoryId = $ID";
	   if(mysqli_query($con,$sql)){}
	   
	   
	    foreach ($DeliveryZoneIDs as $DeliveryZoneID){
            $sql="INSERT INTO CategoriesAndDeliveryZone (CategoryId,DeliveryZoneID) VALUES ('$ID','$DeliveryZoneID')";
			if(mysqli_query($con,$sql)){}
        }
	   

   $key['Result'] = "success";

   if($photofound == "yes"){
   if (move_uploaded_file($_FILES["Photo"]["tmp_name"], $path)) {
        echo "The file ". basename( $_FILES["Photo"]["name"]). " has been uploaded.";
		//header("location: https://jibler.ma/db/db/apps.php"); 
		
		$url = 'https://jibler.app/db/db/apps.php';
      echo '<script>alert(" Done ")</script>';
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
        		$url = 'https://jibler.app/db/db/apps.php';
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
   $key['Result'] = "Error";
           		$url = 'https://jibler.app/db/db/apps.php';
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

