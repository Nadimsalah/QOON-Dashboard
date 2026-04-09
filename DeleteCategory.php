<?php
require "conn.php";


$CategoryID = $_GET["CategoryId"];



$test=4;

	
	
	if($test==4){

   $sql="DELETE FROM Categories WHERE CategoryId =$CategoryID";
   if(mysqli_query($con,$sql))
   {
	   
	   
	   
	
	header("location: https://jibler.app/db/db/apps.php");

   }
   else
   {

	header("location: https://jibler.app/db/db/apps.php");

   }
   
	}
die;
mysqli_close($con);
?>