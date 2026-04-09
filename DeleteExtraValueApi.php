<?php
require "conn.php";


$id = $_GET["id"];
$prodid = $_GET["prodid"];



$test=4;

	
	
	if($test==4){

   $sql="DELETE FROM `ExtraInSideCategoty WHERE ExtraInSideCategotyID =$id";
   if(mysqli_query($con,$sql))
   {
	   
	   
	   
	
	header("location: ControlExtraValues.php?id=$prodid");

   }
   else
   {

	header("location: ControlExtraValues.php?id=$prodid");

   }
   
	}
die;
mysqli_close($con);
?>