<?php
 require "conn.php";

$Arabic = $_POST["Arabic"];
$English = $_POST["English"];
$French = $_POST["French"];
$Priority = $_POST["Priority"];
$Type  = $_POST["Type"];
$Pro   = $_POST["Pro"];
$DeliveryZoneIDs = $_POST["DeliveryZoneID"];


  $Carphoto =  $_FILES["Photo"]["tmp_name"];

  $photo1name="w-".rand();
  $path = "db/db/photo/$photo1name.png";

 
  $actualpath = "https://jibler.app/$path";
 
  $path = "photo/$photo1name.png";
 

  $sql="INSERT INTO Categories (ArabCategory,EnglishCategory,FrenchCategory,Photo,priority,Type,Pro) VALUES
  ('$Arabic','$English','$French','$actualpath',$Priority,'$Type','$Pro')";
   

   
   if(mysqli_query($con,$sql))
   {
	   $ID =  $conn->insert_id;
	   
	   foreach ($DeliveryZoneIDs as $DeliveryZoneID){
            $sql="INSERT INTO CategoriesAndDeliveryZone (CategoryId,DeliveryZoneID) VALUES ('$ID','$DeliveryZoneID')";
			if(mysqli_query($con,$sql)){}
        }
	   

   $key['Result'] = "success";

   
   if (move_uploaded_file($_FILES["Photo"]["tmp_name"], $path)) {
        echo "The file ". basename( $_FILES["Photo"]["name"]). " has been uploaded.";
		header("location: https://jibler.app/db/db/apps.php"); 
    } else {
        echo "Sorry, there was an error uploading your file.";
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

