<?php

require "connlog.php";

$pass="a";
$First = $_POST["Email"] ?? "";
$Second = $_POST["Password"] ?? "";

$AdminID    = "";
$AdminName  = "";

$test=0;
if (isset($con) && $con) {
    $res = mysqli_query($con, "SELECT * FROM Admin WHERE AdminName='$First'");
} else {
    die("Database connection error");
}

$result = array();





while($row = mysqli_fetch_assoc($res)){


$test=4;


$AdminPassword = $row["AdminPassword"];
//echo $AdminPassword;
//echo '<br/>';
//echo $Second;

 
if (password_verify($Second, $AdminPassword)) {
    $test=4;
  
	//echo 'Not';	

} else {
    $test=0;
	
	//echo 'here';
	
}

//die;

$AdminID    = $row["AdminID"];
$AdminName  = $row["AdminName"];



$Userspage        = $row["Userspage"];
$UserInformation  = $row["UserInformation"];
$DownloadUsersData  = $row["DownloadUsersData"];
$DriversPage  = $row["DriversPage"];
$AddNewDriver  = $row["AddNewDriver"];
$DriverProfile  = $row["DriverProfile"];
$ShopsPage  = $row["ShopsPage"];
$AddNewShop  = $row["AddNewShop"];
$ShopProfile  = $row["ShopProfile"];
$OrdersPage    = $row["OrdersPage"];
$OrderDetails  = $row["OrderDetails"];
$WalletPage  = $row["WalletPage"];
$AddSlides = $row["AddSlides"];
$ControleDistance = $row["ControleDistance"];
$Categores = $row["Categores"];
$Notification = $row["Notification"];
$Profile = $row["Profile"];
$Staffaccounts = $row["Staffaccounts"];
$blacklistr = $row["blacklistr"];
$Payments = $row["Payments"];
}
/////////////
//echo json_encode(array("result"=>$result));
if($test==4){
    
   // session_start();
    // $_SESSION["Emailjibler"] = 'loged';
    // $_SESSION["Passwordjibler"] = $Second;
    
    // $_SESSION["AdminID"] = $AdminID;
    // $_SESSION["AdminName"] = $AdminName;
        
        
    setcookie("Emailjibler", 'loged', time() + (86400 * 30), "/"); // 86400 = 1 day
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

//die;
    
        
  //   echo $_SESSION["Emailjibler"] ;;    
     //    die;
    header("location: index.php");  
    
}
else{
//	'herewww';
//	die;
	header("location: login.html"); 
}
die;
mysqli_close($con);
?>