<?php
 require "conn.php";

$FName = $_POST["FName"];
$LName = $_POST["LName"];
$DriverEmail = $_POST["DriverEmail"];
$DriverPhone = $_POST["DriverPhone"];
$AGE = $_POST["AGE"];
$CountryID = $_POST["CountryID"];
$City = $_POST["City"];
$Password = $_POST["Password"];

  $Carphoto =  $_FILES["PersonalPhoto"]["tmp_name"];

  $photo1name="w-".rand();

  $path = "/db/db/photo/$photo1name.png";

 
  $actualpath = "https://jibler.ma/$path";
 
  $path = "photo/$photo1name.png";
  
  $DriverID = $_POST["DriverID"];
  
 
 
//echo $t[0];

$CountryKey = $_POST["CountryKey"];

$DriverPhone = $CountryKey.$DriverPhone;




  $sql="Update Drivers set FName = '$FName', LName = '$LName', DriverEmail = '$DriverEmail', DriverPhone = '$DriverPhone',AGE = '$AGE',CountryID = '$CountryID',City = '$City',DriverPassword = '$Password' WHERE DriverID=$DriverID";
   



   
   if(mysqli_query($con,$sql))
   {
       
	   
	   if($_FILES["PersonalPhoto"]["tmp_name"]!=""){
	  $sql="Update Drivers set PersonalPhoto = '$actualpath' WHERE DriverID=$DriverID";

		   if(mysqli_query($con,$sql))
		   {
			    if (move_uploaded_file($_FILES["PersonalPhoto"]["tmp_name"], $path)) {}
			   
		   }
	  }else{
		  
	  }
	   
	   
	   $url = 'driver-profile.php?id='.$DriverID;
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

	
  
 //  echo "UserCode used before";
  
die;
mysqli_close($con);

?>

