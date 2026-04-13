<?php
$dbhost = "145.223.33.118";
$dbuser = "qoon_Qoon";
$dbpass = ";)xo6b(RE}K%";
$dbname = "qoon_Qoon";
$con = new mysqli($dbhost, $dbuser, $dbpass, $dbname);

echo "--- Shops ---\n";
$res = $con->query("DESCRIBE Shops");
while($row = $res->fetch_assoc()) echo $row['Field'] . ", ";
echo "\n\n--- Drivers ---\n";
$res = $con->query("DESCRIBE Drivers");
while($row = $res->fetch_assoc()) echo $row['Field'] . ", ";
echo "\n\n--- Products ---\n";
$res = $con->query("DESCRIBE Products");
while($row = $res->fetch_assoc()) echo $row['Field'] . ", ";
?>
