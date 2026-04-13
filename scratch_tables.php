<?php
$dbhost = "145.223.33.118";
$dbuser = "qoon_Qoon";
$dbpass = ";)xo6b(RE}K%";
$dbname = "qoon_Qoon";
$con = new mysqli($dbhost, $dbuser, $dbpass, $dbname);

$res = $con->query("SHOW TABLES");
$tables = [];
while($row = $res->fetch_row()) $tables[] = $row[0];
echo implode(", ", $tables);
?>
