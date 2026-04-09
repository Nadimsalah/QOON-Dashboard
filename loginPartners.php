<?php

require "connlog.php";

$userlogin = $_POST["userlogin"];
$Password = $_POST["Password"];


$res = mysqli_query($con,"SELECT * FROM Shops WHERE ShopLogName='$userlogin' AND ShopPassword ='$Password'");



while($row = mysqli_fetch_assoc($res)){
$test = 4;

echo 'found';



}

if($test!=4){
	echo 'not found';
}

die;
mysqli_close($con);
?>