<?php
require "conn.php";

$shopID = isset($_POST["shopID"]) ? (int)$_POST["shopID"] : 0;
$check = isset($_POST["check"]) ? $_POST["check"] : "";

$Status = ($check == "on") ? "NO" : "ACTIVE";

if($shopID > 0){
    $sql = "UPDATE Shops SET Status = '$Status' WHERE ShopID=$shopID";
    mysqli_query($con, $sql);
}

header("Location: shop-profile.php?id=" . $shopID);
exit();
?>
