<?php
 require "conn.php";

$CategoryId 	 = $_POST["CategoryId"];
$PercForOrder    = $_POST["PercForOrder"];

   


   $sql = "UPDATE Categories SET PercForOrder = '$PercForOrder' WHERE CategoryId = $CategoryId";

   if(mysqli_query($con,$sql)){

   }
   else
   {
 //
   }
die;
mysqli_close($con);

?>

