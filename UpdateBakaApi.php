<?php
 require "conn.php";

$BakatID 		= $_POST["BakatID"];



$DigitalStoreCreation        = $_POST["DigitalStoreCreation"];
$FullControlOfStore  = $_POST["FullControlOfStore"];
$AddMoreThanFiveProduct  = $_POST["AddMoreThanFiveProduct"];
$ReceiveOrder  = $_POST["ReceiveOrder"];
$TrackAndManageOrder  = $_POST["TrackAndManageOrder"];
$DeliveryServiceRequest  = $_POST["DeliveryServiceRequest"];
$JiblerPay  = $_POST["JiblerPay"];
$JiblerCard  = $_POST["JiblerCard"];
$WithdrawProfits  = $_POST["WithdrawProfits"];
$JiblerBoost    = $_POST["JiblerBoost"];
$BoostNowPayLater  = $_POST["BoostNowPayLater"];
$OrganicCEO  = $_POST["OrganicCEO"];
$StoriesPerMonth = $_POST["5StoriesPerMonth"];
$PublicationMonth = $_POST["5PublicationMonth"];
$InteractionWithCustomers = $_POST["InteractionWithCustomers"];
$Hosting = $_POST["Hosting"];




if( $DigitalStoreCreation == 'on'){
	$DigitalStoreCreation = "YES";
}else{
	$DigitalStoreCreation = "NO";
}
if( $FullControlOfStore == 'on'){
	$FullControlOfStore = 'YES';
}else{
	$FullControlOfStore = "NO";
}
if( $AddMoreThanFiveProduct == 'on'){
	$AddMoreThanFiveProduct = 'YES';
}else{
	$AddMoreThanFiveProduct = "NO";
}
if( $ReceiveOrder == 'on'){
	$ReceiveOrder = 'YES';
}else{
	$ReceiveOrder = 'NO';
}
if( $TrackAndManageOrder == 'on'){
	$TrackAndManageOrder = 'YES';
}else{
	$TrackAndManageOrder = 'NO';
}
if( $DeliveryServiceRequest == 'on'){
	$DeliveryServiceRequest = 'YES';
}else{
	$DeliveryServiceRequest = 'NO';
}
if( $JiblerPay == 'on'){
	$JiblerPay = 'YES';
}else{
	$JiblerPay = 'NO';
}
if( $JiblerCard == 'on'){
	$JiblerCard = 'YES';
}else{
	$JiblerCard = 'NO';
}
if( $WithdrawProfits == 'on'){
	$WithdrawProfits = 'YES';
}else{
	$WithdrawProfits = 'NO';
}
if( $JiblerBoost == 'on'){
	$JiblerBoost = 'YES';
}else{
	$JiblerBoost = 'NO';
}
if( $BoostNowPayLater == 'on'){
	$BoostNowPayLater = 'YES';
}else{
	$BoostNowPayLater = 'NO';
}
if( $OrganicCEO == 'on'){
	$OrganicCEO = 'YES';
}else{
	$OrganicCEO = 'NO';
}
if( $StoriesPerMonth == 'on'){
	$StoriesPerMonth = 'YES';
}else{
	$StoriesPerMonth = 'NO';
}
if( $PublicationMonth == 'on'){
	$PublicationMonth = 'YES';
}else{
	$PublicationMonth = 'NO';
}
if( $InteractionWithCustomers == 'on'){
	$InteractionWithCustomers = 'YES';
}else{
	$InteractionWithCustomers = 'NO';
}
if( $Hosting == 'on'){
	$Hosting = 'YES';
}else{
	$Hosting = 'NO';
}

 
 


  $sql="UPDATE Bakat SET DigitalStoreCreation = '$DigitalStoreCreation' , FullControlOfStore='$FullControlOfStore',AddMoreThanFiveProduct='$AddMoreThanFiveProduct' , ReceiveOrder = '$ReceiveOrder' , TrackAndManageOrder='$TrackAndManageOrder' , DeliveryServiceRequest = '$DeliveryServiceRequest',JiblerPay = '$JiblerPay' , JiblerCard = '$JiblerCard' , WithdrawProfits='$WithdrawProfits' , JiblerBoost = '$JiblerBoost' , BoostNowPayLater = '$BoostNowPayLater' , OrganicCEO = '$OrganicCEO' , 5StoriesPerMonth = '$StoriesPerMonth' , 5PublicationMonth = '$PublicationMonth' , InteractionWithCustomers = '$InteractionWithCustomers' , Hosting = '$Hosting' WHERE BakatID = $BakatID";
   



   
   if(mysqli_query($con,$sql))
   {
       
      

   
   
	
	$url = 'bakat.php';
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
 
	$url = 'bakat.php';
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

