<?php
$dbhost = "145.223.33.118";
$dbuser = "qoon_Qoon";
$dbpass = ";)xo6b(RE}K%";
$dbname = "qoon_Qoon";
$con = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);

$res = mysqli_query($con, "DESCRIBE Shops");
$cols = []; while($row = mysqli_fetch_assoc($res)) { $cols[] = $row['Field']; }
echo "Shops: " . implode(', ', $cols) . "\n";

$res = mysqli_query($con, "DESCRIBE Drivers");
$cols = []; while($row = mysqli_fetch_assoc($res)) { $cols[] = $row['Field']; }
echo "Drivers: " . implode(', ', $cols) . "\n";
?>
