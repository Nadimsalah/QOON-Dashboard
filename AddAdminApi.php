<?php
 require "conn.php";

$AdminName 		= $_POST["AdminName"];
$Email 	   		= $_POST["Email"];
$Function  		= $_POST["Function"];
$Phone	   		= $_POST["Phone"];
$AdminPassword  = $_POST["AdminPassword"];

$AdminPassword  = password_hash($AdminPassword, PASSWORD_DEFAULT);

$Userspage        = $_POST["Userspage"];
$UserInformation  = $_POST["UserInformation"];
$DownloadUsersData  = $_POST["DownloadUsersData"];
$DriversPage  = $_POST["DriversPage"];
$AddNewDriver  = $_POST["AddNewDriver"];
$DriverProfile  = $_POST["DriverProfile"];
$ShopsPage  = $_POST["ShopsPage"];
$AddNewShop  = $_POST["AddNewShop"];
$ShopProfile  = $_POST["ShopProfile"];
$OrdersPage    = $_POST["OrdersPage"];
$OrderDetails  = $_POST["OrderDetails"];
$WalletPage  = $_POST["WalletPage"];
$AddSlides = $_POST["AddSlides"];
$ControleDistance = $_POST["ControleDistance"];
$Categores = $_POST["Categores"];
$Notification = $_POST["Notification"];
$Profile = $_POST["Profile"];
$Staffaccounts = $_POST["Staffaccounts"];
$blacklistr = $_POST["blacklistr"];
$Payments = $_POST["Payments"];

$all = $_POST["all"];


if( $Userspage == 'on'){
	$Userspage = 1;
}else{
	$Userspage = 0;
}
if( $UserInformation == 'on'){
	$UserInformation = 1;
}else{
	$UserInformation = 0;
}
if( $DownloadUsersData == 'on'){
	$DownloadUsersData = 1;
}else{
	$DownloadUsersData = 0;
}
if( $DriversPage == 'on'){
	$DriversPage = 1;
}else{
	$DriversPage = 0;
}
if( $AddNewDriver == 'on'){
	$AddNewDriver = 1;
}else{
	$AddNewDriver = 0;
}
if( $DriverProfile == 'on'){
	$DriverProfile = 1;
}else{
	$DriverProfile = 0;
}
if( $ShopsPage == 'on'){
	$ShopsPage = 1;
}else{
	$ShopsPage = 0;
}
if( $AddNewShop == 'on'){
	$AddNewShop = 1;
}else{
	$AddNewShop = 0;
}
if( $ShopProfile == 'on'){
	$ShopProfile = 1;
}else{
	$ShopProfile = 0;
}
if( $OrdersPage == 'on'){
	$OrdersPage = 1;
}else{
	$OrdersPage = 0;
}
if( $OrderDetails == 'on'){
	$OrderDetails = 1;
}else{
	$OrderDetails = 0;
}
if( $WalletPage == 'on'){
	$WalletPage = 1;
}else{
	$WalletPage = 0;
}
if( $AddSlides == 'on'){
	$AddSlides = 1;
}else{
	$AddSlides = 0;
}
if( $ControleDistance == 'on'){
	$ControleDistance = 1;
}else{
	$ControleDistance = 0;
}
if( $Categores == 'on'){
	$Categores = 1;
}else{
	$Categores = 0;
}
if( $Notification == 'on'){
	$Notification = 1;
}else{
	$Notification = 0;
}
if( $Profile == 'on'){
	$Profile = 1;
}else{
	$Profile = 0;
}
if( $Staffaccounts == 'on'){
	$Staffaccounts = 1;
}else{
	$Staffaccounts = 0;
}
if( $blacklistr == 'on'){
	$blacklistr = 1;
}else{
	$blacklistr = 0;
}
if( $Payments == 'on'){
	$Payments = 1;
}else{
	$Payments = 0;
}

if($all=='on'){
	
	$Userspage = 1;
	$UserInformation = 1;
	$DownloadUsersData = 1;
	$DriversPage = 1;
	$AddNewDriver = 1;
	$DriverProfile = 1;
	$ShopsPage = 1;
	$AddNewShop = 1;
	$OrdersPage = 1;
	$OrderDetails = 1;
	$WalletPage = 1;
	$AddSlides = 1;
	$ControleDistance = 1;
	$Categores = 1;
	$Notification = 1;
	$Profile = 1;
	$Staffaccounts = 1;
	$blacklistr = 1;
	$Payments = 1;
	
}
 
 


  $sql="INSERT INTO Admin (AdminName,AdminPassword,Functionn,Phone,Email,Userspage,UserInformation,DownloadUsersData,DriversPage,AddNewDriver,DriverProfile,ShopsPage,AddNewShop,ShopProfile,OrdersPage,OrderDetails,WalletPage,AddSlides,ControleDistance,Categores,Notification,Profile,Staffaccounts,blacklistr,Payments) VALUES ('$AdminName','$AdminPassword','$Function','$Phone','$Email',$Userspage,$UserInformation,$DownloadUsersData
  ,$DriversPage,$AddNewDriver,$DriverProfile,$ShopsPage,$AddNewShop,$ShopProfile,$OrdersPage,$OrderDetails,$WalletPage,$AddSlides,$ControleDistance,$Categores,$Notification,$Profile,
  $Staffaccounts,$blacklistr,$Payments)";
   



   
   if(mysqli_query($con,$sql))
   {
       
      

   
   
	
	$url = 'settings-staff-accounts.php';
      echo '<script>alert(" Done ")</script>';
      echo '<script type="text/javascript">';
      echo 'window.location.href="'.$url.'";';
      echo '</script>';
      echo '<noscript>';
      echo '<meta http-equiv="refresh" content="0;url='.$url.'" />';
      echo '</noscript>'; exit;
//	header("location: shop.php"); 
	
   

	
   }
   else
   {
 
	$url = 'settings-staff-accounts.php';
      echo '<script>alert(" خطأ ")</script>';
      echo '<script type="text/javascript">';
      echo 'window.location.href="'.$url.'";';
      echo '</script>';
      echo '<noscript>';
      echo '<meta http-equiv="refresh" content="0;url='.$url.'" />';
      echo '</noscript>'; exit;
   }
die;
mysqli_close($con);

?>

