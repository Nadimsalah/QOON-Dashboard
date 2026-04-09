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

  $path = "db/db/photo/$photo1name.png";

 
  $actualpath = "https://jibler.app/$path";
 
  $path = "photo/$photo1name.png";
  
  
  
 
 
//echo $t[0];


$CountryKey = $_POST["CountryKey"];

$DriverPhone = $CountryKey.$DriverPhone;

  $sql="INSERT INTO Drivers (FName,LName,DriverEmail,DriverPhone,AGE,CountryID,City,DriverPassword,PersonalPhoto,Ckey) VALUES
  ('$FName','$LName','$DriverEmail','$DriverPhone','$AGE','$CountryID','$City','$Password','$actualpath','$CountryKey')";
   



   
   if(mysqli_query($con,$sql))
   {
       
       $last_id = $con->insert_id;
	   
	    $sql="INSERT INTO SubscriptionDriver (DriverID) VALUES('$last_id')";
		   if(mysqli_query($con,$sql))
		   {}

   $key['Result'] = "success";

   
   if (move_uploaded_file($_FILES["PersonalPhoto"]["tmp_name"], $path)) {
	//	header("location: https://sae-marketing.com/jibler/admin/jbler/ShopsMenu.php?ShopID=$last_id"); 
	
	
/////////////////////////////////////////////////////////////////	
	$Carphoto =  $_FILES["CIN"]["tmp_name"];

      $photo1name="w-".rand();
    
    
        $name = $_FILES["CIN"]["name"];
        $ext = end((explode(".", $name))); # extra () to prevent notice
    
      $path = "db/db/photo/$photo1name.$ext";
    
     
      $actualpath = "https://jibler.app/$path";
     
      $path = "photo/$photo1name.$ext";
    	
	$sql="Update Drivers Set CIN = '$actualpath' WHERE DriverID = $last_id";
   
   if(mysqli_query($con,$sql))
   {}
	
	if (move_uploaded_file($_FILES["CIN"]["tmp_name"], $path)) {}
	
	
	///////////////////////////////////////////////////////
	
	
	
	/////////////////////////////////////////////////////////////////	
	$Carphoto =  $_FILES["CV"]["tmp_name"];

      $photo1name="w-".rand();
    
    
        $name = $_FILES["CV"]["name"];
        $ext = end((explode(".", $name))); # extra () to prevent notice
    
      $path = "db/db/photo/$photo1name.$ext";
    
     
      $actualpath = "https://jibler.app/$path";
     
      $path = "photo/$photo1name.$ext";
    	
	$sql="Update Drivers Set CV = '$actualpath' WHERE DriverID = $last_id";
   
   if(mysqli_query($con,$sql))
   {}
	
	if (move_uploaded_file($_FILES["CV"]["tmp_name"], $path)) {}
	
	
	///////////////////////////////////////////////////////
	
	
		/////////////////////////////////////////////////////////////////	
	$Carphoto =  $_FILES["Contract"]["tmp_name"];

      $photo1name="w-".rand();
    
    
        $name = $_FILES["Contract"]["name"];
        $ext = end((explode(".", $name))); # extra () to prevent notice
    
      $path = "db/db/photo/$photo1name.$ext";
    
     
      $actualpath = "https://jibler.app/$path";
     
      $path = "photo/$photo1name.$ext";
    	
	$sql="Update Drivers Set Contract = '$actualpath' WHERE DriverID = $last_id";
   
   if(mysqli_query($con,$sql))
   {}
	
	if (move_uploaded_file($_FILES["Contract"]["tmp_name"], $path)) {}
	
	
	///////////////////////////////////////////////////////
	
	
		/////////////////////////////////////////////////////////////////	
	$Carphoto =  $_FILES["Cart-Ownership"]["tmp_name"];

      $photo1name="w-".rand();
    
    
        $name = $_FILES["Cart-Ownership"]["name"];
        $ext = end((explode(".", $name))); # extra () to prevent notice
    
      $path = "db/db/photo/$photo1name.$ext";
    
     
      $actualpath = "https://jibler.app/$path";
     
      $path = "photo/$photo1name.$ext";
    	
	$sql="Update Drivers Set CartOwnership = '$actualpath' WHERE DriverID = $last_id";
   
   if(mysqli_query($con,$sql))
   {}
	
	if (move_uploaded_file($_FILES["Cart-Ownership"]["tmp_name"], $path)) {}
	
	
	///////////////////////////////////////////////////////
	
	
		/////////////////////////////////////////////////////////////////	
	$Carphoto =  $_FILES["Insurance"]["tmp_name"];

      $photo1name="w-".rand();
    
    
        $name = $_FILES["Insurance"]["name"];
        $ext = end((explode(".", $name))); # extra () to prevent notice
    
      $path = "db/db/photo/$photo1name.$ext";
    
     
      $actualpath = "https://jibler.app/$path";
     
      $path = "photo/$photo1name.$ext";
    	
	$sql="Update Drivers Set Insurance = '$actualpath' WHERE DriverID = $last_id";
   
   if(mysqli_query($con,$sql))
   {}
	
	if (move_uploaded_file($_FILES["Insurance"]["tmp_name"], $path)) {}
	
	
	///////////////////////////////////////////////////////
	
	
	
	
	
	header("location: driver.php?notif=driver_added");
    exit;
	
    } else {
        header("location: driver.php?notif=error");
        exit;
    }
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

