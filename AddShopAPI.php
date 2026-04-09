<?php
 require "conn.php";

$ShopName = $_POST["ShopName"];
$ShopPhone = $_POST["ShopPhone"];
$ShopLoginName = $_POST["ShopLoginName"];
$ShopLoginPassword = $_POST["ShopLoginPassword"];
$ShopLatPosition = $_POST["ShopLatPosition"];
$ShopLongtPosition = $_POST["ShopLongtPosition"];
$CategoryID = $_POST["CategoryID"];
$Type = $_POST["Type"];
$CityID = $_POST["CityID"];

  $Carphoto =  $_FILES["Photo"]["tmp_name"];

  $photo1name="w-".rand();

  $path = "db/db/photo/$photo1name.png";

 
  $actualpath = "https://jibler.ma/$path";
 
  $path = "photo/$photo1name.png";
  
  
  $Carphoto =  $_FILES["Photo2"]["tmp_name"];

  $photo2name="w-".rand();

  $path2 = "db/db/photo/$photo2name.png";

 
  $actualpath2 = "https://jibler.ma/$path2";
 
  $path2 = "photo/$photo2name.png";
 
 
  $Lat = "34.0209";
 $Longt = "-6.8416";
if($ShopLatPosition!=""){ 
 $t = explode(",",$ShopLatPosition);
 
 $Lat = $t[0];
 $Longt = $t[1];
}
//echo $t[0];

session_start();
$AdminID = $_SESSION["AdminID"] ;


if($AdminID==""){
    
    $AdminID = "1";
}

if($Type=="Ourplus"){
	  $Type = "Our";
	  $BakatID = "3"; 	  
}else if($Type=="Our"){
		  $Type = "Our";
	  $BakatID = "2"; 
	
}else{
	
	$BakatID = "1"; 
}



  $sql="INSERT INTO Shops (ShopName,ShopPhone,ShopLogName,ShopPassword,ShopLat,ShopLongt,ShopLogo,ShopCover,CategoryID,Type,AdminID,OwnerPhone,BakatID,CityID) VALUES
  ('$ShopName','$ShopPhone','$ShopLoginName','$ShopLoginPassword','$Lat','$Longt','$actualpath','$actualpath2','$CategoryID'
  ,'$Type','$AdminID','$ShopPhone','$BakatID','$CityID')";
   



   
   if(mysqli_query($con,$sql))
   {
       
       $last_id = $con->insert_id;
	   

$sql="INSERT INTO `ShopTimes` (`ShopID`, `Day`, `Times`) VALUES ('$last_id', 'Monday', '00:00-23:59');";
   if(mysqli_query($con,$sql))
   {}
$sql="INSERT INTO `ShopTimes` (`ShopID`, `Day`, `Times`) VALUES ('$last_id', 'Tuesday', '00:00-23:59');";
   if(mysqli_query($con,$sql))
   {}
$sql="INSERT INTO `ShopTimes` (`ShopID`, `Day`, `Times`) VALUES ('$last_id', 'Wednesday', '00:00-23:59');";
   if(mysqli_query($con,$sql))
   {}
$sql="INSERT INTO `ShopTimes` (`ShopID`, `Day`, `Times`) VALUES ('$last_id', 'Thursday', '00:00-23:59');";
   if(mysqli_query($con,$sql))
   {}
$sql="INSERT INTO `ShopTimes` (`ShopID`, `Day`, `Times`) VALUES ('$last_id', 'Friday', '00:00-23:59');";
   if(mysqli_query($con,$sql))
   {}
    $sql="INSERT INTO `ShopTimes` (`ShopID`, `Day`, `Times`) VALUES ('$last_id', 'Saturday', '00:00-23:59');";
   if(mysqli_query($con,$sql))
   {}
$sql="INSERT INTO `ShopTimes` ( `ShopID`, `Day`, `Times`) VALUES ('$last_id', 'Sunday', '00:00-23:59');";
   if(mysqli_query($con,$sql))
   {}
	   
	   

   $key['Result'] = "success";

   
   if (move_uploaded_file($_FILES["Photo"]["tmp_name"], $path)) {
        echo "The file ". basename( $_FILES["Photo"]["name"]). " has been uploaded.";
	//	header("location: https://sae-marketing.com/jibler/admin/jbler/ShopsMenu.php?ShopID=$last_id"); 
	
	if (move_uploaded_file($_FILES["Photo2"]["tmp_name"], $path2)) {}
	
	$url = 'shop.php';
      echo '<script>alert(" Done ")</script>';
      echo '<script type="text/javascript">';
      echo 'window.location.href="'.$url.'";';
      echo '</script>';
      echo '<noscript>';
      echo '<meta http-equiv="refresh" content="0;url='.$url.'" />';
      echo '</noscript>'; exit;
//	header("location: shop.php"); 
	
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

