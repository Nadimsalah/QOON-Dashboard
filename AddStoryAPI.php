<?php
 require "conn.php";


$Priority = $_POST["Priority"];
$ShopID = $_POST["ShopID"];

  $Carphoto =  $_FILES["Photo"]["tmp_name"];

  $photo1name="w-".rand();

  $path = "jibler/admin/jbler/photo/$photo1name.png";

 
  $actualpath = "https://sae-marketing.com/$path";
 
  $path = "photo/$photo1name.png";
 

  $sql="INSERT INTO Story (StoryPhoto,Periority,ShopID) VALUES
  ('$actualpath','$Priority','$ShopID')";
   

   
   if(mysqli_query($con,$sql))
   {

   $key['Result'] = "success";

   
   if (move_uploaded_file($_FILES["Photo"]["tmp_name"], $path)) {
        echo "The file ". basename( $_FILES["Photo"]["name"]). " has been uploaded.";
		header("location: https://sae-marketing.com/jibler/admin/jbler/Stories.php"); 
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

