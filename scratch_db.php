<?php
$dbhost = "145.223.33.118";
$dbuser = "qoon_Qoon";
$dbpass = ";)xo6b(RE}K%";
$dbname = "qoon_Qoon";
$con = new mysqli($dbhost, $dbuser, $dbpass, $dbname);

$res = mysqli_query($con, "SELECT COUNT(*) as c FROM Orders");
echo "Count Orders: " . mysqli_fetch_assoc($res)['c'] . "\n";

$res = mysqli_query($con, "SELECT COUNT(DISTINCT UserID) as c FROM Orders");
echo "Dist User Orders: " . mysqli_fetch_assoc($res)['c'] . "\n";

$res = mysqli_query($con, "SELECT COUNT(*) as c FROM Users");
echo "Total Users: " . mysqli_fetch_assoc($res)['c'] . "\n";
?>
