<?php
require "conn.php";
$stmt = "SELECT ShopID, ShopName, ShopPhone, Balance, BakatID, CreatedAtShops FROM Shops LIMIT 1";
$res = mysqli_query($con, $stmt);
if (!$res) {
    echo "ERROR: " . mysqli_error($con);
} else {
    echo "SUCCESS\n";
    print_r(mysqli_fetch_assoc($res));
}
