<?php

//require "connlog.php";

$pass="a";
$First = $_POST["Email"];
$Second = $_POST["Password"];

$AdminID    = "";
$AdminName  = "";




$test=4;

$AdminID    = 0;
$AdminName  = 0;



$Userspage        = 0;
$UserInformation  = 0;
$DownloadUsersData  = 0;
$DriversPage  = 0;
$AddNewDriver  = 0;
$DriverProfile  = 0;
$ShopsPage  = 0;
$AddNewShop  = 0;
$ShopProfile  = 0;
$OrdersPage    = 0;
$OrderDetails  = 0;
$WalletPage  = 0;
$AddSlides = 0;
$ControleDistance = 0;
$Categores = 0;
$Notification = 0;
$Profile = 0;
$Staffaccounts = 0;
$blacklistr = 0;
$Payments = 0;



/////////////
//echo json_encode(array("result"=>$result));
if($test==4){
        
    session_start();
    $_SESSION["Emailjibler"] = 'notloged';
    $_SESSION["Passwordjibler"] = 0;
    
    $_SESSION["AdminID"] = 0;
    $_SESSION["AdminName"] = 0;
        
        
    setcookie("Emailjibler", 'notloged', time() + (86400 * 30), "/"); // 86400 = 1 day
    setcookie("AdminID", $AdminID, time() + (86400 * 30), "/"); // 86400 = 1 day
    
    
    setcookie("Passwordjibler", $Second, time() + (86400 * 30), "/"); // 86400 = 1 day
    setcookie("AdminName", $AdminName, time() + (86400 * 30), "/"); // 86400 = 1 day
    
    
    setcookie("Userspage", $Userspage, time() + (86400 * 30), "/"); // 86400 = 1 day
    setcookie("UserInformation", $UserInformation, time() + (86400 * 30), "/"); // 86400 = 1 day
    setcookie("DownloadUsersData", $DownloadUsersData, time() + (86400 * 30), "/"); // 86400 = 1 day
    setcookie("DriversPage", $DriversPage, time() + (86400 * 30), "/"); // 86400 = 1 day
    setcookie("AddNewDriver", $AddNewDriver, time() + (86400 * 30), "/"); // 86400 = 1 day
    setcookie("DriverProfile", $DriverProfile, time() + (86400 * 30), "/"); // 86400 = 1 day
    setcookie("ShopsPage", $ShopsPage, time() + (86400 * 30), "/"); // 86400 = 1 day
    setcookie("AddNewShop", $AddNewShop, time() + (86400 * 30), "/"); // 86400 = 1 day
    setcookie("ShopProfile", $ShopProfile, time() + (86400 * 30), "/"); // 86400 = 1 day
    setcookie("OrdersPage", $OrdersPage, time() + (86400 * 30), "/"); // 86400 = 1 day
    setcookie("OrderDetails", $OrderDetails, time() + (86400 * 30), "/"); // 86400 = 1 day
    setcookie("WalletPage", $WalletPage, time() + (86400 * 30), "/"); // 86400 = 1 day
    setcookie("AddSlides", $AddSlides, time() + (86400 * 30), "/"); // 86400 = 1 day
    setcookie("ControleDistance", $ControleDistance, time() + (86400 * 30), "/"); // 86400 = 1 day
    setcookie("Categores", $Categores, time() + (86400 * 30), "/"); // 86400 = 1 day
    setcookie("Notification", $Notification, time() + (86400 * 30), "/"); // 86400 = 1 day

    setcookie("Profile", $Profile, time() + (86400 * 30), "/"); // 86400 = 1 day
    setcookie("Staffaccounts", $Staffaccounts, time() + (86400 * 30), "/"); // 86400 = 1 day
    setcookie("blacklistr", $blacklistr, time() + (86400 * 30), "/"); // 86400 = 1 day
    setcookie("Payments", $Payments, time() + (86400 * 30), "/"); // 86400 = 1 day


















    
        
    // echo $_SESSION["Emailjibler"] ;;    
    //     die;
    header("location: login.php");  
    
}
else{
	header("location: login.html"); 
}
die;
mysqli_close($con);
?>