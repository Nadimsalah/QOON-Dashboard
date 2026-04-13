<?php
$start = microtime(true);
$dbhost = "145.223.33.118";
$dbuser = "qoon_Qoon";
$dbpass = ";)xo6b(RE}K%";
$dbname = "qoon_Qoon";
$con = new mysqli($dbhost, $dbuser, $dbpass, $dbname);
echo "Connection: ".round(microtime(true)-$start, 4)."s\n";

function bench($sql) {
    global $con;
    $s = microtime(true);
    try { mysqli_query($con, $sql); } catch(\Throwable $e) {}
    echo "Query: ".round(microtime(true)-$s, 4)."s -> $sql\n";
}

bench("SELECT COUNT(*) FROM Users");
bench("SELECT COUNT(*) FROM Users WHERE CreatedAtUser >= DATE_SUB(NOW(), INTERVAL 7 DAY)");
bench("SELECT COUNT(*) FROM Orders");
bench("SELECT COUNT(*) FROM Orders WHERE CreatedAtOrders >= DATE_SUB(NOW(), INTERVAL 7 DAY)");
bench("SELECT COUNT(*) FROM Orders WHERE OrderState NOT IN ('Done','Rated','Cancelled')");
bench("SELECT IFNULL(SUM(OrderPrice),0) FROM Orders WHERE OrderState IN ('Done','Rated')");
bench("SELECT COUNT(*) FROM Drivers");
bench("SELECT IFNULL(SUM(OrderPriceFromShop),0) FROM Orders WHERE PaidForDriver='NotPaid'");
bench("SELECT COUNT(*) FROM Shops");
bench("SELECT CityName FROM DeliveryZone");

echo "Total: ".round(microtime(true)-$start, 4)."s\n";
