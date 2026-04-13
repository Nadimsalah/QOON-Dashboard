<?php
 require "conn.php";


$Priority = $_POST["Priority"];

$SliderPosition = $_POST["position"];
$SelectType     = $_POST["SelectType"];
$ProductID      = $_POST["ProductID"];
$ProductID      = $_POST["ProductID"];
$Carphoto       =  $_FILES["Photo"]["tmp_name"];
$OpenNow        = $_POST["OpenNow"];
$OpenType        = $_POST["OpenType"];

if($ProductID==""){
    
    $ProductID = 0;
}

  $photo1name="w-".rand();

  $path = "photo/$photo1name.png";

  $t = explode(",",$SliderPosition);
 
  $actualpath = "https://qoon.app/$path";
 
  $path = "photo/$photo1name.png";
 
 


  $sql="INSERT INTO Sliders (SliderPhoto,priority,SliderLat,SliderLongt,DefaultPhoto,ProductID,OpenNow,OpenType) VALUES
  ('$actualpath','$Priority',$t[0],$t[1],'$SelectType','$ProductID','$OpenNow','$OpenType')";
   

//	echo $sql;
	
	//die;

   
   if(mysqli_query($con,$sql))
   {

   $key['Result'] = "success";

   
   if (move_uploaded_file($_FILES["Photo"]["tmp_name"], $path)) {
        echo "The file ". basename( $_FILES["Photo"]["name"]). " has been uploaded.";
		header("location: apps.php"); 
    } else {
        echo "Sorry, there was an error uploading your file.";
    }

	
	echo json_encode($key);
   }
   else
   {
 //  echo "UserCode used before";
   $key['Result'] = "Error";
	echo json_encode($key);
   }
die;
mysqli_close($con);

?>

