<?php
$dbhost = "145.223.33.118";
$dbuser = "qoon_Qoon";
$dbpass = ";)xo6b(RE}K%";
$dbname = "qoon_Qoon";
$con = new mysqli($dbhost, $dbuser, $dbpass, $dbname);

$where = "1=1";
$tableQuery = "SELECT ShopID, ShopName, ShopPhone, Balance, BakatID, CreatedAtShops FROM Shops WHERE $where ORDER BY ShopID DESC LIMIT 2";
$tableRes = mysqli_query($con, $tableQuery);

if (!$tableRes) {
    echo "ERROR: " . mysqli_error($con);
} else {
    echo "SUCCESS\n";
    print_r(mysqli_fetch_all($tableRes));
}
