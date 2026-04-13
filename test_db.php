<?php
$dbhost = "145.223.33.118";
$dbuser = "qoon_Qoon";
$dbpass = ";)xo6b(RE}K%";
$dbname = "qoon_Qoon";
$con = new mysqli($dbhost, $dbuser, $dbpass, $dbname);

$res = $con->query("SELECT OrderID, UserName, UserPhone, UserAddress, OrderPrice, OrderState FROM Orders ORDER BY OrderID DESC LIMIT 10");
while($row = $res->fetch_assoc()) {
    echo "ID: " . $row['OrderID'] . " | Name: [" . $row['UserName'] . "] | Phone: " . $row['UserPhone'] . " | State: " . $row['OrderState'] . "\n";
}
?>
