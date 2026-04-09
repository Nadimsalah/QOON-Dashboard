<?php
require "conn.php";
$test=0;

$res = mysqli_query($con,"SELECT * FROM Admin");
while($row = mysqli_fetch_assoc($res)){
	$AdminPassword  = $row["AdminPassword"];
	$AdminID  = $row["AdminID"];
	$CurrentPassHashed = password_hash($AdminPassword, PASSWORD_DEFAULT);
	echo $CurrentPassHashed;
	echo '<br/>';
	
	
	$sql22="UPDATE Admin SET AdminPassword='$CurrentPassHashed' WHERE AdminID=$AdminID";
	if(mysqli_query($con,$sql22))
	{}
	
	
}
//$res = mysqli_query($con,"SELECT * FROM Admin");

//while($row = mysqli_fetch_assoc($res)){

//$data = $row[0];


//$AdminPassword  = $row["AdminPassword"];
//$AdminID  = $row["AdminID"]

//$CurrentPassHashed = password_hash($AdminPassword, PASSWORD_DEFAULT);

/*
$sql22="UPDATE Admin SET AdminPassword='$CurrentPassHashed' WHERE AdminID=$AdminID";
					
		
		

	if(mysqli_query($con,$sql22))
	{}
		
*/

///}


die;
mysqli_close($con);
?>