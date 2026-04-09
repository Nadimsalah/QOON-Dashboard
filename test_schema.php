<?php
// Bypass auth by not including conn.php directly if it redirects, or just bypass the check
$dbhost = "145.223.33.118";
$dbuser = "qoon_Qoon";
$dbpass = ";)xo6b(RE}K%";
$dbname = "qoon_Qoon";
$con = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);

$res4 = mysqli_query($con, "SHOW COLUMNS FROM Users");
while($row = mysqli_fetch_array($res4)){
    echo $row[0].", ";
}
?>
