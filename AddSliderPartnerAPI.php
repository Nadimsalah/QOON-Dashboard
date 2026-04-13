<?php
 require "conn.php";




$SliderPosition = $_POST["position"];
$SelectType     = $_POST["SelectType"];
$Carphoto       =  $_FILES["Photo"]["tmp_name"];
$OpenNow        = $_POST["OpenNow"];
$OpenType        = $_POST["OpenType"];



  $photo1name="w-".rand();

  $path = "photo/$photo1name.png";

  $t = explode(",",$SliderPosition);
 
  $actualpath = "https://qoon.app/$path";
 
  $path = "photo/$photo1name.png";
 

  $sql="INSERT INTO SliderPartner (SliderPhoto,SliderLat,SliderLongt,DefaultPhoto,OpenNow,OpenType) VALUES
  ('$actualpath',$t[0],$t[1],'$SelectType','$OpenNow','$OpenType')";
   

   
   if(mysqli_query($con,$sql))
   {

   $key['Result'] = "success";

   
   if (move_uploaded_file($_FILES["Photo"]["tmp_name"], $path)) {
        echo "The file ". basename( $_FILES["Photo"]["name"]). " has been uploaded.";
		header("location: apps.php#tabs-2"); 
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

