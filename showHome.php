<?php
require "conn.php";

$shopID = isset($_POST["shopID"]) ? (int)$_POST["shopID"] : 0;
$inHome = isset($_POST["inHome"]) ? $_POST["inHome"] : "";

if($shopID > 0){
    $inHomeClean = mysqli_real_escape_string($con, $inHome);
    $sql = "UPDATE Shops SET InHome = '$inHomeClean' WHERE ShopID=$shopID";
    mysqli_query($con, $sql);
}

header("Location: shop-profile.php?id=" . $shopID);
exit();
?>
