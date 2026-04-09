<?php
require 'connlog.php';

$res = mysqli_query($con, "SHOW COLUMNS FROM Money");
echo "Money table:\n";
while($row = mysqli_fetch_assoc($res)) {
    echo $row['Field'] . "\n";
}

$res = mysqli_query($con, "SELECT OrderPriceForOur FROM Orders LIMIT 5");
echo "\nOrders OrderPriceForOur:\n";
while($row = mysqli_fetch_assoc($res)) {
    echo $row['OrderPriceForOur'] . "\n";
}

$res = mysqli_query($con, "SELECT DriverOmolaPaid FROM Orders LIMIT 5");
echo "\nOrders DriverOmolaPaid:\n";
while($row = mysqli_fetch_assoc($res)) {
    echo $row['DriverOmolaPaid'] . "\n";
}
?>
