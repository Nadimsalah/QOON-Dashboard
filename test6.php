<?php
require 'connlog.php';

echo "Drivers table:\n";
$res = mysqli_query($con, "SHOW COLUMNS FROM Drivers");
while($row = mysqli_fetch_assoc($res)) { echo $row['Field'] . " "; }

echo "\n\nShops table:\n";
$res = mysqli_query($con, "SHOW COLUMNS FROM Shops");
while($row = mysqli_fetch_assoc($res)) { echo $row['Field'] . " "; }
?>
