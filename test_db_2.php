<?php
$dbhost = "145.223.33.118";
$dbuser = "qoon_Qoon";
$dbpass = ";)xo6b(RE}K%";
$dbname = "qoon_Qoon";
$con = new mysqli($dbhost, $dbuser, $dbpass, $dbname);

echo "--- Last 5 Orders (any state) ---\n";
$res = $con->query("SELECT OrderID, UserName, UserPhone, UserEmail, OrderPrice, OrderState FROM Orders ORDER BY OrderID DESC LIMIT 5");
while($row = $res->fetch_assoc()) {
    echo "ID: " . $row['OrderID'] . " | Name: [" . $row['UserName'] . "] | Email: [" . $row['UserEmail'] . "] | Status: [" . $row['OrderState'] . "]\n";
}

echo "\n--- Last 5 Completed Orders ---\n";
$res = $con->query("SELECT OrderID, UserName, UserPhone, UserEmail, OrderPrice, OrderState FROM Orders WHERE OrderState = 'Done' ORDER BY OrderID DESC LIMIT 5");
while($row = $res->fetch_assoc()) {
    echo "ID: " . $row['OrderID'] . " | Name: [" . $row['UserName'] . "] | Email: [" . $row['UserEmail'] . "] | Status: [" . $row['OrderState'] . "]\n";
}
?>
