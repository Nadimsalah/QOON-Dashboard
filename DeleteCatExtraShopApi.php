<?php
require "conn.php";


$id = $_GET["id"];
$prodid = $_GET["prodid"];



$test=4;

	
	
	if($test==4){

   $sql="DELETE FROM ExtraCategory WHERE ExtraCategoryID =$id";
   if(mysqli_query($con,$sql))
   {
	   
	   
	   
	
	header("location: controlExtra.php?prodid=$prodid");

   }
   else
   {

	header("location: controlExtra.php?prodid=$prodid");

   }
   
	}
die;
mysqli_close($con);
?>